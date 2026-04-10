<?php

namespace App\Livewire\Main\Ufok;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryEvents extends Component
{
    public $category;

    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.category-events');
    }

    public function mount($id, $category_title): void
    {
        $this->category = Category::with('event_relation')->withTrashed()->where(function ($q) use ($id, $category_title) {
            $q->where('category_title', $category_title)
                ->where('id', $id);
        })->first();

    }
    }
