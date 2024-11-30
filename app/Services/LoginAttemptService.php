<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class LoginAttemptService
{
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 15; // minutes

    public function handleFailedAttempt(User $user)
    {
        $user->increment('failed_login_attempts');

        if ($user->failed_login_attempts >= self::MAX_ATTEMPTS) {
            $user->locked_at = Carbon::now();
            $user->save();
        }
    }

    public function resetAttempts(User $user)
    {
        $user->failed_login_attempts = 0;
        $user->locked_at = null;
        $user->save();
    }

    public function isLockedOut(User $user): bool
    {
        if (!$user->locked_at) {
            return false;
        }

        $lockoutEnds = Carbon::parse($user->locked_at)->addMinutes(self::LOCKOUT_DURATION);
        return Carbon::now()->lt($lockoutEnds);
    }
} 