<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
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

class CategoryNews extends Component
{
    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    public array $search_query = [];
    public $category_title ;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.category-news');
    }

    #[Computed]
    public function posts()
    {
        return Post::with(['category.relationable' , 'tag.relationable' , 'author.relationable','files.file'])->when(!empty($this->search_query['search_text']), function ($query) {
            $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
        })
            ->when(!empty($this->search_query['date']) && !empty($this->search_query['to_date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['to_date'] . ' 23:59:59']);
            })
            ->when(!empty($this->category_title) , function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', $this->category_title);
                        }
                    );
                });
            })

            ->whereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('category_type', CategoryTypeEnum::NEWS->value);
                    }
                );
            })->latest()->paginate(6);


    }


}
