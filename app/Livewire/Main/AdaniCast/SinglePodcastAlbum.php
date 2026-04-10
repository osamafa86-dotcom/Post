<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\PodcastAlbum;
use App\Models\PodcastTrack;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SinglePodcastAlbum extends Component
{
    public object $podcast_album;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.single-podcast-album');
    }

    public function mount($podcast_album_id): void
    {
        $this->podcast_album = PodcastAlbum::find($podcast_album_id)->with('files','podcast_tracks.files')->first();
    }

    #[Computed]
    public function podcast_tracks(): Collection|array
    {
        return PodcastTrack::query()->where('podcast_album_id', $this->podcast_album_id)->with('files')->get();
    }
}
