<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleTraffic extends ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Run before routing, sessions and authentication, including for unknown URLs.
        // Health checks must remain available when an IP exhausts its allowance.
        if ($request->is('up')) {
            return $next($request);
        }

        return parent::handle($request, $next, 'traffic');
    }
}
