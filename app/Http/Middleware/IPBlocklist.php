<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IPBlocklist
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $cacheKey = 'blocked_ip_' . $ip;
        $isBlocked = Cache::has($cacheKey);
        
        Log::info('IP Blocklist Check', [
            'ip' => $ip,
            'cache_key' => $cacheKey,
            'is_blocked' => $isBlocked
        ]);

        if ($isBlocked) {
            Log::warning('Blocked IP attempted access', ['ip' => $ip]);
            return response('Your IP address has been blocked due to suspicious activity.', 403);
        }

        return $next($request);
    }
} 