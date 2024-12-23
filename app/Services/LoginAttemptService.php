<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SecurityAlert;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Hash;

class LoginAttemptService
{
    private const MAX_ATTEMPTS = 5;
    private const VERIFICATION_THRESHOLD = 2;
    private const BLOCK_DURATION = 30;
    private const ATTEMPT_DECAY = 60;
    private const MAX_CODE_ATTEMPTS = 3;
    private const CODE_COOLDOWN = 1;

    private $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function recordFailedAttempt(string $ip): void
    {
        $attempts = Cache::get($this->getAttemptsKey($ip), 0) + 1;
        
        Log::channel('daily')->info('Login attempt recorded', [
            'ip' => $ip,
            'attempt_number' => $attempts,
            'max_attempts' => self::MAX_ATTEMPTS,
            'remaining_attempts' => self::MAX_ATTEMPTS - $attempts
        ]);

        Cache::put($this->getAttemptsKey($ip), $attempts, now()->addMinutes(self::ATTEMPT_DECAY));

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->blockIp($ip);
        }
    }

    public function handleFailedAttempt(User $user): bool
    {
        $attempts = Cache::get($this->getUserAttemptsKey($user->id), 0) + 1;
        Cache::put(
            $this->getUserAttemptsKey($user->id),
            $attempts,
            now()->addMinutes(self::ATTEMPT_DECAY)
        );

        Log::channel('daily')->warning('Failed login attempt for user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'attempts' => $attempts,
            'threshold' => self::VERIFICATION_THRESHOLD
        ]);

        return $attempts >= self::VERIFICATION_THRESHOLD;
    }

    public function isBlocked(string $ip): bool
    {
        $blocked = Cache::has($this->getBlockKey($ip));
        
        if ($blocked) {
            Log::channel('daily')->info('Blocked IP attempted access', [
                'ip' => $ip,
                'block_expires' => Cache::get($this->getBlockKey($ip))
            ]);
        }
        
        return $blocked;
    }

    public function blockIp(string $ip): void
    {
        $expiresAt = now()->addMinutes(self::BLOCK_DURATION);
        
        Cache::put(
            $this->getBlockKey($ip),
            $expiresAt,
            $expiresAt
        );

        Log::channel('daily')->warning('IP blocked', [
            'ip' => $ip,
            'duration' => self::BLOCK_DURATION . ' minutes',
            'expires_at' => $expiresAt->toDateTimeString()
        ]);
    }

    public function getAttemptsRemaining(string $ip): int
    {
        $attempts = Cache::get($this->getAttemptsKey($ip), 0);
        return max(self::MAX_ATTEMPTS - $attempts, 0);
    }

    public function clearFailedAttempts(User $user): void
    {
        Cache::forget($this->getUserAttemptsKey($user->id));
        Log::channel('daily')->info('Cleared failed attempts for user', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function canRequestVerificationCode(string $email): bool
    {
        return !RateLimiter::tooManyAttempts(
            $this->getCodeRequestKey($email),
            1
        );
    }

    public function recordVerificationCodeRequest(string $email): void
    {
        RateLimiter::hit(
            $this->getCodeRequestKey($email),
            60 * self::CODE_COOLDOWN
        );
    }

    public function canAttemptVerificationCode(string $email): bool
    {
        return !RateLimiter::tooManyAttempts(
            $this->getCodeAttemptKey($email),
            self::MAX_CODE_ATTEMPTS
        );
    }

    public function recordVerificationCodeAttempt(string $email): void
    {
        RateLimiter::hit($this->getCodeAttemptKey($email));
    }

    public function getCodeCooldownSeconds(string $email): int
    {
        return RateLimiter::availableIn($this->getCodeRequestKey($email));
    }

    public function getRemainingCodeAttempts(string $email): int
    {
        return RateLimiter::remaining(
            $this->getCodeAttemptKey($email),
            self::MAX_CODE_ATTEMPTS
        );
    }

    public function notifyFailedAttempts(User $user): void
    {
        if (Cache::get($this->getUserAttemptsKey($user->id), 0) === self::VERIFICATION_THRESHOLD) {
            $user->notify(new SecurityAlert(
                'Multiple Failed Login Attempts',
                'We detected multiple failed login attempts on your account. For security, we\'ve enabled additional verification.',
                'Review Account Activity',
                route('profile.security'),
                'warning'
            ));
        }
    }

    public function notifyNewDevice(User $user): void
    {
        $deviceInfo = [
            'device' => $this->agent->device(),
            'platform' => $this->agent->platform(),
            'browser' => $this->agent->browser(),
            'ip' => request()->ip(),
        ];

        $user->notify(new SecurityAlert(
            'New Device Login',
            "A new login was detected from:\nDevice: {$deviceInfo['device']}\nBrowser: {$deviceInfo['browser']}\nPlatform: {$deviceInfo['platform']}\nIP: {$deviceInfo['ip']}",
            'Review Active Sessions',
            route('profile.security'),
            'info'
        ));
    }

    public function notifyAccountBlocked(User $user): void
    {
        $user->notify(new SecurityAlert(
            'Account Temporarily Blocked',
            'Your account has been temporarily blocked due to multiple failed login attempts. This is a security measure to protect your account.',
            'Contact Support',
            route('contact'),
            'error'
        ));
    }

    public function isKnownDevice(User $user): bool
    {
        $deviceId = $this->generateDeviceId();
        $knownDevices = Cache::get("user.{$user->id}.known_devices", []);
        
        return in_array($deviceId, $knownDevices);
    }

    public function markDeviceAsKnown(User $user): void
    {
        $deviceId = $this->generateDeviceId();
        $knownDevices = Cache::get("user.{$user->id}.known_devices", []);
        
        if (!in_array($deviceId, $knownDevices)) {
            $knownDevices[] = $deviceId;
            Cache::put("user.{$user->id}.known_devices", $knownDevices, now()->addMonths(6));
        }
    }

    private function generateDeviceId(): string
    {
        $userAgent = request()->userAgent();
        $ip = request()->ip();
        $browser = $this->agent->browser();
        $platform = $this->agent->platform();
        
        return Hash::make($userAgent . $ip . $browser . $platform);
    }

    private function getAttemptsKey(string $ip): string
    {
        return "login_attempts_{$ip}";
    }

    private function getUserAttemptsKey(int $userId): string
    {
        return "user_login_attempts_{$userId}";
    }

    private function getBlockKey(string $ip): string
    {
        return "blocked_ip_{$ip}";
    }

    private function getCodeRequestKey(string $email): string
    {
        return 'verification_code_request:' . $email;
    }

    private function getCodeAttemptKey(string $email): string
    {
        return 'verification_code_attempt:' . $email;
    }
} 