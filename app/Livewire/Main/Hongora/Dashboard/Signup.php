<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Signup extends Component
{

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.signup');
    }

    public array $state = [];
    public function mount()
    {
        $this->attempts = 0;

        if (Auth::guard('web_user')->check()) {
            return redirect('/hongora/dashboard/'); // Redirect authenticated users
        }
    }

    public function signup()
    {

        $validate = Validator::make($this->state,[
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',

        ])->validate();
        $fullNameParts = explode(' ', trim($validate['full_name']));
        $first_name = $fullNameParts[0] ?? '';
        $last_name = isset($fullNameParts[1])
            ? implode(' ', array_slice($fullNameParts, 1))
            : '';

        $user = \App\Models\User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
        ]);

        $user->details()->create();
//
//        $emailKey = 'login_attempts_' . $this->email;
//        $attempts = Cache::get($emailKey, 0);
//
//        if ($attempts >= 3) {
//            $lockedOutUntil = Cache::get('login_locked_out_' . $this->email);
//            if ($lockedOutUntil && now() < $lockedOutUntil) {
//                $remainingTime = max(1, now()->diffInSeconds($lockedOutUntil));
//                $errorMessage = "يرجى المحاولة فيما بعد. الوقت المتبقي: $remainingTime ثانية.";
//
//                session()->flash('error', $errorMessage);
//                return redirect()->back();
//            }
//            Alert::create([
//                'title' => 'محاولة دخول فاشلة يرجى الانتباه',
//                'type' => 'خطر',
//                'status' => false,
//                'content' => json_encode(['email' => $this->email]),
//            ]);
//        }
//
//        $credentials = [
//            'email' => $this->email,
//            'password' => $this->password,
//        ];
//
//        if (Auth::guard('web_user')->attempt($credentials)) {
//            // Reset attempts on successful login
//            Cache::forget($emailKey);
//
//            // Remove lockout information from cache on successful login
//            Cache::forget('login_locked_out_' . $this->email);
//
//            return redirect()->intended('/hongora/dashboard/');
//        } else {
//            $attempts++;
//
//            if ($attempts >= 5) {
//                $lockedOutUntil = now()->addMinutes(); // Lockout for 2 minutes
//                // Store lockout information in cache
//                Cache::put('login_locked_out_' . $this->email, $lockedOutUntil, now()->addMinutes());
//            }
//
//            // Store login attempts in cache
//            Cache::put($emailKey, $attempts);

        //    session()->flash('error', 'بيانات غير صحيحة حاول مرة اخرى.');
         //   return redirect()->back();
       // }

        session()->flash('error', __('messages.Thank_you_your_request_will_be_reviewed'));

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/hongora/login');
    }

}
