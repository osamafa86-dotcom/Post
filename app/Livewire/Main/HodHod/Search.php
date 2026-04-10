<?php

namespace App\Livewire\Main\HodHod;

use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
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

    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $search;
    #[Url(keep: true)]
    public $searchDateFrom;
    #[Url(keep: true)]
    public $searchDateTo;
    #[Url(keep: true)]
    public $categorySearch;

    public function mount()
    {
        $search = request()->query('search');
        if ($this->id) {
            $this->categorySearch[] = $this->id;
        }
        if ($search) {
            $this->search = $search;
        }
    }

    public function resetSearch()
    {
        $this->reset(['search', 'searchDateFrom', 'searchDateTo', 'categorySearch']);
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedsearchDateFrom()
    {
        $this->resetPage();
    }

    public function updatedSearchDateTo()
    {
        $this->resetPage();
    }

    public function updatedCategorySearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function combined()
    {
        $posts = Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->searchDateFrom, fn($q) => $q->whereDate('publish_date', '>=', $this->searchDateFrom))
            ->when($this->searchDateTo, fn($q) => $q->whereDate('publish_date', '<=', $this->searchDateTo))
            ->when($this->categorySearch, fn($q) => $q->whereHas('categories', fn($q) => $q->where('relationable_is_main', 1)->whereIn('relationable_id', (array)$this->categorySearch)))
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->select('id', 'title',
                DB::raw("NULL as video_type"),
                DB::raw("NULL as link"),
                'publish_date as date',
                'slug',
                DB::raw("'post' as type"))
            ->latest('publish_date')
            ->get();

        $materials = [];

// Merge and sort by date descending
        $combined = $posts->concat($materials)->sortByDesc('date');

// Manual pagination
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
//        return Post::where('publish_status', '=', PublishEnum::PUBLISHED->value)
//            ->when($this->search, function ($q) {
//                $q->where('title', 'like', '%' . $this->search . '%');
//            })->when($this->searchDateFrom, function ($q) {
//                $q->whereDate('publish_date', '>=', $this->searchDateFrom);
//            })->when($this->searchDateTo, function ($q) {
//                $q->whereDate('publish_date', '<=', $this->searchDateTo);
//            })->when($this->categorySearch, function ($q) {
//                $q->whereHas('categories', function ($q) {
//                    $q->whereIn('relationable_id', (array)$this->categorySearch);
//                });
//            })->with(['category.relationable', 'author.relationable', 'files.file'])->latest()->paginate(12);
//    }
//
//    #[Computed]
//    public function materials()
//    {
//        return Material::where('publish_status', '=', PublishEnum::PUBLISHED->value)
//            ->when($this->search, function ($q) {
//                $q->where('title', 'like', '%' . $this->search . '%');
//            })->when($this->searchDateFrom, function ($q) {
//                $q->whereDate('created_at', '>=', $this->searchDateFrom);
//            })->when($this->searchDateTo, function ($q) {
//                $q->whereDate('created_at', '<=', $this->searchDateTo);
//            })->when($this->categorySearch, function ($q) {
//                $q->whereHas('categories', function ($q) {
//                    $q->whereIn('relationable_id', (array)$this->categorySearch);
//                });
//            })->with(['category.relationable', 'presenter.relationable', 'files.file'])->latest()->paginate(12);
//    }

    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.search');
    }
}
