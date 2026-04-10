<?php

namespace App\Livewire\Dashboard\Posts;

use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
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
use Illuminate\Support\Facades\Lang;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListDeletedPosts extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?object $Post;
    public $perPage = 10;
    public ?string $search = "";
    public string $sortField = "publish_date";
    public string $sortDirection = "desc";
    public bool $selectAllColumns = true;
    public $status;
    public $selected = [];
    public $selectAll = false;
    public $delete_text ;
    public array $columnVisibility = [];
    public array $author_ids = [];
    public bool $sortOnlySelected = false;

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.posts.list-deleted-posts');
    }

    #[Computed]
    public function deleted_posts(): LengthAwarePaginator
    {
        $direction = strtolower($this->sortDirection) === 'asc' ? 'asc' : 'desc';
        $allowed = ['id','title','publish_date','created_at','updated_at',
            'views','views_sum','category','author','tag','publisher','resource','type','available_date'];

        $query = Post::onlyTrashed()
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

                'main_publisher_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as prp', 'prp.relationable_id', '=', 'participants.id')
                    ->whereColumn('prp.post_id', 'posts.id')
                    ->where('prp.relationable_type', Participant::class)
                    ->where('prp.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::PUBLISHERS->value)
                    ->limit(1),

                'main_resource_name' => Participant::query()
                    ->select('participants.name')
                    ->join('post_relations as prr', 'prr.relationable_id', '=', 'participants.id')
                    ->whereColumn('prr.post_id', 'posts.id')
                    ->where('prr.relationable_type', Participant::class)
                    ->where('prr.relationable_is_main', 1)
                    ->where('participants.type', ParticipantTypeEnum::RESOURCES->value)
                    ->limit(1),
            ])
            ->withSum('views as views_sum', 'views_number');

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
            // Eager loads
            ->with([
                'category.relationable', 'categories.relationable',
                'tag.relationable', 'tags.relationable',
                'author.relationable', 'authors.relationable',
                'publisher.relationable', 'publishers.relationable',
                'resource.relationable', 'resources.relationable',
                'type.relationable', 'types.relationable',
                'special_file.relationable', 'special_files.relationable',
                'user','post_special_content',
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
            ->when(!in_array($this->sortField, $allowed), function ($q) {
                $q->orderBy('posts.' . $this->sortField, $this->sortDirection);
            });

        return $query->paginate($this->perPage);
    }



    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }
    private function applySorting($query)
    {
        if ($this->sortOnlySelected && !empty($this->selected)) {
            $query->whereIn('posts.id', $this->selected);
        }
        if ($this->sortField === 'category') {
            return $query->join('categories', 'posts.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $this->sortDirection);
        }

        if ($this->sortField === 'author') {
            return $query->with('authors')
                ->orderByRaw('(select name from authors where authors.id = posts.author_id limit 1) ' . $this->sortDirection);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function changeStatus($post_id): void
    {
        $this->Post = Post::query()->withTrashed()->find($post_id);

       // $this->status = $status;
    }

    public function confirmChangeStatus(): void
    {

        $this->Post->update(['publish_status' =>  PublishEnum::DRAFT->value, 'deleted_at' => null]);
        $this->Post->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => __('validation.change_post_status'),
        ]);
        $this->resetSelection();
    }

    public function  bulkConfirmChangeStatus () :void
    {
        $posts = Post::withTrashed()->whereIn('id', $this->selected)->get();

        $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

        foreach ($posts as $post) {
            $post->update(['publish_status' => PublishEnum::DRAFT->value , 'deleted_at' => null]);
            $post->user_logs()->create([
                'user_id' => Auth::id(),
                'action_status' => __('validation.change_post_status'),
            ]);

        }
        $this->dispatch('hide_bulk_post_change_status_modal');
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
    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('deleted_post_columns', $this->columnVisibility);
        }
    }
    public function bulkConfirmChangeAuthor()
    {
        $posts = Post::withTrashed()->whereIn('id', $this->selected)->get();

        foreach ($posts as $post) {
            // حذف المؤلفين السابقين
            $post->authors()->detach();

            // إضافة المؤلفين الجدد
            foreach ($this->author_ids as $index => $authorId) {
                $post->authors()->attach($authorId, [
                    'relationable_is_main' => $index === 0 ? 1 : 0,
                ]);
            }
        }

        $this->dispatch('hide_bulk_change_author_modal');
        $this->resetSelection();
    }

    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up';
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }
    public function updatedColumnVisibility($value, $key)
    {
        if (!in_array(false, $this->columnVisibility)) {
            $this->selectAllColumns = true;
        } else {
            $this->selectAllColumns = false;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('deleted_post_columns', $this->columnVisibility);
        }
    }
    public function mount()
    {
        $this->columnVisibility = array_filter(
            config('features.post_columns', []),
            fn ($value) => $value === true
        );

        if (auth()->check()) {
            $savedColumns = auth()->user()->getPreference('deleted_post_columns', []);
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
    public function delete ($post_id): void
    {
      //  $this->Post = $post;
        $this->Post = Post::query()->withTrashed()->where('id',$post_id)->first();
        $this->dispatch('show_delete_modal');
    }

    public function confirmDelete(): void
    {
        PostRelation::where('post_id',$this->Post->id)->forceDelete();
        $this->Post->forceDelete();

        $this->Post->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);

        $this->dispatch('hide_delete_modal');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $postIds = collect($this->deleted_posts->items())->pluck('id')->all();
        if ($value) {
            $this->selected = array_unique(array_merge($this->selected, $postIds));
        } else {
            $this->selected = array_diff($this->selected, $postIds);
        }
        $this->checkSelectAll();
    }


    public function updatedSelected(): void
    {
        $this->checkSelectAll();
    }


    public function updatedPage(): void
    {
        $this->selectAll = false;
        $this->checkSelectAll();
    }

    private function checkSelectAll(): void
    {
        $postIds = collect($this->deleted_posts->items())->pluck('id')->all();
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
                $posts = Post::withTrashed()->whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($posts as $post) {
                    $post->forceDelete();

                    $post->user_logs()->create([
                        'user_id' => Auth::id(),
                        'action_status' => \App\Enums\ActionsEnum::DELETE->value,
                    ]);
                }


                $this->dispatch('hide_delete_selected_modal');
                $this->resetPage();
                $this->resetSelection();

            } else {
                session()->flash('error',__('messages.error_delete_text'));
            }
        } else {
            session()->flash('error',__('messages.empty_delete_text') );
        }


    }
    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}

