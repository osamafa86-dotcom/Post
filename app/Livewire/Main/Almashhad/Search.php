<?php

namespace App\Livewire\Main\Almashhad;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $category_id;

    public $category;
    public $category_title;
    public $search = '';
    public $dateBeginFilter;
    public $dateEndFilter;

    public function mount($id=null)
    {
        $this->category_id = $id;
        $this->handleCategory();
    }

    private function handleCategory()
    {
        if (isset($this->category_id)) {
            $this->category = Category::find($this->category_id);
            $this->category_title = $this->category?->category_title;
        } else {
            $this->category_title = 'التصنيفات';
        }
    }

    public function resetAllFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->handleCategory();
    }

    public function applyFilter()
    {
        $this->resetPage();
        unset($this->posts);
        $this->handleCategory();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
        $this->handleCategory();
    }
    public function updatedSearch()
    {
        $this->resetPage();
        $this->handleCategory();

    }

    #[Computed]
    public function combined()
    {
        $posts = Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->when(!empty($this->search), function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when(!empty($this->category_id) && $this->category_id != 'all', function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('relationable_is_main',1)->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category_id);
                        }
                    );
                });
            })
            ->when(!empty($this->dateBeginFilter) && !empty($this->dateEndFilter), function ($query) {
                $query->whereBetween('publish_date', [$this->dateBeginFilter . ' 00:00:00', $this->dateEndFilter . ' 23:59:59']);
            })
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->select(
                'id',
                'title',
                DB::raw("NULL as video_type"),
                DB::raw("NULL as link"),
                'publish_date as date',
                'slug',
                DB::raw("'post' as stype")
            )
            ->latest('publish_date')
            ->get();

        // Materials query
        $materials = Material::where('publish_status', PublishEnum::PUBLISHED->value)
            ->when(!empty($this->search), function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when(!empty($this->category_id) && $this->category_id != 'all', function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('relationable_is_main',1)->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category_id);
                        }
                    );
                });
            })
            ->when(!empty($this->dateBeginFilter) && !empty($this->dateEndFilter), function ($query) {
                $query->whereBetween('created_at', [$this->dateBeginFilter . ' 00:00:00', $this->dateEndFilter . ' 23:59:59']);
            })
            ->with(['category.relationable', 'presenter.relationable', 'files.file'])
            ->select(
                'id',
                'title',
                'video_type',
                'link',
                'created_at as date',
                DB::raw("NULL as slug"),
                DB::raw("'material' as stype")
            )
            ->latest('created_at')
            ->get();

        $combined = $posts->concat($materials)->sortByDesc('date');
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage),
            $combined->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return $paginated;
    }

//    #[Computed]
//    public function posts()
//    {
//        $query = Post::query();
//
//        if (isset($this->category_id)) {
//            $categoryId = $this->category_id;
//            $query->whereHas('categories', function ($q) use ($categoryId) {
//                $q->where([['relationable_id', $categoryId], ['relationable_is_main',1]]);
//            });
//        }
//
//        return $query
//            ->when($this->search, function ($q) {
//                $q->where('title', 'like', '%' . $this->search . '%');
//            })
//            ->when($this->dateBeginFilter, function ($q) {
//                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
//            })
//            ->when($this->dateEndFilter, function ($q) {
//                $q->whereDate('created_at', '<=', $this->dateEndFilter);
//            })
//            ->with(['files.file', 'category.relationable','author.relationable'])
//            ->latest()
//            ->paginate(15);
//    }
//
//    #[Computed]
//    public function materials()
//    {
//        $query = Material::query();
//        if ($this->category_id) {
//            $query->whereHas('categories', function ($q) {
//                $q->where([['relationable_id', $this->category_id], ['relationable_is_main',1]]);
//            });
//        }
//        return $query->when($this->search, function ($q) {
//            $q->where('title', 'like', '%' . $this->search . '%');
//        })->when($this->dateBeginFilter, function ($q) {
//            $q->whereDate('created_at', '>=', $this->dateBeginFilter);
//        })->when($this->dateEndFilter, function ($q) {
//            $q->whereDate('created_at', '<=', $this->dateEndFilter);
//        })->with(['files.file', 'category.relationable','presenter.relationable'])->latest()->paginate(15);
//    }

    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        $this->dispatch('edited');
        return view('livewire.main.almashhad.search');
    }
}
