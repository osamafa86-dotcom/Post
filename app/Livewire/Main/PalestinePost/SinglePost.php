<?php

namespace App\Livewire\Main\PalestinePost;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class SinglePost extends Component
{
    #[Url(keep: true)]
    public $id;

    #[Url(keep: true)]
    public $slug;

    public Post $post;
    public $sameCategoryPost;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.single-post', [
            'post' => $this->post
        ]);
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');

        $this->post = Post::with([
            'category.relationable',
            'author.relationable',
            'tags.relationable',
            'files.file'
        ])
            ->withTrashed()
            ->where(function ($query) {
                $query->where('slug', $this->slug)
                    ->where('id', $this->id);
            })
            ->firstOrFail();

        // Related posts depend only on the category, not on the current article, so cache
        // per-category (bounded 5-min staleness) and exclude the current post in PHP.
        $categoryId = $this->post->category?->id;
        $related = Cache::remember('post:related:cat:' . ($categoryId ?? 'none'), 300, fn () => Post::with(['category.relationable'])
            ->whereHas('category', fn ($query) => $query->where('relationable_id', $categoryId))
            ->orderByDesc('publish_date')
            ->take(6)
            ->get());
        $this->sameCategoryPost = $related->where('id', '!=', $this->id)->take(5)->values();

        if (!Session::has('viewed_post_' . $this->post->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->post->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_post_' . $this->post->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }
    }

    #[Computed]
    public function tags()
    {
        return $this->post->tags;
    }
}
