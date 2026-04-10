<?php

namespace App\Livewire\Main\Almohajer;

use App\Models\PodcastAlbum;
use App\Models\VideoAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AllVideoAlbums extends Component
{
    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.all-video-albums');
    }

    #[Computed]
    public function videos(): Collection|array
    {
        return VideoAlbum::query()->latest()->take(9)->get();
    }
}
