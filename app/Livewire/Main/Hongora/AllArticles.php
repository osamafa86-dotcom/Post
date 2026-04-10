<?php

namespace App\Livewire\Main\Hongora;

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

class AllArticles extends Component
{
    public $type, $id , $category_id;

    public array $search_query = [];


    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.all-articles');
    }


    public function mount($id = null): void
    {
        if ($id && $this->type == 'category') {
            $this->search_query['category_id'] = $id;
        } else {
            $this->search_query['category_id']  = 'all';
        }

    }

    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('views', 'desc')
            ->take(5)
            ->with('category', 'author', 'files')
            ->get();
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
            ->when(!empty($this->id ) && $this->type == 'tag' , function ($query)  {

                $query->WhereHas('tag', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('id',  $this->id);
                        }
                    );
                });

            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with('category', 'author', 'files')
            ->paginate(8);
    }

    #[Computed]
    public function categories()
    {
        return Category::where('category_type', CategoryTypeEnum::NEWS->value)->withCount('post_relation')->get();
    }
}
