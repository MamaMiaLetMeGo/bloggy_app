<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoginAttemptService
{
    private const MAX_ATTEMPTS = 5;
    private const VERIFICATION_THRESHOLD = 2;
    private const BLOCK_DURATION = 90; // minutes
    private const ATTEMPT_DECAY = 60; // minutes

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
} 