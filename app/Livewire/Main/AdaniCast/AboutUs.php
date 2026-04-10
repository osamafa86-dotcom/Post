<?php

namespace App\Livewire\Main\AdaniCast;
use App\Enums\ParticipantTypeEnum;
use App\Models\Participant;
use App\Models\Presenter;
use App\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AboutUs extends Component
{
    public $Settings;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $this->Settings = Setting::first();
        return view('livewire.main.adani-cast.about-us');
    }

    #[Computed]
    public function presenters()
    {
        return Participant::where('type', ParticipantTypeEnum::PRESENTERS->value)->with(['participant_social_media','files.file'])->take(4)->get();
    }

    #[Computed]
    public function partners()
    {
        return Participant::where('type', ParticipantTypeEnum::PARTNERS->value)->with(['participant_social_media','files.file'])->take(4)->get();
    }
}
