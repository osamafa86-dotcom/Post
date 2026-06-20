<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use http\Url;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AllPosts extends Component
{
    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    public array $search_query = [];
    public $category_title , $tag_id;


    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.all-posts');
    }

    #[Computed]
    public function posts()
    {

        $segment = request()->segment(1);
        $title = request()->segment(2);

        $query = Post::when(!empty($this->search_query['search_text']), function ($query) {
            $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
        })
            ->when(!empty($this->search_query['date']) && !empty($this->search_query['to_date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['to_date'] . ' 23:59:59']);
            })
            ->when(!empty($this->search_query['category_title'])  && $this->search_query['category_title'] != 'all', function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->search_query['category_title']);
                        }
                    );
                });
            })
            ->when($segment == 'tag_news', function ($query) use ($title) {
                $query->whereHas('tag', function ($query) use ($title) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) use ($title) {
                            $subQuery->where('tag_name',  $title);
                        }
                    );
                });
            })
            ->when($segment ==  'category_news', function ($query) use ($title) {
                $query->whereHas('category', function ($query) use ($title) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) use ($title) {
                            $subQuery->where('category_title',  $title);
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
            });

        // The pagination COUNT runs nested EXISTS over ~22k rows (the 4s+ bottleneck).
        // Cache it (5-min staleness) and pass it to paginate() as $total so Laravel
        // skips the count query entirely — Livewire pagination stays intact.
        $cacheKey = 'allposts:count:' . md5(json_encode([$segment, $title, $this->search_query]));
        $total = Cache::remember($cacheKey, 300, fn () => (clone $query)->count());

        return $query
            ->with(['category.relationable', 'tag.relationable', 'author.relationable', 'files.file'])
            ->latest()
            ->paginate(6, ['*'], 'page', null, $total);
    }
    #[Computed]
    public function categories(): Collection|array
    {

        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->whereHas('post_relation')
            ->withCount('post_relation')
            ->orderBy('post_relation_count', 'desc')
            ->get();
    }


    public function mount($category_title =null): void
    {
        if (!empty($category_title)) {
            $this->search_query['category_title'] = Category::where('category_title', $category_title)->first()?->id;
        }

    }

//    #[Computed]
//    public function tags()
//    {
//        return  $this->Post->tags;
//
//    }

}
