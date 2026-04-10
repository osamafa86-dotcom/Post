<?php

namespace App\Livewire\Main\Almashhad;

use App\Models\Material;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class SinglePost extends Component
{
    #[Url]
    public $id;
    #[Url]
    public $slug;
public $type;
    public function mount()
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');
        $this->type = 'news';

        if ($this->post && !Session::has('viewed_post_' . $this->post->id)) {
            $now = \Carbon\Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', \Carbon\Carbon::today())
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
    public function post()
    {
        return Post::where('id', $this->id)->with(['files.file', 'tags.relationable', 'author.relationable', 'category.relationable'])->first();
    }

    #[Computed]
    public function lookAlikePosts()
    {
        return Post::whereHas('categories', function ($q) {
            $q->where('relationable_id', $this->post?->category?->relationable?->id);
        })->where('id', '!=', $this->post?->id)->with(['files.file', 'author.relationable', 'category.relationable'])->take(3)->get();
    }

    #[Computed]
    public function material()
    {
        return Material::where('id', $this->id)->with(['files.file', 'tags.relationable', 'category.relationable', 'presenter.relationable'])->first();
    }

    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.single-post');
    }
}
