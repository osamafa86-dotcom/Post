<?php

namespace App\Livewire\Main\Alarouri;

use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.about-us');
    }
}
