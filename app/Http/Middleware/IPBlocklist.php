<?php

namespace App\Http\Middleware;

use App\Services\LoginAttemptService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IPBlocklist
{
    public function __construct(
        private LoginAttemptService $loginAttemptService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        
        if ($this->loginAttemptService->isBlocked($ip)) {
            return response('Your IP has been temporarily blocked due to multiple failed login attempts. Please try again later.', 403);
        }

        return $next($request);
    }
} 