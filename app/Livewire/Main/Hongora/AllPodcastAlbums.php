<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\MaterialTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\PodcastAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AllPodcastAlbums extends Component
{
    use WithPagination;
    public $category;
    public $podcastSort='recent';
    public $search;
    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.all-podcast-albums');
    }
    #[Computed]
    public function materialAlbums()
    {
        return MaterialAlbum::query()
            ->where( 'type', MaterialTypeEnum::PODCAST->value)
            ->when($this->search, function ($q) {
                return $q->where('name', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })->with(['files.file','category.relationable'])
            ->paginate(6);
    }


}
