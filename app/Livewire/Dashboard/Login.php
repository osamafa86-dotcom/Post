<?php

namespace App\Livewire\Dashboard;

use App\Enums\AlertTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Alert;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;
    public $attempts;
    public $locale;

    public function mount()
    {
        $this->attempts = 0;
        if (!session()->has('locale')) {
            $this->setInitialLanguage();
        }
        if (Auth::check()) {
            return redirect('/dashboard/');
        }
    }

    protected function setInitialLanguage()
    {
        $browserLang = substr(request()->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        $supportedLangs = ['en', 'ar'];

        if (in_array($browserLang, $supportedLangs)) {
            $locale = $browserLang;
        } else {
            $locale = config('app.locale', 'en');
            if (!in_array($locale, $supportedLangs)) {
                $locale = 'en';
            }
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    public function changeLanguage($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
            $this->dispatch('languageChanged', $locale);
        }
    }

    public function login()
    {
        $validatedData = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $emailKey = 'login_attempts_' . $this->email;
        $attempts = Cache::get($emailKey, 0);

        if ($attempts >= 3) {
            $lockedOutUntil = Cache::get('login_locked_out_' . $this->email);
            if ($lockedOutUntil && now() < $lockedOutUntil) {
                $remainingTime = max(1, now()->diffInSeconds($lockedOutUntil));
                $errorMessage = __('validation.try_later',['time' => $remainingTime]);

                session()->flash('error', $errorMessage);
                return redirect()->back();
            }
            Alert::create([
                'title' => __('validation.failed_login_try'),
                'type' => AlertTypeEnum::DANGER->value,
                'status' => false,
                'content' => json_encode(['email' => $this->email]),
            ]);
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];
        $loginAttempt=Auth::guard('web')->attempt($credentials);
        if ($loginAttempt){
            $user=Auth::user();
        }
        if ($loginAttempt && $user->user_status==UserStatusEnum::ADMIN->value) {
            // Reset attempts on successful login
            Cache::forget($emailKey);

            // Remove lockout information from cache on successful login
            Cache::forget('login_locked_out_' . $this->email);

            return redirect()->intended('/dashboard/');

        } else {
            $attempts++;

            if ($attempts >= 5) {
                $lockedOutUntil = now()->addMinutes(); // Lockout for 2 minutes
                // Store lockout information in cache
                Cache::put('login_locked_out_' . $this->email, $lockedOutUntil, now()->addMinutes());
            }

            // Store login attempts in cache
            Cache::put($emailKey, $attempts);

            session()->flash('error', __('validation.incorrect_login_credentials'));
            return redirect()->back();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.login')->with([
            'currentLocale' => app()->getLocale()
        ]);
    }
}
