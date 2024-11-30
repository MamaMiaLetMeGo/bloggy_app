<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    protected $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirect($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Unable to connect with ' . ucfirst($provider)]);
        }
    }

    public function callback($provider)
    {
        try {
            $this->socialAuthService->handleProviderCallback($provider);
            return redirect()->route('dashboard');
        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Unable to login with ' . ucfirst($provider)]);
        }
    }
}
