<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\PublishEnum;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class TagNews extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public $tag;

    #[Layout('components.layouts.main.fimed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.tag-news');
    }

    public function mount($tag_name): void
    {
        $this->tag = Tag::where('tag_name', $tag_name)
            ->orWhere('tag_name', 'LIKE', "%{$tag_name}%")
            ->firstOrFail();
    }

    #[Computed]
    public function articles()
    {
        return Post::select('id', 'slug', 'title', 'description', 'publish_date', 'news_type')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereHas('tags', function ($q) {
                $q->where('relationable_id', $this->tag->id);
            })
            ->orderByDesc('publish_date')
            ->with(['files.file', 'author.relationable.files.file', 'category.relationable'])
            ->paginate(6);
    }
}
