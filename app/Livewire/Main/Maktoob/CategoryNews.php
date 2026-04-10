<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\PublishEnum;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryNews extends Component
{
    public int $posts_number = 5;
    public string $category_title;
    public object $posts;
    public object $most_viewed_posts;
    public object $latest_posts;
    public object $article_posts;
    public int $posts_count;

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.category-news');
    }

    public function mount(string $category_title): void
    {
        $this->category_title = $category_title;

        $query = Post::with(['category.relationable', 'files.file'])
            ->where('publish_status', PublishEnum::PUBLISHED->value);

        $this->most_viewed_posts = $query->clone()
            ->withSum('views as views_sum', 'views_number')
        ->orderByRaw('COALESCE(views_sum, 0) DESC')
        ->orderByDesc('publish_date')
            ->take(5)
            ->get();
        $this->latest_posts = $query->clone()->orderBy('publish_date', 'desc')->take(5)->get();
        $this->article_posts = $query->clone()->whereHas('category', function ($q) {
            $q->where('category_title', 'مقالات');
        })->orderBy('publish_date', 'desc')->take(5)->get();
        $this->loadPosts();
    }

    public function loadPosts(): void
    {
        $query = Post::with(['category.relationable', 'files.file'])
            ->whereHas('category', function ($q) {
                $q->where('category_title', $this->category_title);
            });

        $this->posts = $query->clone()->take($this->posts_number)->get();
        $this->posts_count = $query->count();
    }

    public function incrementPostNumber(): void
    {
        $this->posts_number += 5;
        $this->loadPosts();
    }
}
