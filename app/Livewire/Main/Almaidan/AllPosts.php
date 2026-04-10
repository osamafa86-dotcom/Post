<?php

namespace App\Livewire\Main\Almaidan;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Advertisement;
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

    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    public $type, $id, $category_id;

    public array $search_query = [];


    #[Layout('components.layouts.main.almaidan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almaidan.all-posts');
    }


    public function mount($id = null): void
    {
        if ($id && $this->type == 'category') {
            $this->search_query['category_id'] = $id;
        } else {
            $this->search_query['category_id'] = 'all';
        }
        if ($id && $this->type == 'text') {
            $this->search_query['search_text'] = $id;
        }
    }


    #[Computed]
    public function posts()
    {
        return Post::latest('publish_date')
            ->when(!empty($this->search_query['search_text']), function ($query) {
                $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
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
            ->with(['category.relationable', 'author.relationable.files.file', 'files.file'])
            ->paginate(9);

    }

    #[Computed]
    public function categories()
    {
        return Category::where('category_type', CategoryTypeEnum::NEWS->value)
            ->withCount(['post_relation' => function ($query) {
                $query->whereHas('post', function ($q) {
                    $q->where('publish_status', PublishEnum::PUBLISHED->value);
                });
            }])->orderBy('post_relation_count', 'desc')->get();
    }
    public function updatedSearchQuery()
    {
        $this->resetPage();
    }
}
