<?php

namespace App\Livewire\Main\Alahed;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AllPosts extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap_main';
    public array $filter = [];
    public $category;

    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.alahed.all-posts');
    }

    public function mount($category_id = null)
    {
        if (!empty($category_id))
            $this->category = Category::where('category_title', $category_id)->first();
        else $this->category = null;
        $this->filter['category_id'] = $this->category?->id ?? null;
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->when(isset($this->filter), function ($q) {
                if (!empty($this->filter['search'])) {
                    $q->where('title', 'like', '%' . $this->filter['search'] . '%');
                }
                if (!empty($this->filter['category_id']) && $this->filter['category_id'] != 'all') {
                    $q->whereHas('category', function ($q) {
                        $q->where('relationable_id', $this->filter['category_id']);
                    });
                }
                if (!empty($this->filter['publish_date_form']) && !empty($this->filter['publish_date_to'])) {
                    $q->whereBetween('publish_date', [$this->filter['publish_date_form'], $this->filter['publish_date_to']]);
                }
            })->with(['files.file'])
            ->orderByDesc('publish_date')
            ->paginate(9);
    }

    #[Computed]
    public function categories()
    {
        return Category::pluck('category_title', 'id');
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
