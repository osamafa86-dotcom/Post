<?php

namespace App\Livewire\Main\Tayqan;


use App\Enums\NewsTypeEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Url;

class IndexPage extends Component
{
    public $postForTopHighlight;

    #[Url(as: 'type')]
    public $filterType = 'all';

    #[Url(as: 'category')]
    public $filterCategory = 'all';

    public $mainPost;
    public $subPosts = [];
    public $investigationPosts;
    public $filteredPosts = [];

    public function mount()
    {
        $this->filterType = request()->query('type', 'all');
        $this->filterCategory = request()->query('category', 'all');

        $this->loadMainPosts();
        $this->loadFilteredTopHighlight(); // Load filtered top highlights
    }

    public function filter($type)
    {
        $this->filterType = $type;
        $this->filterCategory = 'all'; // Reset category when changing type
        $this->loadFilteredTopHighlight(); // Reload filtered top highlights
    }

    public function filterCategory($category)
    {
        $this->filterCategory = $category;
        $this->filterType = 'all'; // Reset type when changing category
        $this->loadFilteredTopHighlight(); // Reload filtered top highlights
    }

    protected function loadMainPosts(): void
    {
        $withRelations = [
            'categories.relationable',
            'types.relationable',
            'authors.relationable.files.file',
            'publishers.relationable.files.file',
            'files.file',
        ];

        $posts = Post::query()
            ->whereIn('news_type', [
                NewsTypeEnum::MAIN_NEWS->value,
                NewsTypeEnum::SUB_NEWS->value,
            ])
            ->with($withRelations)
            ->latest('publish_date')
            ->get();

        $this->mainPost = $posts
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->first();

        $this->subPosts = $posts
            ->where('news_type', NewsTypeEnum::SUB_NEWS->value)
            ->take(3)
            ->values();
    }


    protected function loadFilteredTopHighlight()
    {
        $postQuery = Post::query();

        if ($this->filterType !== 'all') {
            $postQuery->join('post_relations as type_relations', function ($join) {
                $join->on('posts.id', '=', 'type_relations.post_id')
                    ->where('type_relations.relationable_type', 'App\Models\Type');
            })
                ->join('types', 'type_relations.relationable_id', '=', 'types.id')
                ->where('types.type_name', $this->filterType);
        }

        if ($this->filterCategory !== 'all') {
            $postQuery->join('post_relations as category_relations', function ($join) {
                $join->on('posts.id', '=', 'category_relations.post_id')
                    ->where('category_relations.relationable_type', 'App\Models\Category');
            })
                ->join('categories as filter_categories', 'category_relations.relationable_id', '=', 'filter_categories.id')
                ->where('filter_categories.id', $this->filterCategory);
        }

        if ($this->filterType !== 'all' || $this->filterCategory !== 'all') {
            $postQuery->select('posts.*')->distinct();
        }

        $this->filteredPosts = $postQuery->with(['categories.relationable', 'types.relationable', 'authors.relationable.files.file', 'publishers.relationable.files.file', 'files.file'])
            ->orderBy('posts.publish_date', 'desc')
            ->orderBy('posts.id', 'desc')
            ->take(6)
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::with(['files.file', 'post_relation.post.files.file', 'post_relation.post.authors.relationable', 'material_relation.material.files.file'])
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function randPosts()
    {
        return Post::inRandomOrder()
            ->with(['files.file', 'authors.relationable', 'category.relationable'])
            ->limit(6)
            ->get();
    }

    #[Layout('components.layouts.main.tayqan.main')]
    public function render()
    {
//        $excludeIds = Post::where('news_type', NewsTypeEnum::MAIN_NEWS->value)
//            ->orderBy('updated_at', 'desc')
//            ->take(1)
//            ->pluck('id')
//            ->merge(
//                Post::where('news_type', "<>", NewsTypeEnum::SUB_NEWS->value)
//                    ->orderBy('updated_at', 'desc')
//                    ->take(3)
//                    ->pluck('id')
//            );
//
//        $this->investigationPosts = Post::where('news_type', "<>", NewsTypeEnum::MAIN_NEWS->value)
//            ->whereNotIn('id', $excludeIds)
//            ->orderBy('id', 'desc')
//            ->take(5)
//            ->get();

        return view('livewire.main.tayqan.index-page');
    }

}
