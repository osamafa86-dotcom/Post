<?php

namespace App\Livewire\Main\Tayqan;

use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    #[Layout('components.layouts.main.tayqan.main')]
    public function render()
    {
        return view('livewire.main.tayqan.about-us');
    }
}
