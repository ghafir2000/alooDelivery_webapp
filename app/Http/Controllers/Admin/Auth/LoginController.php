<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\SessionKey;
use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\Auth as AuthViewPath; // Renamed to avoid conflict
use App\Http\Controllers\BaseController;
use App\Models\Admin;
use App\Services\AdminService;
use App\Traits\RecaptchaTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
// Add these imports
use App\Services\NotificationService;
use App\Contracts\Repositories\PhoneOrEmailVerificationRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    use RecaptchaTrait;

    public function __construct(
        private readonly Admin $admin, 
        private readonly AdminService $adminService,
        // Inject the new services
        private readonly PhoneOrEmailVerificationRepositoryInterface $phoneOrEmailVerificationRepo,
        private readonly NotificationService $notificationService
    )
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
        // REMOVED: $this->middleware('verified'); We will use a custom middleware later.
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable
    {
        return $this->getLoginView(loginUrl: $type);
    }

    public function generateReCaptcha()
    {
        $recaptchaBuilder = $this->generateDefaultReCaptcha(4);
        if (Session::has(SessionKey::ADMIN_RECAPTCHA_KEY)) {
            Session::forget(SessionKey::ADMIN_RECAPTCHA_KEY);
        }
        Session::put(SessionKey::ADMIN_RECAPTCHA_KEY, $recaptchaBuilder->getPhrase());
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $recaptchaBuilder->output();
    }

    private function getLoginView(string $loginUrl): View
    {
        $loginTypes = [
            UserRole::ADMIN => getWebConfig(name: 'admin_login_url'),
            UserRole::EMPLOYEE => getWebConfig(name: 'employee_login_url')
        ];

        $userType = array_search($loginUrl, $loginTypes);
        abort_if(!$userType, 404);

        $recaptchaBuilder = $this->generateDefaultReCaptcha(4);
        Session::put(SessionKey::ADMIN_RECAPTCHA_KEY, $recaptchaBuilder->getPhrase());

        $recaptcha = getWebConfig(name: 'recaptcha');

        return view(AuthViewPath::ADMIN_LOGIN, compact('recaptchaBuilder', 'recaptcha'))->with(['role' => $userType]);
    }

    public function login(Request $request): RedirectResponse
    {
        $recaptcha = getWebConfig(name: 'recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secretKey = getWebConfig(name: 'recaptcha')['secret_key'];
                        $response = $value;
                        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $response;
                        $response = Http::get($url);
                        $response = $response->json();
                        if (!isset($response['success']) || !$response['success']) {
                            $fail(translate('ReCAPTCHA_Failed'));
                        }
                    },
                ],
            ]);
        } else if(strtolower(session(SessionKey::ADMIN_RECAPTCHA_KEY)) != strtolower($request['default_captcha_value'])) {
            Toastr::error(translate('ReCAPTCHA_Failed'));
            return back();
        }
        
        $admin = $this->admin->where('email', $request['email'])->first();

        if (isset($admin) && in_array($request['role'], [UserRole::ADMIN, UserRole::EMPLOYEE]) && $admin->status) {
            // Check credentials manually
            if (Auth::guard('admin')->validate(['email' => $request['email'], 'password' => $request['password']])) {
                
                // 1. Credentials are correct. Send OTP.
                $this->sendLoginOtp($admin);

                // 2. Store 'remember' preference if needed for later
                if ($request->boolean('remember')) {
                    session(['admin_remember' => true]);
                }

                // 3. Redirect to OTP page with Admin ID (do not log them in yet)
                Toastr::success(translate('Verification code sent to your phone.'));
                return redirect()->route('admin.auth.otp.view', ['id' => $admin->id]);
            }
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('credentials does not match or your account has been suspended')]);
    }

    public function logout(): RedirectResponse
    {
        $this->adminService->logout();
        // Clear the OTP session on logout
        session()->forget('admin_otp_verified'); 
        session()->flash('success', translate('logged out successfully'));
        return redirect('login/' . getWebConfig(name: 'admin_login_url'));
    }

    /**
     * Generates and sends the OTP via WhatsApp.
     */
    private function sendLoginOtp($admin)
    {
        $token = rand(100000, 999999);

        // Delete old tokens
        $this->phoneOrEmailVerificationRepo->delete(['phone_or_email' => $admin->phone]);

        // Store new token
        $this->phoneOrEmailVerificationRepo->add([
            'phone_or_email' => $admin->phone,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send WhatsApp Notification
        $postData = [
            'description' => "Your admin login verification code is: " . $token
        ];
        $this->notificationService->sendWhatsAppNotification($admin->phone, $postData);
    }
}