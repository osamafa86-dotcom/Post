<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Enums\UserStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Register extends Component
{
    public array $state = [];
    public function mount()
    {
        if (Auth::guard('web_user')->check()) {
            return redirect()->route('main.maktoob.user_dashboard_main');
        }
    }

    public function register()
    {
        $validator = Validator::make(
            $this->state,
            [
                'first_name' => 'required|string',
                'last_name'  => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password'   => 'required|min:8|confirmed',
            ],
            [
                'email.unique' => __('messages.unable_to_use'),
                'password.confirmed' => __('messages.not_matching'),
                'password.min' => __('messages.min_length'),
            ]
        );

        $validated = $validator->validate();

        $user = \App\Models\User::create([
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'user_status' => UserStatusEnum::SUBSCRIBER->value,
        ]);

        $user->details()->create(['is_active' => true]);

        Auth::guard('web_user')->login($user);

        return redirect()->route('main.maktoob.user_dashboard_main');
    }


    #[Layout('components.layouts.main.maktoob.user_dashboard.auth')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.register');
    }
}
