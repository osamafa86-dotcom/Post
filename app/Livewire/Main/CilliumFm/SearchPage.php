<?php

namespace App\Livewire\Main\CilliumFm;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\PodcastAlbum;
use App\Models\Post;
use App\Models\Tag;
use App\Models\VideoAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Expr\Array_;

class SearchPage extends Component
{

    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    public $type, $id;

    public array $search_query = [];


    #[Layout('components.layouts.main.cillium_fm.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        return view('livewire.main.cillium-fm.search-page');
    }

    public function mount($id = null): void
    {
        if ($id && $this->type == 'category') {
            $this->search_query['category_id'] = $id;
        } else {
            $this->search_query['category_id'] = 'all';
        }

    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
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
            ->when(!empty($this->search_query['date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['date'] . ' 23:59:59']);
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
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->paginate(8);
    }

    #[Computed]
    public function categories()
    {
        return Category::where('category_type', CategoryTypeEnum::NEWS->value)->withCount('post_relation')->get();
    }

}
