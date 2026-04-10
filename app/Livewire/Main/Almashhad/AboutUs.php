<?php

namespace App\Livewire\Main\Almashhad;

use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.about-us');
    }
}
