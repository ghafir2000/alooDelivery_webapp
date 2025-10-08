<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        // --- START: ADD THIS HARD-CODED SAFETY CHECK ---
        // This is a specific fix for the admin login problem.
        // It checks if the request is a POST request to the root-level 'login' URL.
        if ($guard === 'admin' && $request->isMethod('POST')) {
            logger('RedirectIfAuthenticated', [$guard], 'in post block');
            // If it is, we bypass ALL logic in this middleware and proceed.
            return $next($request);
        }
        // --- END: ADD THIS HARD-CODED SAFETY CHECK ---
        
        logger('RedirectIfAuthenticated', [$guard]); // Your logger will now only run for GET requests

        switch ($guard) {
            case 'admin':
                logger('RedirectIfAuthenticated', [$guard], 'in get admin block');
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard.index');
                }
                break;
            case 'seller':
                logger('RedirectIfAuthenticated', [$guard], 'in get seller block');
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('vendor.dashboard.index');
                }
                break;
            case 'customer':
                logger('RedirectIfAuthenticated', [$guard], 'in get customer block');
                if (Auth::guard($guard)->check()) {
                    // Keep the fix for customer verification
                    if ($request->routeIs('customer.auth.check-verification') || $request->routeIs('customer.auth.verify')) {
                        // Do nothing
                    } else {
                        return redirect()->route('home');
                    }
                }
                break;
            default:
                logger('RedirectIfAuthenticated', [$guard], 'in default block');
                if (Auth::guard($guard)->check()) {
                    return redirect('home');
                }
                break;
        }

        logger('RedirectIfAuthenticated', [$guard], 'in else block, next request');

        return $next($request);
    }
}