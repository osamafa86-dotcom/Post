<?php

namespace App\Livewire\Main\Almohajer;

use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Faq extends Component
{
    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.faq');
    }
}
