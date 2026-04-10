<?php

namespace App\Livewire\Main\Alarouri;

use App\Models\Post;
use App\Models\Category;
use App\Models\Type;
use App\Enums\PublishEnum;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class News extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = 'all';
    public $yearFilter = 'all';
    public $sortBy = 'newest';
    public $dateFrom = '';
    public $dateTo = '';

    public $yearLimits = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => 'all'],
        'yearFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'newest'],
    ];

    public function updatingSearch()
    {
        $this->yearLimits = [];
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->yearLimits = [];
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingYearFilter()
    {
        $this->yearLimits = [];
        $this->resetPage();
    }

    public function loadMoreForYear($year)
    {
        $this->yearLimits[$year] = ($this->yearLimits[$year] ?? 6) + 6;
    }

    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        $query = Post::with(['category.relationable', 'type.relationable', 'files.file'])
            ->where('publish_status', PublishEnum::PUBLISHED->value);

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->categoryFilter !== 'all') {
            $query->whereHas('category', function($q) {
                $q->where('categories.id', $this->categoryFilter);
            });
        }

        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('publish_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('publish_date', '<=', $this->dateTo);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('publish_date', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default: // newest
                $query->orderBy('publish_date', 'desc');
                break;
        }

        // Result sets
        $featuredPosts = (clone $query)
            ->orderBy('publish_date', 'desc')
            ->take(2)
            ->get();

        // Get available years from the filtered query
        $availableYears = (clone $query)
            ->selectRaw('YEAR(publish_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter(); // Remove nulls

        $groupedNews = [];
        foreach ($availableYears as $year) {
            if ($this->yearFilter !== 'all' && $this->yearFilter != $year) {
                continue;
            }

            $limit = $this->yearLimits[$year] ?? 6;
            
            $posts = (clone $query)
                ->whereYear('publish_date', $year)
                ->orderBy('publish_date', 'desc')
                ->take($limit)
                ->get();

            $totalInYear = (clone $query)
                ->whereYear('publish_date', $year)
                ->count();

            $groupedNews[] = [
                'year' => $year,
                'posts' => $posts,
                'hasMore' => $totalInYear > $limit,
                'total' => $totalInYear
            ];
        }

        $categories = Category::all();
        $lastUpdate = Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->max('publish_date');

        return view('livewire.main.alarouri.news', [
            'featuredPosts' => $featuredPosts,
            'groupedNews' => $groupedNews,
            'availableYears' => $availableYears,
            'categories' => $categories,
            'lastUpdate' => $lastUpdate,
        ]);
    }
}
