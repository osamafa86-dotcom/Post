<?php

namespace App\Livewire\Main\AdaniCast;

use App\Enums\CategoryTypeEnum;
use App\Enums\LinkPosition;
use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\Course;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\Participant;
use App\Models\Reel;
use App\Models\SocialMedia;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class IndexPage extends Component
{
    #[Computed]
    public function social_medias(): Collection|array
    {
        return SocialMedia::query()->where('position',LinkPosition::AllPlaces->value)->get();
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
    public function categories()
    {
        return Category::with(['files.file'])
            ->withCount('material_relation')
            ->where('category_type', CategoryTypeEnum::PODCAST->value)
            ->get();
    }

    #[Computed]
    public function famousPodcasts()
    {
        return Material::where('type', MaterialTypeEnum::PODCAST->value)
            ->withSum('views as views_sum', 'views_number')
            ->orderByRaw('COALESCE(views_sum, 0) DESC')
            ->with(['files.file', 'material_album','category.relationable','presenters.relationable'])
            ->take(8)
            ->get();

    }
    #[Computed]
    public function latestPodcasts()
    {
        return Material::where('type', MaterialTypeEnum::PODCAST->value)
            ->latest('created_at')
            ->with(['files.file', 'material_album','category.relationable','presenters.relationable'])
            ->take(8)
            ->get();

    }

    #[Computed]
    public function presenters()
    {
        return Participant::where('type', ParticipantTypeEnum::PRESENTERS->value)
            ->with(['participant_social_media'])->take(4)->get();
    }

    #[Computed]
    public function reels()
    {
        return Reel::latest()->get();
    }



    #[Computed]
    public function course()
    {
        return Course::with(['user_logs.user'])->first();
    }

    #[Computed]
    public function offers_podcast_album()
    {
     return MaterialAlbum::where('type', MaterialTypeEnum::PODCAST->value)
         ->withCount([
             'album_materials as max_views' => function ($query) {
                 $query->selectRaw('MAX(views)');
             }
         ])
         ->orderByDesc('max_views')
         ->with(['files.file','category.relationable','latest_album_materials'])
         ->take(4)
         ->get();
    }

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.index-page')
            ->layout('components.layouts.main.adani_cast.main', [
                'social_medias' => $this->social_medias,
            ]);
    }
}
