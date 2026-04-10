<?php

namespace App\Livewire\Dashboard\Posts;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\PostSpecialContent;
use App\Models\SortData;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListPosts extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $perPage = 10;
    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public $author_ids= [];
    protected string $paginationTheme = 'bootstrap';
    public bool $selectAllColumns = true;
    public bool $employeePendingPosts = false;
    public $tagSearch = '';
    public $selectedTags = [];
    public $loadedTags = [];
    public ?object $Post;
    public ?string $search = "";
    public string $sortField = "publish_date";
    public string $sortDirection = "desc";
    public bool $sortOnlySelected = false;
    public bool $useFulltextSearch = true; // Use FULLTEXT for better performance

    public $type, $id;

    public $file_excel;

    public $category_ids= [];

    public array $columnVisibility = [];
    public array $state_filter = [
        'category_id' => [],
        'tag_id' => [],
        'author_id' => [],
        'publisher_id' => [],
        'resource_id' => [],
    ];

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.posts.list-posts');
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        if ($this->employeePendingPosts) {
            return Auth::user()->employeesPendingPosts()->latest()
                ->withoutGlobalScope(\App\Scope\LanguageScope::class)
                ->with([
                    'category.relationable',
                    'tag.relationable',
                    'author.relationable',
                    'publisher.relationable',
                    'resource.relationable',
                    'type.relationable',
                    'user:id,first_name,last_name',
                    'post_special_content:id,post_id,available_date',
                    'translations:id,lang,translation_group,title',
                ])
                ->paginate($this->perPage);
        }

        $direction = strtolower($this->sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowed = ['id','title','publish_date','created_at','updated_at',
            'views','views_sum','category','author','tag','publisher','resource','type','available_date'];

        $query = Post::query()
            ->withoutGlobalScope('special_content_available')
            ->withoutGlobalScope(\App\Scope\LanguageScope::class)
            ->select('posts.*')
            ->where('posts.publish_status', PublishEnum::PUBLISHED->value);

        // Only add subqueries if we're sorting by them (performance optimization)
        $needsSubqueries = in_array($this->sortField, ['category', 'author', 'tag', 'publisher', 'resource', 'type']);

        if ($needsSubqueries) {
            $query->addSelect([
                // التصنيف الأساسي
                'main_category_title' => Category::query()
                    ->select('categories.category_title')
                    ->join('post_relations as prc', 'prc.relationable_id', '=', 'categories.id')
                    ->whereColumn('prc.post_id', 'posts.id')
                    ->where('prc.relationable_type', Category::class)
                    ->where('prc.relationable_is_main', 1)
                    ->limit(1),

                // الكاتب الأساسي
                'main_author_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as pra', 'pra.relationable_id', '=', 'participants.id')
                    ->whereColumn('pra.post_id', 'posts.id')
                    ->where('pra.relationable_type', Participant::class)
                    ->where('pra.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::AUTHORS->value)
                    ->limit(1),

                // التاج الأساسي
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
                'main_publisher_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as prp', 'prp.relationable_id', '=', 'participants.id')
                    ->whereColumn('prp.post_id', 'posts.id')
                    ->where('prp.relationable_type', Participant::class)
                    ->where('prp.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::PUBLISHERS->value)
                    ->limit(1),

                // المصدر
                'main_resource_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as prr', 'prr.relationable_id', '=', 'participants.id')
                    ->whereColumn('prr.post_id', 'posts.id')
                    ->where('prr.relationable_type', Participant::class)
                    ->where('prr.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::RESOURCES->value)
                    ->limit(1),
            ]);
        }

        $query->withSum('views as views_sum', 'views_number');

        if ($this->sortOnlySelected && !empty($this->selected)) {
            $query->whereIn('posts.id', $this->selected);
        }

        $query->when($this->search, function ($q) {
            $searchTerm = trim($this->search);

            if (is_numeric($searchTerm)) {
                $q->where('posts.id', $searchTerm);
            }
            // Use FULLTEXT search for better performance (requires MySQL 5.6+)
            elseif ($this->useFulltextSearch && strlen($searchTerm) >= 3) {
                // FULLTEXT search on title and description only (faster)
                $q->whereRaw(
                    "MATCH(title, description) AGAINST(? IN BOOLEAN MODE)",
                    [$searchTerm . '*']
                )->orWhere('slug', 'like', '%' . $searchTerm . '%');
            }
            // Fallback to LIKE search for short terms or if FULLTEXT disabled
            else {
                $term = '%' . $searchTerm . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                    // Avoid searching in body field - it's very slow
                });
            }
        })
            // فلاتر التصنيف/التاج - محسّنة باستخدام JOIN بدلاً من whereHas
            ->when(!empty($this->state_filter['category_id']), function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(\DB::raw(1))
                        ->from('post_relations')
                        ->whereColumn('post_relations.post_id', 'posts.id')
                        ->where('post_relations.relationable_type', Category::class)
                        ->whereIn('post_relations.relationable_id', $this->state_filter['category_id'])
                        ->whereNull('post_relations.deleted_at');
                });
            })
            ->when(!empty($this->selectedTags), function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(\DB::raw(1))
                        ->from('post_relations')
                        ->whereColumn('post_relations.post_id', 'posts.id')
                        ->where('post_relations.relationable_type', Tag::class)
                        ->whereIn('post_relations.relationable_id', $this->selectedTags)
                        ->whereNull('post_relations.deleted_at');
                });
            })
            // Eager loads - تحميل فقط العلاقات الأساسية المطلوبة
            ->with([
                'category.relationable',
                'tag.relationable',
                'author.relationable',
                'publisher.relationable',
                'resource.relationable',
                'type.relationable',
                'user:id,first_name,last_name', // Select only needed columns
                'post_special_content:id,post_id,available_date',
                'translations:id,lang,translation_group,title',
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
            ->when($this->sortField === 'publisher', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_publisher_name, '') $direction");
            })
            ->when($this->sortField === 'resource', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_resource_name, '') $direction");
            })
            ->when($this->sortField === 'tag', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_tag_name, '') $direction");
            })
            ->when($this->sortField === 'type', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(main_type_name, '') $direction");
            })
            ->when($this->sortField === 'publish_date', function ($q) use ($direction) {
                $q->orderByRaw("COALESCE(publish_date, '') $direction");
            })
            ->when($this->sortField === 'available_date', function ($q) use ($direction) {
                $q->leftJoin('post_special_contents', 'posts.id', '=', 'post_special_contents.post_id')
                    ->orderByRaw("COALESCE(post_special_contents.available_date, '') $direction");
            })
            ->when(in_array($this->sortField, ['id', 'title', 'publish_date', 'created_at', 'updated_at']), function ($q) {
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
    public function getFilterSummary()
    {
        // Get the current paginated posts collection
        $currentPagePosts = $this->posts->getCollection();

        // Check if any filters are active
        $isFiltered = $this->search ||
            !empty(array_filter($this->state_filter['category_id'])) ||
            !empty($this->selectedTags) ||
            !empty(array_filter($this->state_filter['author_id']));

        return [
            'total' => $this->posts->total(), // Total posts in database that match filters
            'current_count' => $currentPagePosts->count(), // Posts on current page
            'isFiltered' => $isFiltered,
            'has_active_filters' => $isFiltered
        ];
    }
    public function clearAllFilters()
    {
        $this->search = "";
        $this->selectedTags = [];
        $this->state_filter = [
            'category_id' => [],
            'tag_id' => [],
            'author_id' => [],
            'publisher_id' => [],
            'resource_id' => [],
        ];
        $this->resetPage();
    }
    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up'; // Default icon when not sorted
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }

    public function mount()
    {
        $this->columnVisibility = array_filter(
            config('features.post_columns', []),
            fn ($value) => $value === true
        );
        if ($this->type == 'category') {
            $this->state_filter['category_id'][] = $this->id;
        }
        $this->loadInitialTags();
        if (auth()->check()) {
            $savedColumns = auth()->user()->getPreference('post_columns', []);

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
    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->sortOnlySelected = false;
        $this->resetPage();
    }
    public function toggleSortMode(): void
    {
        if (!empty($this->selected)) {
            $this->sortOnlySelected = !$this->sortOnlySelected;
            $this->resetPage();
        }
    }
    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        // Save preferences
        if (auth()->check()) {
            auth()->user()->setPreference('post_columns', $this->columnVisibility);
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
            auth()->user()->setPreference('post_columns', $this->columnVisibility);
        }
    }


    #[Renderless]
    public function changeStatus(Post $post): void
    {
        $this->Post = $post;
    }

    public function publish(Post $post): void
    {
        $post->update([
            'publish_status' => PublishEnum::PUBLISHED->value
        ]);
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

    public function bulkConfirmChangeStatus(): void
    {
        $posts = Post::whereIn('id', $this->selected)->get();

        $this->selected = [];
        $this->selectAll = false;
        $this->delete_text = '';

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

    public function bulkConfirmChangeCategory()
    {
        $posts = Post::whereIn('id', $this->selected)->get();

        $this->selected = [];
        $this->selectAll = false;
        $this->delete_text = '';

        foreach ($posts as $post) {
            $post->categories()->forceDelete();
            if (!empty($this->category_ids)) {
                foreach ($this->category_ids as $key => $row)
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $row,
                        'relationable_type' => Category::class,
                        'relationable_is_main' => $key == 0 ? 1 : 0,
                    ]);
            }

        }
        $this->category_ids = '';
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


    public function updatedStateFilter(): void
    {
        foreach (['category_id', 'tag_id', 'author_id'] as $key) {
            $this->state_filter[$key] = array_filter($this->state_filter[$key] ?? []);
        }

        $this->resetPage(); // لضمان إعادة التصفية بعد التغيير
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

    public function removeTag($tagId)
    {
        $this->selectedTags = array_filter($this->selectedTags, function ($id) use ($tagId) {
            return $id != $tagId;
        });
    }

    public function loadMoreTags()
    {
        $currentCount = $this->loadedTags->count();

        $moreTags = Tag::when($this->tagSearch, function ($query) {
            $query->where('tag_name', 'like', '%' . $this->tagSearch . '%');
        })
            ->where('tag_type', TagTypeEnum::NEWS->value)
            ->orderBy('tag_name')
            ->skip($currentCount)
            ->limit(10)
            ->get();

        $this->loadedTags = $this->loadedTags->merge($moreTags);
    }

    public function confirmDeleteSelected(): void
    {
        if ($this->delete_text) {
            if ($this->delete_text == 'Delete') {
                $posts = Post::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

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


    public function searchTags()
    {
        $this->validate(['tagSearch' => 'nullable|string|max:255']);

        $this->loadedTags = Tag::when($this->tagSearch, function ($query) {
            $query->where('tag_name', 'like', '%' . $this->tagSearch . '%');
        })
            ->where('tag_type', TagTypeEnum::NEWS->value)
            ->orderBy('tag_name')
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function loadInitialTags()
    {
        $this->loadedTags = Tag::where('tag_type', TagTypeEnum::NEWS->value)
            ->orderBy('tag_name')
            ->limit(10)
            ->get();
    }


    #[Computed]
    public function authors()
    {
        return Participant::select('id', 'name')->where('type', ParticipantTypeEnum::AUTHORS)->get();
    }


    public function updatedTagSearch(): void
    {
        $this->searchTags();
    }

    public function filterResults()
    {
        $this->posts = Post::select('posts.*')
            ->where('posts.publish_status', PublishEnum::PUBLISHED->value)
            ->when(!empty($this->state_filter['search_text']), function ($query) {
                $searchText = $this->state_filter['search_text'];
                $query->where(function ($q) use ($searchText) {
                    $q->where('title', 'like', '%' . $searchText . '%')
                        ->orWhere('description', 'like', '%' . $searchText . '%')
                        ->orWhere('body', 'like', '%' . $searchText . '%')
                        ->orWhere('posts.id', 'like', '%' . $searchText . '%'); // Add ID search
                });

                // Also add exact ID search if numeric
                if (is_numeric($searchText)) {
                    $query->orWhere('posts.id', $searchText);
                }
            })
            ->when(!empty($this->state_filter['from_date_add']) && !empty($this->state_filter['to_date_add']), function ($query) {
                $query->whereBetween('created_at', [$this->state_filter['from_date_add'] . ' 00:00:00', $this->state_filter['to_date_add'] . ' 23:59:59']);
            })
            ->when(!empty($this->state_filter['from_date_news']) && !empty($this->state_filter['to_date_news']), function ($query) {
                $query->whereBetween('publish_date', [$this->state_filter['from_date_add'] . ' 00:00:00', $this->state_filter['to_date_add'] . ' 23:59:59']);
            })
            ->when(!empty($this->state_filter['category_id']), function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->whereIn('id', $this->state_filter['category_id']);
                        }
                    );
                });
            })
            ->when(!empty($this->selectedTags), function ($query) {
                $query->whereHas('tags', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->whereIn('id', $this->selectedTags);
                        }
                    );
                });

            })
            ->with([
                'category.relationable',
                'tag.relationable',
                'author.relationable',
                'publisher.relationable',
                'resource.relationable',
                'type.relationable',
                'user:id,first_name,last_name',
                'post_special_content:id,post_id,available_date',
            ])
            ->orderBy('posts.' . $this->sortField, $this->sortDirection)
            ->latest()
            ->paginate(10);
    }
    public function exportPosts()
    {

        $file_name = 'posts' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new PostsExport, $file_name);
    }

    /**
     * @throws ValidationException
     */
    public function importPosts()
    {
        $validatedData = Validator::make(
            ['file_excel' => $this->file_excel],
            ['file_excel' => 'required|mimes:xlsx,csv,xls']
        )->validate();

        Excel::import(new PostsImport, $validatedData['file_excel']);

    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }

    public function generatePreview($postId): void
    {
        $post = Post::findOrFail($postId);
        $token = $this->generatePreviewToken($post);

        $this->dispatch('preview-token-generated', postId: $postId, token: $token);
    }

    public function generatePreviewToken(Post $post): string
    {
        $token = Str::random(40);

        $post->load([
            'category',
            'category.relationable',
            'tags.relationable',
            'author.relationable',
            'files.file'
        ]);

        $previewData = [
            'post_id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'description' => $post->description,
            'image_name' => $post->files->first()?->file?->path,
            'image_alt' => $post->image_alt ?? $post->title,
            'publish_date' => $post->publish_date,
            'category' => $post->category?->relationable?->category_title,
            'category_id' => [$post->category?->relationable?->id],
            'tags' => $post->tags->pluck('relationable.tag_name')->toArray(),
            'author' => $post->author?->relationable?->name,
            'author_id' => [$post->author?->relationable?->id],
            'author_work' => $post->author?->relationable?->work,
            'author_description' => $post->author?->relationable?->description,
        ];

        Cache::put("post-preview:$token", $previewData, now()->addHours(24));
        return $token;
    }


}

