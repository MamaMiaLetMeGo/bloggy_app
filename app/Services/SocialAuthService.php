<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function handleProviderCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::where('provider', $provider)
                       ->where('provider_id', $socialUser->getId())
                       ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => bcrypt(str()->random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            auth()->login($user, true);
            return $user;

        } catch (\Exception $e) {
            logger()->error('Social auth error: ' . $e->getMessage());
            throw $e;
        }
    }
} 