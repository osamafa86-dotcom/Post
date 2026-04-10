<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Participant;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Writer extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $author;
    public string $search = '';
    public string $category_filter = '';
    public string $sort_order = 'newest';

    #[Layout('components.layouts.main.fimed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.writer');
    }

    public function mount($author_id): void
    {
        $this->author = Participant::withoutGlobalScopes()
            ->where('id', $author_id)
            ->with(['files.file', 'participant_social_media'])
            ->firstOrFail();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortOrder(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function authorImage()
    {
        return $this->author->files?->file;
    }

    #[Computed]
    public function posts()
    {
        $query = Post::whereHas('author', function ($q) {
            $q->where('relationable_id', $this->author->id);
        })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable', 'files.file']);

        if ($this->search) {
            $query->where('title', 'LIKE', "%{$this->search}%");
        }

        if ($this->category_filter) {
            $query->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->category_filter);
            });
        }

        if ($this->sort_order === 'oldest') {
            $query->orderBy('publish_date');
        } elseif ($this->sort_order === 'most_viewed') {
            $query->withCount('views')->orderByDesc('views_count');
        } else {
            $query->orderByDesc('publish_date');
        }

        return $query->paginate(6);
    }

    #[Computed]
    public function totalPosts(): int
    {
        return Post::whereHas('author', function ($q) {
            $q->where('relationable_id', $this->author->id);
        })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->count();
    }

    #[Computed]
    public function authorCategories()
    {
        return Post::whereHas('author', function ($q) {
            $q->where('relationable_id', $this->author->id);
        })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with('category.relationable')
            ->get()
            ->pluck('category')
            ->flatten()
            ->pluck('relationable')
            ->filter()
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function otherAuthors()
    {
        return Participant::where('type', ParticipantTypeEnum::AUTHORS->value)
            ->where('id', '!=', $this->author->id)
            
            ->inRandomOrder()
            ->with(['files.file'])
            ->limit(4)
            ->get();
    }
}
