<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllPosts extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    #[Url(as: 'category')]
    public string $category_filter = '';

    #[Url(as: 'tag')]
    public string $tag_filter = '';

    #[Url]
    public string $author_filter = '';

    public string $sort_tag = '';
    public string $view_mode = 'grid';

    // Current category/tag info for page title
    public ?string $pageTitle = null;
    public ?string $pageType = null; // 'category', 'tag', or null (all)

    #[Layout('components.layouts.main.fimed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.fimed.all-posts');
    }

    public function mount(): void
    {
        // Read from query params: ?category=فلسطين or ?tag=غزة
        $catParam = request('category', '');
        $tagParam = request('tag', '');

        if ($catParam) {
            // Find category by name and set filter by ID
            $cat = Category::where('category_title', $catParam)->first();
            if ($cat) {
                $this->category_filter = (string) $cat->id;
                $this->pageTitle = $cat->category_title;
                $this->pageType = 'category';
            }
        }

        if ($tagParam) {
            $tag = Tag::where('tag_name', $tagParam)->first();
            if ($tag) {
                $this->tag_filter = (string) $tag->id;
                $this->pageTitle = $tag->tag_name;
                $this->pageType = 'tag';
            }
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAuthorFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->category_filter = '';
        $this->tag_filter = '';
        $this->author_filter = '';
        $this->sort_tag = '';
        $this->pageTitle = null;
        $this->pageType = null;
        $this->resetPage();
    }

    public function setTag(string $tag): void
    {
        $this->sort_tag = $this->sort_tag === $tag ? '' : $tag;
        $this->resetPage();
    }

    #[Computed]
    public function articles()
    {
        $query = Post::select('id', 'slug', 'title', 'description', 'publish_date', 'news_type')
            ->where('publish_status', PublishEnum::PUBLISHED->value);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%")
                    ->orWhere('description', 'LIKE', "%{$this->search}%");
            });
        }

        if ($this->category_filter) {
            $query->whereHas('category', function ($q) {
                $q->where('relationable_id', $this->category_filter);
            });
        }

        if ($this->tag_filter) {
            $query->whereHas('tags', function ($q) {
                $q->where('relationable_id', $this->tag_filter);
            });
        }

        if ($this->author_filter) {
            $query->whereHas('author', function ($q) {
                $q->where('relationable_id', $this->author_filter);
            });
        }

        if ($this->sort_tag === 'most_read') {
            $query->withCount('views')->orderByDesc('views_count');
        } elseif ($this->sort_tag === 'featured') {
            $query->where('news_type', \App\Enums\NewsTypeEnum::MAIN_NEWS->value);
        } else {
            $query->orderByDesc('publish_date');
        }

        return $query->with(['files.file', 'author.relationable.files.file', 'category.relationable'])
            ->paginate(6);
    }

    #[Computed]
    public function totalArticles()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)->count();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('category_title')->get();
    }

    #[Computed]
    public function authors()
    {
        return Participant::where('type', ParticipantTypeEnum::AUTHORS->value)
            ->orderBy('name')
            ->get();
    }
}
