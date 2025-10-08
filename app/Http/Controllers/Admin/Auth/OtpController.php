<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Repositories\PhoneOrEmailVerificationRepositoryInterface;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\Services\NotificationService; // <-- Add this import

class OtpController extends Controller
{
    // Inject both services in the constructor
    public function __construct(
        private readonly PhoneOrEmailVerificationRepositoryInterface $phoneOrEmailVerificationRepo,
        private readonly NotificationService $notificationService
    ){
        $this->middleware('guest:admin');
    }

    // This method remains the same
    public function showOtpForm(Request $request)
    {
        if (!$request->has('id')) {
            Toastr::error(translate('Invalid request. Please login again.'));
            return redirect('/login/' . getWebConfig(name: 'admin_login_url'));
        }
        $admin = Admin::find($request->id);
        if (!$admin) {
            Toastr::error(translate('Admin not found.'));
            return redirect('/login/' . getWebConfig(name: 'admin_login_url'));
        }
        return view('admin-views.auth.otp-verify', ['admin' => $admin]);
    }

    // This method remains the same
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:admins,id',
            'otp' => 'required|numeric',
        ]);
        $admin = Admin::findOrFail($request->id);
        $verification = $this->phoneOrEmailVerificationRepo->getFirstWhere([
            'phone_or_email' => $admin->phone,
            'token' => $request->otp,
        ]);
        if ($verification) {
            $this->phoneOrEmailVerificationRepo->delete(['phone_or_email' => $admin->phone]);
            $remember = session()->pull('admin_remember', false);
            Auth::guard('admin')->login($admin, $remember);
            $request->session()->put('admin_otp_verified', true);
            Toastr::success(translate('Login successful.'));
            return redirect()->intended(route('admin.dashboard.index'));
        }
        Toastr::error(translate('Invalid OTP. Please try again.'));
        return back()->withInput(['id' => $request->id]);
    }

    // ===== THIS IS THE NEW METHOD =====
    public function resendOtp(Request $request)
    {
        $request->validate(['id' => 'required|exists:admins,id']);
        $admin = Admin::findOrFail($request->id);

        try {
            // Generate and store a new token
            $token = rand(100000, 999999);
            $this->phoneOrEmailVerificationRepo->delete(['phone_or_email' => $admin->phone]);
            $this->phoneOrEmailVerificationRepo->add([
                'phone_or_email' => $admin->phone,
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Prepare and send the WhatsApp message
            $postData = ['description' => "Your new verification code is: " . $token];
            $this->notificationService->sendWhatsAppNotification($admin->phone, $postData);

            Toastr::success(translate('A new OTP has been sent to your phone.'));

        } catch (\Exception $e) {
            Toastr::error(translate('Failed to send OTP. Please try again later.'));
        }

        return redirect()->back(); // Redirect back to the OTP entry page
    }
}