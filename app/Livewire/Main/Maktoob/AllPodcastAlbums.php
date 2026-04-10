<?php

namespace App\Livewire\Main\Maktoob;

use App\Models\PodcastAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AllPodcastAlbums extends Component
{
    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.all-podcast-albums');
    }

    #[Computed]
    public function podcasts(): Collection|array
    {
        return PodcastAlbum::query()->latest()->take(9)->get();
    }

}
