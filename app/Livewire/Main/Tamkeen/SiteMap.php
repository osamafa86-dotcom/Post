<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\Material;
use App\Models\Participant;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SiteMap extends Component
{


    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {



        return view('livewire.main.tamkeen.site-map');
    }
}
