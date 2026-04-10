<?php

namespace App\Livewire\Main\AdaniCast;

use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Material;
use App\Models\Participant;
use App\Models\VideoTrack;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.main.adani_cast.main')]
class Gallery extends Component
{

    #[Computed]
    public function gallery()
    {
        return Material::whereIn('type', [MaterialTypeEnum::VIDEO->value , MaterialTypeEnum::IMAGE->value])->with('files.file')->take(12)->get();
    }
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.gallery');
    }
}
