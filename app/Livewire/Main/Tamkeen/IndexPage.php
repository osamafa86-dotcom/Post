<?php

namespace App\Livewire\Main\Tamkeen;
use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{


    #[Computed]
    public function courses(): Collection|array
    {
        return Event::query()
           ->with(['files', 'event_dates'])
           ->get();

    }


    #[Computed]
    public function categories_index(): Collection|array
    {
        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->where('show_index', 1)
            ->whereHas('post_relation')
            ->with(['post_relation.post.files.file'])
            ->withCount('post_relation')
            ->orderBy('post_relation_count', 'desc')
            ->get();

    }


    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.index-page');
    }
}
