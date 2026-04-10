<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    #[Layout('components.layouts.main.fimed.main', ['withSwiper' => true])]
    public function render(): Factory|Application|ViewContract|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.index-page');
    }

    /**
     * Hero: Main featured post
     */
    #[Computed]
    public function mainPost()
    {
        return Post::select('id', 'slug', 'title', 'description', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->orderByDesc('publish_date')
            ->with(['category.relationable', 'files.file', 'author.relationable.files.file'])
            ->first();
    }

    /**
     * Hero: Sidebar posts (4 items)
     */
    #[Computed]
    public function heroSidebarPosts()
    {
        return Post::select('id', 'slug', 'title', 'description', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::SUB_NEWS->value)
            ->orderByDesc('publish_date')
            ->with(['files.file', 'category.relationable'])
            ->limit(5)
            ->get();
    }

    /**
     * All categories with show_index = 1, with their posts (same as Almohajer)
     */
    #[Computed]
    public function categoriesIndex(): Collection|array
    {
        return Category::query()
            ->where('show_index', 1)
            ->with([
                'post_relation' => fn($q) => $q
                    ->where('relationable_is_main', true)
                    ->whereHas('post', function ($q) {
                        $q->where('publish_status', PublishEnum::PUBLISHED->value);
                    })
                    ->orderByDesc(
                        Post::select('publish_date')
                            ->whereColumn('posts.id', 'post_relations.post_id')
                            ->latest('publish_date')
                            ->take(1)
                    )
                    ->limit(5),
                'post_relation.post.category.relationable',
                'post_relation.post.authors.relationable.files.file',
                'post_relation.post.author.relationable.files.file',
                'post_relation.post.files.file',
            ])
            ->orderBy('order')
            ->get();
    }

    /**
     * Most read posts (top 5 by views)
     */
    #[Computed]
    public function mostReadPosts()
    {
        return Post::select('id', 'slug', 'title', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->withCount('views')
            ->orderByDesc('views_count')
            ->with(['author.relationable', 'category.relationable'])
            ->limit(5)
            ->get();
    }

    /**
     * Advertisements
     */
    #[Computed]
    public function advertisements()
    {
        return Advertisement::where('publish_status', PublishEnum::PUBLISHED->value)
            ->get()
            ->groupBy('place');
    }
}
