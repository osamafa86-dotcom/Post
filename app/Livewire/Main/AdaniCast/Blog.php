<?php

namespace App\Livewire\Main\AdaniCast;

use App\Enums\CategoryTypeEnum;
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
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;
    public int $articles_number = 0;
    public int $article_count = 0;
    public $category;
    public string $blogSort = 'default';
    public string $search = '';

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.blog');
    }

    public function mount(): void
    {
        $this->article_count = Post::query()
            ->whereHas('category', function ($q) {

                $q->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('category_type', CategoryTypeEnum::NEWS->value);
                    }
                );


            })

            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->count();
        $this->articles_number = 12;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedCategory(): void
    {
        $this->resetPage();
    }
    public function updatedBlogSort(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function articles()
    {
        return Post::query()
            ->whereHas('category', function ($q) {

                $q->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('category_type', CategoryTypeEnum::NEWS->value);
                    }
                );


            })
            ->when($this->blogSort, function ($q) {
                if($this->blogSort == 'default') {
                    return $q;
                }elseif ($this->blogSort == 'mostViewed') {
                    return $q->orderByDesc('views');
                }elseif ($this->blogSort == 'recent') {
                    return $q->orderByDesc('created_at');
                }
                return $q;
            })
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($q) {

                     $q->whereHas('category', function ($q) {

                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category);
                        }
                    );


                });
            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['author'])
            ->paginate(6);
    }
    #[Computed]
    public function categories(): array
    {
        return Category::select('id', 'category_title')
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->whereHas('post_relation')
            ->orderBy('category_title')->withCount('post_relation')
            ->get()
            ->toArray();
    }
    public function sortCategory($id): void
    {
        $this->category=$id;
    }
    #[Computed]
    public function lastThreeArticles(): Collection|array
    {
        return Post::query()
            ->whereHas('category', function ($q) {
                $q->where('category_title', 'مقالات');
            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();
    }
}
