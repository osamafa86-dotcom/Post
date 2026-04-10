<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\ParticipantTypeEnum;
use App\Models\Participant;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.about-us');
    }

    #[Computed]
    public function supporters(): Collection|array
    {

        return Participant::query()
            ->where('type', ParticipantTypeEnum::SUPPORTERS->value)
            ->get();
    }

    #[Computed]
    public function partners(): Collection|array
    {

        return Participant::query()
            ->where('type', ParticipantTypeEnum::PARTNERS->value)
            ->get();
    }

    #[Computed]
    public function team(): Collection|array
    {

        return Participant::query()
            ->where('type', ParticipantTypeEnum::TEAM->value)
            ->get();
    }

}
