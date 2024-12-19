<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    public function __construct()
    {
        Log::info('TwoFactorAuthentication middleware constructed');
    }

    public function handle(Request $request, Closure $next): Response
    {
        Log::info('TwoFactorAuthentication middleware handling request', [
            'path' => $request->path(),
            'user' => auth()->user()?->email
        ]);

        $user = auth()->user();

        if ($user && 
            $user->two_factor_enabled && 
            !session()->has('2fa.confirmed') &&
            !$request->is('2fa*') && 
            !$request->is('logout')
        ) {
            // Redirect to 2FA challenge
            return redirect()->route('2fa.challenge');
        }

        return $next($request);
    }
}
