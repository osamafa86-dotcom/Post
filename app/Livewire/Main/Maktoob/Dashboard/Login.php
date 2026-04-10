<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Models\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Http\Request;

class Login extends Component
{
    public $email;
    public $password;
    public $attempts;
    public $mode = 'login'; // login, forgot, verify, reset
    public $code;
    public $newPassword;
    public $newPasswordConfirmation;
    public $message;

    public function mount()
    {
        $this->attempts = 0;
        if (Auth::guard('web_user')->check()) {
            return redirect()->route('main.maktoob.user_dashboard_main');
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $emailKey = 'web_login_attempts_' . $this->email;
        $attempts = Cache::get($emailKey, 0);

        if ($attempts >= 3) {
            $lockedOutUntil = Cache::get('web_login_locked_out_' . $this->email);
            if ($lockedOutUntil && now() < $lockedOutUntil) {
                $remainingTime = max(1, now()->diffInSeconds($lockedOutUntil));
                $errorMessage = "يرجى المحاولة فيما بعد. الوقت المتبقي: $remainingTime ثانية.";

                session()->flash('error', $errorMessage);
                return redirect()->back();
            }
            Alert::create([
                'title' => __('messages.Failed_login_attempt_please_pay_attention'),
                'type' => 'خطر',
                'status' => false,
                'content' => json_encode(['email' => $this->email]),
            ]);
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::guard('web_user')->attempt($credentials)) {


            if (Auth::guard('web_user')->user()?->details?->is_active) {
                Cache::forget($emailKey);
                Cache::forget('web_login_locked_out_' . $this->email);

                // Check if there was a pending subscription plan selection
                if (session()->has('selected_subscription_plan_id')) {
                    $planId = session()->get('selected_subscription_plan_id');
                    session()->forget('selected_subscription_plan_id');
                    return redirect()->route('main.maktoob.support', ['redirect_to_plan' => $planId]);
                }

                if (!empty(session('beforeLoginUrl', ''))) {
                    return redirect(session('beforeLoginUrl', ''));
                }
                return redirect()->route('main.maktoob.user_dashboard_main');
            } else {
                Auth::guard('web_user')->logout();
                session()->flash('error', __('messages.The_account_has_not_been_approved_yet'));
                return redirect()->back();
            }

        } else {
            $attempts++;
            if ($attempts >= 5) {
                $lockedOutUntil = now()->addMinutes(); // Lockout for 2 minutes
                // Store lockout information in cache
                Cache::put('web_login_locked_out_' . $this->email, $lockedOutUntil, now()->addMinutes());
            }

            // Store login attempts in cache
            Cache::put($emailKey, $attempts);

            session()->flash('error', __('messages.Incorrect_data'));
            return redirect()->back();
        }
    }

    public function showForgotPassword()
    {
        $this->mode = 'forgot';
        $this->resetErrorBag();
        $this->message = null;
    }

    public function showLogin()
    {
        $this->mode = 'login';
        $this->resetErrorBag();
        $this->message = null;
    }

    public function sendResetCode()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

//        $user = \App\Models\User::where('email', $this->email)->first();
//        if (!$user) {
//            $this->addError('email', __('messages.User_not_found'));
//            return;
//        }
        $code = rand(100000, 999999);
        $cacheKey = 'password_reset_code_' . $this->email;
        Cache::put($cacheKey, $code, now()->addMinutes(15));

        // Sending Email (Using Mail::raw for simplicity if no template exists)
        try {
            \Illuminate\Support\Facades\Mail::raw("Your password reset code is: $code", function ($message) {
                $message->to($this->email)
                    ->subject('Password Reset Code');
            });

            $this->mode = 'verify';
            $this->message = __('messages.Reset_code_sent_to_your_email');
        } catch (\Exception $e) {
            // Add dd() to show the exact error
//            dd('Email sending failed:', $e->getMessage(), 'Full exception:', $e);

            // Or just the error message if you want less output
            // dd('Email sending failed:', $e->getMessage());

            $this->addError('email', 'Failed to send email. Please try again later.');
        }
    }
    public function verifyCode()
    {
        $this->validate([
            'code' => 'required|numeric',
        ]);

        $cacheKey = 'password_reset_code_' . $this->email;
        $storedCode = Cache::get($cacheKey);

        if ($storedCode && $storedCode == $this->code) {
            $this->mode = 'reset';
            $this->message = null;
        } else {
            $this->addError('code', __('messages.Invalid_or_expired_code'));
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:8',
            'newPasswordConfirmation' => 'required|same:newPassword',
        ]);

        $user = \App\Models\User::where('email', $this->email)->first();
        if ($user) {
            $user->password = \Illuminate\Support\Facades\Hash::make($this->newPassword);
            $user->save();

            Cache::forget('password_reset_code_' . $this->email);

            $this->mode = 'login';
            session()->flash('success', __('messages.Password_reset_successfully_Please_login'));
            return redirect()->route('main.maktoob.user_dashboard_login');
        }

        $this->addError('email', __('messages.Something_went_wrong'));
    }


    public function logout(Request $request)
    {
        Auth::guard('web_user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('main.maktoob.user_dashboard_login');
    }

    #[Layout('components.layouts.main.maktoob.user_dashboard.auth')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.login');
    }
}
