<?php

namespace App\Livewire\Main\Almaidan;

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

    #[Layout('components.layouts.main.almaidan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almaidan.single-post', [
            'post' => $this->post
        ]);
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');

        $withRelations = [
            'category',
            'category.relationable',
            'tags.relationable',
            'author.relationable',
            'files.file'
        ];



        $this->post = Post::with($withRelations)
            ->withTrashed()
            ->where(function ($query) {
                $query->where('id', $this->id)
                      ->where('slug', $this->slug);
            })
            ->firstOrFail();

        $this->randomPost = Post::with($withRelations)
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $this->sameCategoryPost = Post::with($withRelations)
            ->where('id', '!=', $this->id)
            ->whereHas('category', function ($query) {
                $query->where('relationable_id', $this->post->category?->relationable_id);
            })
            ->take(4)
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
