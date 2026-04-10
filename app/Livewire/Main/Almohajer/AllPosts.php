<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AllPosts extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public $type, $id, $category_id;

    public array $search_query = [];
    public $search_text = '';


    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.all-posts');
    }

    public function mount($id = null): void
    {
        if ($id && $this->type == 'category') {
            $this->search_query['category_id'] = $id;
        } else {
            $this->search_query['category_id'] = 'all';
        }

    }

    #[Computed]
    public function posts()
    {
        return Post::latest('publish_date')
            ->when(!empty($this->search_text), function ($query) {
                $query->where('title', 'like', '%' . $this->search_text . '%');
            })
            ->when(!empty($this->search_query['category_id']) && $this->search_query['category_id'] != 'all', function ($query) {
                $query->WhereHas('category', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->search_query['category_id']);
                        }
                    );
                });
            })
            ->when(!empty($this->search_query['date']) && !empty($this->search_query['to_date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['to_date'] . ' 23:59:59']);
            })
            ->when(!empty($this->id) && $this->type == 'tag', function ($query) {
                $query->WhereHas('tag', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->id);
                        }
                    );
                });
            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['category.relationable', 'authors.relationable','authors.relationable.files.file', 'files.file'])
            ->paginate(9);
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatedSearchText(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::where('category_type', CategoryTypeEnum::NEWS->value)->withCount('post_relation')->get();
    }
}
