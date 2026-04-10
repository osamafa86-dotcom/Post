<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\PublishEnum;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryNews extends Component
{

    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.category-news');
    }
}
