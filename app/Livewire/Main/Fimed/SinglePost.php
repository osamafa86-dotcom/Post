<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\PublishEnum;
use App\Models\Post;
use App\Models\Tag;
use App\Models\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SinglePost extends Component
{
    public $post;
    public ?int $postId = null;

    #[Layout('components.layouts.main.fimed.main')]
    public function render(): Factory|Application|ViewContract|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.single-post');
    }

    public function mount(): void
    {
        $this->postId = request('id');

        $this->post = Post::where('id', $this->postId)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with([
                'files.file',
                'author.relationable.files.file',
                'category.relationable',
                'tags.relationable',
            ])
            ->firstOrFail();

        // Record view
        View::updateOrCreate(
            ['viewable_type' => Post::class, 'viewable_id' => $this->post->id],
            ['views_number' => DB::raw('views_number + 1'), 'last_viewed_at' => now()]
        );
    }

    #[Computed]
    public function postCategory()
    {
        return $this->post->category?->relationable;
    }

    #[Computed]
    public function postAuthor()
    {
        return $this->post->author?->relationable;
    }

    #[Computed]
    public function postAuthorImage()
    {
        return $this->post->author?->relationable?->files?->file;
    }

    #[Computed]
    public function featuredImage()
    {
        return $this->post->files?->where('model_column', 'image')->first()?->file;
    }

    #[Computed]
    public function postTags()
    {
        return $this->post->tags?->map(fn($rel) => $rel->relationable)->filter();
    }

    #[Computed]
    public function relatedPosts()
    {
        $categoryId = $this->postCategory?->id;

        if (!$categoryId) return collect();

        return Post::select('id', 'slug', 'title', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('id', '!=', $this->post->id)
            ->whereHas('category', function ($q) use ($categoryId) {
                $q->where('relationable_id', $categoryId);
            })
            ->orderByDesc('publish_date')
            ->with(['files.file'])
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function mostReadPosts()
    {
        return Post::select('id', 'slug', 'title')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('id', '!=', $this->post->id)
            ->withCount('views')
            ->orderByDesc('views_count')
            ->limit(4)
            ->get();
    }

    #[Computed]
    public function popularTags()
    {
        return Tag::limit(9)->get();
    }

    #[Computed]
    public function prevPost()
    {
        return Post::select('id', 'slug', 'title')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('publish_date', '<', $this->post->publish_date)
            ->orderByDesc('publish_date')
            ->first();
    }

    #[Computed]
    public function nextPost()
    {
        return Post::select('id', 'slug', 'title')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('publish_date', '>', $this->post->publish_date)
            ->orderBy('publish_date')
            ->first();
    }

    #[Computed]
    public function readTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->post->body ?? ''));
        return max(1, (int) ceil($wordCount / 200));
    }
}
