<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\CategoryTypeEnum;
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
    public $randomPost;
    public $sameCategoryPost;

    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.single-post', [
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

        $this->randomPost = Post::whereNot('id', $this->id)
            ->with(['files.file'])
            ->whereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('category_type', CategoryTypeEnum::NEWS->value);
                    }
                );
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $this->sameCategoryPost = Post::whereNot('id', $this->id)
            ->with(['files.file'])
            ->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->post->category?->relationable?->id);
            })
            ->limit(3)
            ->get();

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
            }            Session::put('viewed_post_' . $this->post->id, [
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
