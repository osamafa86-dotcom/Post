<?php

namespace App\Livewire\Main\AdaniCast;

use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\Participant;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PresenterPodcasts extends Component
{

    public $id;
    public $presenter;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.presenter-podcasts');
    }
    public function mount(): void
    {
        $this->presenter= Participant::where('id', $this->id)->with(['files.file','participant_social_media'])->first();
    }

    #[Computed]
    public function albums()
    {
        $presenterId = $this->id;
        return MaterialAlbum::whereHas('album_materials.presenters', function ($query) use ($presenterId) {
            $query->whereHasMorph(
                'relationable',
                [Participant::class],
                function ($q) use ($presenterId) {
                    $q->where('id', $presenterId);
                }
            );
        })->with(['files.file','category.relationable','album_materials.presenter.relationable.files.file'])->get();
    }

    #[Computed]
    public function materials()
    {
        $presenterId = $this->id;

        return Material::whereHas('presenters', function ($query) use ($presenterId) {
            $query->whereHasMorph(
                'relationable',
                [Participant::class],
                function ($q) use ($presenterId) {
                    $q->where('id', $presenterId);
                }
            );
        })->with(['files.file','category.relationable'])->get();
    }
    public function playAudio($podcastId)
    {
        if (Cache::has('podcast_id')){
            if (Cache::get('podcast_id') != $podcastId){
                $this->dispatch('playAudio', ['podcastId' => $podcastId]);
            }else{
                Cache::forget('podcast_id');
                $this->dispatch('pauseAudioPlayer');
                $this->dispatch('refreshPodcastButton');
            }
        }else{
            $this->dispatch('playAudio', ['podcastId' => $podcastId]);
        }
    }

    public function addFavorite($id)
    {
        $sessionId = session()->getId();
        $key = "favorites_session_{$sessionId}";
        $favorites = Cache::get($key, []);
        if (in_array($id, $favorites)) {
            $favorites = array_diff($favorites, [$id]);
        } else {
            $favorites[] = $id;
        }
        Cache::put($key, $favorites);
        $this->dispatch('updateFavoriteList');
    }

    #[Computed]
    public function mostViewed()
    {
        return Material::where('type', MaterialTypeEnum::PODCAST->value)
            ->with(['files.file','category.relationable','presenters.relationable'])
            ->withSum('views as views_sum', 'views_number')
            ->orderByRaw('COALESCE(views_sum, 0) DESC')
            ->get();
    }
}
