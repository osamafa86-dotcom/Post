<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialPage;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.login');
    }

    public $email;
    public $password;
    public $attempts;

    public function mount()
    {
        $this->attempts = 0;

        if (Auth::guard('web_user')->check()) {
            return redirect('/hongora/dashboard/'); // Redirect authenticated users
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
                'title' =>  __('messages.Failed_login_attempt_please_pay_attention'),
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
                    if(!empty(session('beforeLoginUrl',''))){
                        return redirect(session('beforeLoginUrl',''));
                    }
                    return redirect()->route('hongora.user_dashboard.main');
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/hongora/login');
    }

}
