<?php

namespace App\Livewire\Main\Alarouri;

use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class SinglePost extends Component
{
    #[Url]
    public $id;
    #[Url]
    public $slug;
    public Post $post;

    public function mount()
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');

        $this->post = Post::with([
            'category.relationable',
            'tags.relationable',
            'author.relationable.files.file',
            'files.file'
        ])
            ->where(function ($q) {
                $q->where('slug', $this->slug)
                    ->where('id', $this->id);
            })
            ->firstOrFail();

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
    public function post()
    {
        return Post::where('id', $this->id)
            ->with([
                'files.file', 'tags.relationable', 'authors.relationable.files.file',
                'authors.relationable.participant_social_media', 'publishers.relationable.files.file',
                'resources.relationable.files.file', 'category.relationable'
            ])
            ->first();
    }

    #[Computed]
    public function relatedPosts()
    {
        $categoryId = $this->post->category?->relationable_id;

        return Post::where('id', '!=', $this->id)
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('category', function ($q) use ($categoryId) {
                    $q->where('relationable_id', $categoryId);
                });
            })
            ->with(['files.file', 'category.relationable', 'authors.relationable'])
            ->orderBy('publish_date', 'desc')
            ->limit(2)
            ->get();
    }

    #[Computed]
    public function tableOfContents()
    {
        $content = $this->post->body;
        $toc = [];

        if (empty($content)) {
            return $toc;
        }

        preg_match_all('/<h([2-3])[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h\1>/i', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $level = (int) $match[1];
            $id = $match[2];
            $title = strip_tags($match[3]);

            $toc[] = [
                'level' => $level,
                'id' => $id,
                'title' => $title
            ];
        }

        return $toc;
    }
    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.single-post');
    }
}
