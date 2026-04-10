<?php

namespace App\Livewire\Main\Fimed;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        $settings = Setting::first();
        return view('livewire.main.fimed.about-us', ['settings' => $settings]);
    }
}
