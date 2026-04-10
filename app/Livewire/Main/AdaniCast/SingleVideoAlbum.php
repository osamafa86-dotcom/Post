<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\PodcastAlbum;
use App\Models\PodcastTrack;
use App\Models\VideoAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SingleVideoAlbum extends Component
{
    public object $video_album;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.single-video-album');
    }

    public function mount($video_album_id): void
    {
        $this->video_album = VideoAlbum::find($video_album_id)->with('files','video_tracks')->first();
    }
}
