<?php

namespace App\Livewire\Main\Fimed;

use App\Models\Category;
use App\Models\SpecialPage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SiteMap extends Component
{
    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        return view('livewire.main.fimed.site-map');
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('category_title')->get();
    }

    #[Computed]
    public function specialPages()
    {
        return SpecialPage::orderBy('title')->get();
    }
}
