<?php

namespace App\Livewire\Main\Ufok;

use App\Enums\CategoryTypeEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SiteMap extends Component
{
    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.site-map');
    }
}
