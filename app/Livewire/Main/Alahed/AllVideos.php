<?php

namespace App\Livewire\Main\Alahed;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AllVideos extends Component
{
    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.alahed.all-videos');
    }
}
