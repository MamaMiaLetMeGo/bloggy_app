<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\LoginCodeMail;
use Illuminate\Support\Facades\Mail;

class LoginAttemptService
{
    public function handleFailedAttempt(User $user)
    {
        $user->increment('failed_login_attempts');

        if ($user->failed_login_attempts >= 3) {
            $code = Str::random(6);
            $user->update([
                'login_code' => $code,
                'login_code_expires_at' => now()->addHours(1),
            ]);

            Mail::to($user)->send(new LoginCodeMail($code));
            return true;
        }

        return false;
    }

    public function verifyLoginCode(User $user, string $code)
    {
        if ($user->login_code === $code && 
            $user->login_code_expires_at > now()) {
            
            $user->update([
                'failed_login_attempts' => 0,
                'login_code' => null,
                'login_code_expires_at' => null,
            ]);

            return true;
        }

        return false;
    }
} 