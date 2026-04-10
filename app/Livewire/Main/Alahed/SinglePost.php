<?php

namespace App\Livewire\Main\Alahed;

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class SinglePost extends Component
{

    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;
    public object $Post;

    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $lastPosts = Post::with(['category.relationable', 'tags.relationable', 'files.file'])
            ->whereNot('id', $this->Post?->id)
            ->latest()
            ->take(6)
            ->get();

        $similarPosts = Post::with(['category.relationable', 'tags.relationable', 'files.file'])
            ->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->Post?->category?->id);
            })
            ->whereNot('id', $this->Post?->id)
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.main.alahed.single-post', [
            'lastPosts' => $lastPosts,
            'similarPosts' => $similarPosts,
        ]);
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');


        $post = Post::with(['category.relationable', 'tags.relationable', 'files.file'])->where(function ($q) {
            $q->where('slug', $this->slug)
                ->where('id', $this->id);
        })->first();
        $this->Post = $post;

        if (!Session::has('viewed_post_' . $this->Post->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->Post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->Post->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_post_' . $this->Post->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }
    }
}
