<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryNews extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public $category;
    public string $view_mode = 'grid';

    #[Layout('components.layouts.main.fimed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.category-news');
    }

    public function mount($category_title): void
    {
        $this->category = Category::where('category_title', $category_title)
            ->orWhere('category_title', 'LIKE', "%{$category_title}%")
            ->firstOrFail();
    }

    #[Computed]
    public function articles()
    {
        return Post::select('id', 'slug', 'title', 'description', 'publish_date', 'news_type')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->category->id);
            })
            ->orderByDesc('publish_date')
            ->with(['files.file', 'author.relationable.files.file', 'category.relationable'])
            ->paginate(6);
    }

    #[Computed]
    public function totalArticles(): int
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->category->id);
            })
            ->count();
    }
}
