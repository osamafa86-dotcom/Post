<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListPendingActions extends Component
{
    use WithPagination;

    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public $perPage = 10;
    public bool $selectAllColumns = true;
    public array $columnVisibility = [];
    public bool $employeePendingPosts = false;

    public ?object $Post;
    public ?string $search = "";
    public string $sortField = "publish_date";
    public string $sortDirection = "desc";
    public bool $sortOnlySelected = false;

    public $category_ids = [];
    public $author_ids = [];

    public $state_filter = [
        'category_id' => [],
        'tag_id' => [],
        'author_id' => [],
        'search_text' => '',
        'from_date_add' => '',
        'to_date_add' => '',
        'from_date_news' => '',
        'to_date_news' => '',
    ];

    public $tagSearch = '';
    public $selectedTags = [];
    public $loadedTags = [];

    protected string $paginationTheme = 'bootstrap';

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-pending-actions');
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        $direction = strtolower($this->sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowed = ['id','title','publish_date','created_at','updated_at',
            'views','views_sum','category','author','tag','publisher','resource','type'];

        $query = Post::query()
            ->withoutGlobalScope('special_content_available')
            ->select('posts.*')
            // أعمدة مساعدة للترتيب
            ->addSelect([
                'main_category_title' => Category::query()
                    ->select('categories.category_title')
                    ->join('post_relations as prc', 'prc.relationable_id', '=', 'categories.id')
                    ->whereColumn('prc.post_id', 'posts.id')
                    ->where('prc.relationable_type', Category::class)
                    ->where('prc.relationable_is_main', 1)
                    ->limit(1),

                'main_author_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as pra', 'pra.relationable_id', '=', 'participants.id')
                    ->whereColumn('pra.post_id', 'posts.id')
                    ->where('pra.relationable_type', Participant::class)
                    ->where('pra.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::AUTHORS->value)
                    ->limit(1),

                'main_tag_name' => Tag::query()
                    ->select('tags.tag_name')
                    ->join('post_relations as prt', 'prt.relationable_id', '=', 'tags.id')
                    ->whereColumn('prt.post_id', 'posts.id')
                    ->where('prt.relationable_type', Tag::class)
                    ->where('prt.relationable_is_main', 1)
                    ->limit(1),

                'main_type_name' => Type::query()
                    ->select('types.type_name')
                    ->join('post_relations as prt', 'prt.relationable_id', '=', 'types.id')
                    ->whereColumn('prt.post_id', 'posts.id')
                    ->where('prt.relationable_type', Type::class)
                    ->where('prt.relationable_is_main', 1)
                    ->limit(1),
            ])
            ->where('posts.publish_status', PublishEnum::PENDING->value)
            ->withSum('views as views_sum', 'views_number');

        // Filter by selected posts if we're in "sort only selected" mode
        if ($this->sortOnlySelected && !empty($this->selected)) {
            $query->whereIn('posts.id', $this->selected);
        }

        // البحث
        $query->when($this->search, function ($q) {
            $term = '%'.$this->search.'%';
            $q->where(function ($w) use ($term) {
                $w->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhere('body', 'like', $term);
            });
        })
            ->when(!empty($this->state_filter['category_id']), function ($q) {
                $q->whereHas('category', function ($qq) {
                    $qq->whereHasMorph('relationable', [Category::class], function ($sub) {
                        $sub->whereIn('id', $this->state_filter['category_id']);
                    });
                });
            })
            ->when(!empty($this->selectedTags), function ($q) {
                $q->whereHas('tags', function ($qq) {
                    $qq->whereHasMorph('relationable', [Tag::class], function ($sub) {
                        $sub->whereIn('id', $this->selectedTags);
                    });
                });
            })
            ->with([
                'category.relationable', 'categories.relationable',
                'tag.relationable', 'tags.relationable',
                'author.relationable', 'authors.relationable',
                'type.relationable', 'types.relationable',
                'user',
            ])
            // --- الترتيب ---
            ->when(in_array($this->sortField, ['views', 'views_sum']), function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(views_sum, 0) $direction");
            })
            ->when($this->sortField === 'category', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_category_title, '') $direction");
            })
            ->when($this->sortField === 'author', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_author_name, '') $direction");
            })
            ->when($this->sortField === 'tag', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_tag_name, '') $direction");
            })
            ->when($this->sortField === 'type', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_type_name, '') $direction");
            })
            ->when(!in_array($this->sortField, $allowed), function ($q) {
                $q->orderBy('posts.' . $this->sortField, $this->sortDirection);
            });

        return $query->paginate($this->perPage);
    }

    public function sortBy($field): void
    {
        if (!empty($this->selected)) {
            $this->sortOnlySelected = true;
        } else {
            $this->sortOnlySelected = false;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->selectAll = false;
        $this->delete_text = '';
        $this->resetPage();
    }

    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up';
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }

    public function toggleSortMode(): void
    {
        if (!empty($this->selected)) {
            $this->sortOnlySelected = !$this->sortOnlySelected;
            $this->resetPage();
        }
    }

    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->sortOnlySelected = false;
        $this->resetPage();
    }

    public function mount()
    {
        $this->columnVisibility = array_filter(
            config('features.post_columns', []),
            fn ($value) => $value === true
        );

        $this->loadInitialTags();

        if (auth()->check()) {
            $savedColumns = auth()->user()->getPreference('pending_posts_columns', []);
            if (!empty($savedColumns)) {
                $allowedColumns = array_keys($this->columnVisibility);
                $filteredSaved = array_intersect_key(
                    $savedColumns,
                    array_flip($allowedColumns)
                );
                $this->columnVisibility = array_merge($this->columnVisibility, $filteredSaved);
            }
        }
    }

    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('pending_posts_columns', $this->columnVisibility);
        }
    }

    public function updatedColumnVisibility($value, $key)
    {
        if (!in_array(false, $this->columnVisibility)) {
            $this->selectAllColumns = true;
        } else {
            $this->selectAllColumns = false;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('pending_posts_columns', $this->columnVisibility);
        }
    }

    public function changeStatus(Post $post): void
    {
        $this->Post = $post;
    }

    public function confirmChangeStatusPublish(): void
    {
        $this->Post->update([
            'publish_status' => PublishEnum::PUBLISHED->value
        ]);
        $this->Post->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => __('validation.change_post_status'),
        ]);
        $this->resetSelection();
    }

    public function confirmChangeStatus(): void
    {
        $this->Post->update(['publish_status' => PublishEnum::DRAFT->value]);
        $this->Post->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => __('validation.change_post_status'),
        ]);
        $this->resetSelection();
    }

    public function bulkConfirmChangeStatus(): void
    {
        $posts = Post::whereIn('id', $this->selected)->get();

        foreach ($posts as $post) {
            $post->update(['publish_status' => PublishEnum::DRAFT->value]);
            $post->user_logs()->create([
                'user_id' => Auth::id(),
                'action_status' => __('validation.change_post_status'),
            ]);
        }

        $this->dispatch('hide_bulk_post_change_status_modal');
        $this->resetSelection();
    }

    public function bulkConfirmChangeAuthor(): void
    {
        $posts = Post::whereIn('id', $this->selected)->get();

        foreach ($posts as $post) {
            $post->authors()->whereHasMorph('relationable', [Participant::class], function ($query) {
                $query->where('type', ParticipantTypeEnum::AUTHORS->value);
            })->forceDelete();

            if (!empty($this->author_ids)) {
                foreach ($this->author_ids as $key => $authorId) {
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $authorId,
                        'relationable_type' => Participant::class,
                        'relationable_is_main' => $key == 0 ? 1 : 0,
                    ]);
                }
            }
        }

        $this->author_ids = [];
        $this->dispatch('hide_bulk_post_change_author_modal');
        $this->resetSelection();
    }

    public function bulkConfirmChangeCategory(): void
    {
        $posts = Post::whereIn('id', $this->selected)->get();

        foreach ($posts as $post) {
            $post->categories()->forceDelete();
            if (!empty($this->category_ids)) {
                foreach ($this->category_ids as $key => $row) {
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $row,
                        'relationable_type' => Category::class,
                        'relationable_is_main' => $key == 0 ? 1 : 0,
                    ]);
                }
            }
        }

        $this->category_ids = [];
        $this->dispatch('hide_bulk_post_change_category_modal');
        $this->resetSelection();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function delete(Post $post): void
    {
        $this->Post = $post;
        $this->dispatch('show_delete_modal');
    }

    public function confirmDelete(): void
    {
        $this->Post?->sortable?->delete();
        $this->Post->delete();

        $this->Post->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);

        $this->dispatch('hide_delete_modal');
        $this->resetSelection();
    }

    public function updatedSelectAll($value): void
    {
        $postIds = collect($this->posts->items())->pluck('id')->all();
        if ($value) {
            $this->selected = array_unique(array_merge($this->selected, $postIds));
        } else {
            $this->selected = array_diff($this->selected, $postIds);
        }
        $this->checkSelectAll();
        $this->sortOnlySelected = false;
    }

    public function updatedSelected(): void
    {
        $this->checkSelectAll();
        $this->sortOnlySelected = false;
    }

    public function updatedPage(): void
    {
        $this->selectAll = false;
        $this->checkSelectAll();
    }

    private function checkSelectAll(): void
    {
        $postIds = collect($this->posts->items())->pluck('id')->all();
        $this->selectAll = !array_diff($postIds, $this->selected);
    }

    public function deleteSelected(): void
    {
        if (!empty($this->selected)) {
            $this->dispatch('show_delete_selected_modal');
        } else {
            $this->dispatch('no-data');
        }
    }

    public function confirmDeleteSelected(): void
    {
        if ($this->delete_text) {
            if ($this->delete_text == 'Delete') {
                $posts = Post::whereIn('id', $this->selected)->get();

                foreach ($posts as $post) {
                    $post->delete();
                    $post->user_logs()->create([
                        'user_id' => Auth::id(),
                        'action_status' => \App\Enums\ActionsEnum::DELETE->value,
                    ]);
                }

                $this->dispatch('hide_delete_selected_modal');
                $this->resetPage();
                $this->resetSelection();
            } else {
                session()->flash('error', __('messages.error_delete_text'));
            }
        } else {
            session()->flash('error', __('messages.empty_delete_text'));
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::select('id', 'category_title')->where('category_type', CategoryTypeEnum::NEWS->value)->get();
    }

    #[Computed]
    public function authors()
    {
        return Participant::select('id', 'name')->where('type', ParticipantTypeEnum::AUTHORS)->get();
    }

    #[Computed]
    public function loadInitialTags()
    {
        $this->loadedTags = Tag::where('tag_type', TagTypeEnum::NEWS->value)
            ->orderBy('tag_name')
            ->limit(10)
            ->get();
    }

    public function searchTags()
    {
        $this->loadedTags = Tag::when($this->tagSearch, function ($query) {
            $query->where('tag_name', 'like', '%' . $this->tagSearch . '%');
        })
            ->where('tag_type', TagTypeEnum::NEWS->value)
            ->orderBy('tag_name')
            ->limit(20)
            ->get();
    }

    public function removeTag($tagId)
    {
        $this->selectedTags = array_filter($this->selectedTags, function ($id) use ($tagId) {
            return $id != $tagId;
        });
    }

    public function updatedTagSearch(): void
    {
        $this->searchTags();
    }

    public function updatedStateFilter(): void
    {
        foreach (['category_id', 'tag_id', 'author_id'] as $key) {
            $this->state_filter[$key] = array_filter($this->state_filter[$key] ?? []);
        }
        $this->resetPage();
    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}
