<?php

namespace App\Livewire\Main\HodHod;

use App\Enums\LinkPosition;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchBar extends Component
{
    public $categoryID,$search;
    #[Computed]
    public function categories()
    {
        return \App\Models\Category::all();
    }

    #[Computed]
    public function social_media()
    {
        return \App\Models\SocialMedia::with('icon')->where('position',LinkPosition::AllPlaces->value)->get();
    }

    public function submitSearch()
    {
        return redirect()->route('main.hodhod.search',['id'=>$this->categoryID,'search'=>$this->search]);
    }
    public function render()
    {
        return view('livewire.main.hod-hod.search-bar');
    }
}
