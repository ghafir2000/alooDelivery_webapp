<?php

namespace App\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        logger(' CustomerMiddleware starts');
        $user = Auth::guard('customer')->user();

        // Check 1: Is the account suspended?
        if (!$user->is_active) {
            auth('customer')->logout();
            Toastr::warning(translate('the_account_is_suspended'));
            return redirect()->route('customer.auth.login');
        }

        // Check 2: Is the account unverified?
        if (!$user->is_phone_verified && !$user->is_email_verified) {
            logger('CustomerMiddleware', [$user]);
            logger('CustomerMiddleware', [
                'identity' => base64_encode($user->phone ?? $user->email),
                'type' => base64_encode($user->phone ? 'phone_verification' : 'email_verification'),
            ]);
            // Allow access to verification pages to prevent redirect loops
            if ($request->routeIs('customer.auth.check-verification') || $request->routeIs('customer.auth.verify')) {
                return $next($request);
            }

            // Redirect all other requests to the verification page
            Toastr::info(translate('you_must_verify_your_account_before_you_can_continue'));
            return redirect()->route('customer.auth.check-verification', [
                'identity' => base64_encode($user->phone ?? $user->email),
                'type' => base64_encode($user->phone ? 'phone_verification' : 'email_verification'),
            ]);
        }

        // If active AND verified, let them pass.
        return $next($request);
    }
}