<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.about-us');
    }

    #[Computed]
    public function team()
    {
        return Participant::where('type', ParticipantTypeEnum::TEAM->value)->get();
    }
}
