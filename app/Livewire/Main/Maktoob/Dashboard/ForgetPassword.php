<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ForgetPassword extends Component
{
    public function mount()
    {
        if (Auth::guard('web_user')->check()) {
            return redirect()->route('main.maktoob.user_dashboard_main');
        }
    }
    #[Layout('components.layouts.main.maktoob.user_dashboard.auth')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.forget-password');
    }
}
