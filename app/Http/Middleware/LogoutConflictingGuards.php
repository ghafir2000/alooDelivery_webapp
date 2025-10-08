<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutConflictingGuards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $intendedGuard  The guard that is ALLOWED for this section.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $intendedGuard)
    {
        $guards = array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            // If the current guard is NOT the one intended for this route,
            // and the user is logged in on that guard, log them out.
            if ($guard !== $intendedGuard && Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        return $next($request);
    }
}