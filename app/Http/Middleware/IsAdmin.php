<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('IsAdmin middleware running', [
            'path' => $request->path(),
            'is_authenticated' => auth()->check(),
            'user' => auth()->check() ? [
                'email' => auth()->user()->email,
                'is_admin' => auth()->user()->is_admin,
            ] : null,
        ]);

        if (!auth()->check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        if (!auth()->user()->is_admin) {
            Log::warning('User not admin, access denied', [
                'user' => auth()->user()->email
            ]);
            abort(403, 'Unauthorized action.');
        }

        Log::info('Admin access granted', [
            'user' => auth()->user()->email
        ]);

        return $next($request);
    }
}
