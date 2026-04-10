<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Models\HongoraUserSound;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MainDashboard extends Component
{

    #[Layout('components.layouts.main.hongora.user_dashboard.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.main-dashboard');
    }

    #[Computed]
    public function totalViews()
    {
        return HongoraUserSound::where('user_id',Auth::guard('web_user')->id())->sum('views');
    }
}
