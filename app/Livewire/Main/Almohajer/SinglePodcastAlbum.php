<?php

namespace App\Livewire\Main\Almohajer;

use App\Models\PodcastAlbum;
use App\Models\PodcastTrack;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


class SinglePodcastAlbum extends Component
{
    public object $podcast_album;

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.single-podcast-album');
    }

    public function mount($podcast_album_id): void
    {
        $this->podcast_album = PodcastAlbum::find($podcast_album_id)->with('files','podcast_tracks.files')->first();
        if (!Session::has('viewed_podcast_' . $this->podcast_album->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->podcast_album->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->podcast_album->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_podcast_' . $this->podcast_album->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }

    }

    #[Computed]
    public function podcast_tracks(): Collection|array
    {
        return PodcastTrack::query()->where('podcast_album_id', $this->podcast_album_id)->with('files')->get();
    }
}
