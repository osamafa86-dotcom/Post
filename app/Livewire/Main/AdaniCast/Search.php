<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Material;
use App\Models\PodcastTrack;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Search extends Component
{
    public ?string $searchTerm = '';

    public function search()
    {
        return redirect()->route('main.adani_cast.podcasts', ['search' => $this->searchTerm]);
    }

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.search');
    }
}
