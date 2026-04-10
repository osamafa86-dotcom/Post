<?php

namespace App\Livewire\Main\Hongora;

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class SinglePost extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;

    public Post $post;
    public $randomPosts;
    public $relatedPosts;
    public $previousPost;
    public $nextPost;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.single-post', [
            'post' => $this->post
        ]);
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');

        $this->post = Post::with([
            'category.relationable',
            'tags.relationable',
            'author.relationable',
            'files.file'
        ])
            ->withTrashed()
            ->where(function ($q) {
                $q->where('slug', $this->slug)
                    ->where('id', $this->id);
            })
            ->firstOrFail();

        $this->randomPosts = Post::with(['category.relationable', 'files.file'])
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $this->relatedPosts = Post::with(['category.relationable', 'files.file'])
            ->where('id', '!=', $this->id)
            ->whereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('id', $this->post->category?->id);
                    }
                );
            })
            ->limit(4)
            ->get();

        $this->previousPost = Post::where('id', '<', $this->id)
            ->with(['files.file'])
            ->orderBy('id', 'desc')
            ->first();

        $this->nextPost = Post::where('id', '>', $this->id)
            ->with(['files.file'])
            ->orderBy('id', 'asc')
            ->first();

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
        return $this->post->tags()->get()->pluck('relationable');
    }
}
