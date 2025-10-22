<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminOtpIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        // If the user is logged in as an admin...
        if (Auth::guard('admin')->check()) {
            // ...but hasn't completed the OTP step in this session.
            // if (!$request->session()->get('admin_otp_verified')) {
                
            //     // Log them out to force the process again
            //     Auth::guard('admin')->logout();
                
            //     // Redirect to their specific login URL
            //     $loginUrl = getWebConfig(name: 'admin_login_url');
            //     return redirect('/login/' . $loginUrl)->withErrors(['You must complete OTP verification.']);
            // }
        }

        return $next($request);
    }
}