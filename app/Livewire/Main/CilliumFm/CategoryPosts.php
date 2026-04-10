<?php

namespace App\Livewire\Main\CilliumFm;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryPosts extends Component
{
    use \Livewire\WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $category_id;
    public object $category;

    #[Layout('components.layouts.main.cillium_fm.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.cillium-fm.category-posts');
    }

    public function mount(): void
    {
        if (!empty($this->category_id))
            $this->category = Category::find($this->category_id);
    }

    #[Computed]
    public function posts()
    {

        return Post::latest('publish_date')
            ->when(!empty($this->category_id), function ($query) {
                $query->WhereHas('category', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->category_id);
                        }
                    );
                });

            })
//            ->when(!empty($this->category_id) , function ($query) {
//                $query->where('category_id', $this->category_id);
//            })

            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->paginate(8);
    }


    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->withSum('views as views_sum', 'views_number')
        ->orderByRaw('COALESCE(views_sum, 0) DESC')
        ->orderByDesc('publish_date')
            ->take(5)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->get();
    }
}
