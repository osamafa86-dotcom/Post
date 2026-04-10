<?php

namespace App\Livewire\Main\AdaniCast;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\PodcastAlbum;
use App\Models\PodcastTrack;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Podcasts extends Component
{
    use WithPagination;

    public $category;
    public $podcastSort='recent';
    #[Url(keep: true)]
    public $search;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.podcasts');
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
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function mount($category_title = null): void
    {
        $category = Category::where('category_title', $category_title)->first();
        $this->category = (int)$category?->id;

    }
    public function sortCategory($id=null): void
    {
        $this->category=$id;
        $this->resetPage();
    }
    #[Computed]
    public function podcasts()
    {
        return Material::query()
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->when($this->podcastSort, function ($q) {
                if($this->podcastSort == 'default') {
                    return $q;
                }elseif ($this->podcastSort == 'recent') {
                    return $q->orderBy('created_at', 'desc');
                }
                return $q;
            })
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($q) {
                return  $q->whereHas('category', function ($q2) {

                    $q2->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category);
                        }
                    );


                });
            })
            ->with(['files.file','presenter.relationable'])
            ->paginate(6);
    }

    #[Computed]
    public function categories(): array
    {
        return Category::select('id', 'category_title')
            ->whereIn('category_type', [CategoryTypeEnum::PODCAST->value,CategoryTypeEnum::VIDEO->value ,CategoryTypeEnum::IMAGE->value ])
            ->orderBy('category_title')->withCount('main_material_relation')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function podcastsAlbums(): Collection|array
    {
        return PodcastAlbum::query()->latest()->take(9)->get();
    }

}
