<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\PublishEnum;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TagNews extends Component
{
    public int $posts_number = 5;
    public string $tag_name;
    public object $posts;
    public object $most_viewed_posts;
    public object $latest_posts;
    public object $article_posts;
    public int $posts_count;

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.tag-news');
    }

    public function mount(string $tag_name): void
    {
        $this->tag_name = $tag_name;

        $query = Post::with(['tags.relationable', 'files.file', 'category.relationable'])
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
        $query = Post::with(['tags.relationable', 'files.file', 'category.relationable'])
            ->whereHas('tags', function ($q) {
                $q->where('tag_name', $this->tag_name);
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
