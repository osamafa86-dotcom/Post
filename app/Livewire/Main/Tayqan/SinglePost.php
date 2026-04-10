<?php

namespace App\Livewire\Main\Tayqan;

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

        $this->post = Post::where('id', $this->id)
            ->with([
                'files.file', 'tags.relationable', 'authors.relationable.files.file',
                'authors.relationable.participant_social_media', 'publishers.relationable.files.file',
                'resources.relationable.files.file', 'category.relationable'
            ])
            ->firstOrFail();

        if (!Session::has('viewed_post_' . $this->post->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
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
    public function lookAlikePosts()
    {
        $currentPost = $this->post;

        if (!$currentPost) {
            return collect();
        }

        // Extract keywords from title (remove common words)
        $keywords = $this->extractKeywords($currentPost->title);

        return Post::where('id', '!=', $currentPost->id)
            ->where(function($query) use ($keywords, $currentPost) {
                // Search for each keyword
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'LIKE', "%{$keyword}%");
                }
                // Fallback to same category
                $query->orWhereHas('categories', function ($q) use ($currentPost) {
                    $q->where('relationable_id', $currentPost->category?->relationable?->id);
                });
            })
            ->with(['files.file', 'author.relationable.files.file', 'category.relationable'])
            ->orderByRaw($this->getRelevanceScore($keywords))
            ->orderByDesc('publish_date')
            ->take(3)
            ->get();
    }

    private function extractKeywords($title)
    {
        // Remove common Arabic stop words and split into keywords
        $stopWords = ['في', 'من', 'إلى', 'على', 'أن', 'ما', 'هذا', 'هذه', 'كان', 'يكون'];
        $words = preg_split('/\s+/', $title);

        return array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && mb_strlen($word) > 2;
        });
    }

    private function getRelevanceScore($keywords)
    {
        $score = "CASE ";
        foreach ($keywords as $index => $keyword) {
            $weight = count($keywords) - $index; // Higher weight for first keywords
            $score .= "WHEN title LIKE '%{$keyword}%' THEN {$weight} ";
        }
        $score .= "ELSE 0 END";

        return $score;
    }
    #[Layout('components.layouts.main.tayqan.main')]
    public function render()
    {
        return view('livewire.main.tayqan.single-post');
    }
}
