<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Participant;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Podcasts extends Component
{

    use WithPagination;

    #[Url(keep: true)]
    public $album_id;
    #[Url(keep: true)]
    public $category;
    public $favorites = [];
    public $podcastSort = 'recent';
    public $search;



    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.podcasts');
    }

    public function mount($category_title = null): void
    {
        $sessionId = session()->getId();
        $this->favorites = Cache::get("favorites_session_{$sessionId}", []);
    }

    public function sortCategory($id): void
    {
        $this->category = $id;
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function podcasts()
    {
        return Material::query()
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->when($this->podcastSort, function ($q) {
                if ($this->podcastSort == 'default') {
                    return $q;
                } elseif ($this->podcastSort == 'recent') {
                    return $q->orderBy('created_at', 'desc');
                }
                return $q;
            })
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->album_id, function ($q) {
                return $q->whereHas('material_album', function ($q2) {
                    $q2->where('album_id', $this->album_id);
                });
            })
            ->when($this->category && $this->category != 'all', function ($q) {
                return $q->whereHas('category', function ($q2) {
                    $q2->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category);
                        }
                    );
                });
            })
            ->with(['tags.relationable', 'presenter.relationable', 'files.file', 'material_album'])
            ->paginate(10);
    }

    #[Computed]
    public function categories(): array
    {
        return Category::select('id', 'category_title')
            ->where('category_type', CategoryTypeEnum::PODCAST->value)
            ->orderBy('category_title')->withCount('material_relation')
            ->get()
            ->toArray();
    }


    public function toggleFavorite($id)
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
        $this->favorites = $favorites;
        $this->dispatch('updateFavoriteList');
    }

    #[On('updatePodcastFavoriteList')]
    public function updatePodcastFavoriteList()
    {
        $sessionId = session()->getId();
        $key = "favorites_session_{$sessionId}";
        $favorites = Cache::get($key, []);
        Cache::put($key, $favorites);
        $this->favorites = $favorites;
    }

    public function isFavorited($id)
    {
        return in_array($id, $this->favorites);
    }
}
