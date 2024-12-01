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
            Log::info('Redirecting to 2FA challenge', [
                'user' => $user->email,
                'two_factor_enabled' => $user->two_factor_enabled,
                'session_2fa' => session()->has('2fa.confirmed'),
                'path' => $request->path()
            ]);
            
            return redirect()->route('2fa.challenge');
        }

        return $next($request);
    }
}
