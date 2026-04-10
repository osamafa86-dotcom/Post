<?php

namespace App\Livewire\Main\Tayqan;

use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
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
        $this->handleCategory();
    }

    public function applyFilter()
    {
        unset($this->posts);
        $this->handleCategory();
    }

    public function updatedCategoryId()
    {
        $this->handleCategory();
    }

    #[Computed]
    public function posts()
    {
        $query = Post::query();

        if (isset($this->category_id)) {
            $categoryId = $this->category_id;
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('relationable_id', $categoryId);
            });
        }

        return $query
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->with(['files.file', 'categories'])
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function materials()
    {
        $query = Material::query();
        if ($this->category_id) {
            $query->whereHas('categories', function ($q) {
                $q->where('relationable_id', $this->category_id);
            });
        }
        return $query->when($this->search, function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%');
        })->when($this->dateBeginFilter, function ($q) {
            $q->whereDate('created_at', '>=', $this->dateBeginFilter);
        })->when($this->dateEndFilter, function ($q) {
            $q->whereDate('created_at', '<=', $this->dateEndFilter);
        })->with(['files.file', 'categories','presenter'])->latest()->paginate(15);
    }

    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.search');
    }
}
