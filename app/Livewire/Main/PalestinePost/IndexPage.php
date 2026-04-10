<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\DataPageEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\ImmigrantData;
use App\Models\Material;
use App\Models\Post;
use App\Models\Quote;
use App\Models\SortData;
use App\Models\SpecialFile;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    public $category_id = "all";
    public int $number_of_materials = 15;
    public int $total_of_materials = 0;
    public $selectedCategory;
    public $opportunityModelData;
    public ?int $playing_id;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $main_post = Post::select('id', 'slug', 'title', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->orderByDesc('publish_date')
            ->with(['category.relationable', 'files.file'])
            ->first();
        return view('livewire.main.palestine-post.index-page', ['main_post' => $main_post]);
    }

    public function mount(): void
    {
        $this->number_of_materials = max(15, min((int) request('count', 15), 200));
        $this->total_of_materials = SortData::count();
        if ($this->local_news?->children?->first()) {
            $this->chooseLocalCategory($this->local_news->children->first());
        }
    }

    #[Computed]
    public function sub_posts()
    {
        $query = Post::select('id', 'slug', 'title', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::SUB_NEWS->value)
            ->with(['category.relationable'])
            ->orderByDesc('publish_date');
        if (!empty($this->main_post)) {
            $query->where('id', '!=', $this->main_post->id)->take(3);
        } else {
            $query->take(3);
        }

        return $query->get();
    }

    #[Computed]
    public function opportunitiesAndGrants()
    {
        return ImmigrantData::whereIn('type', [DataPageEnum::OPPORTUNITY->value, DataPageEnum::SCHOLARSHIP->value])->with(['files.file'])->latest()->take(12)->get();
    }

    public function setOpportunity(ImmigrantData $data): void
    {
        $this->opportunityModelData = $data;
    }

    #[Computed]
    public function last_posts()
    {
        return Post::select('id', 'slug', 'title', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable', 'files.file'])
            ->orderByDesc('publish_date')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function materials()
    {
        $items = SortData::with([
            'sortable.files.file',
        ])->orderByDesc('order_number')
            ->take($this->number_of_materials)
            ->get();
        $items->loadMorph('sortable', [
            \App\Models\Post::class => ['category.relationable'],
            \App\Models\Material::class => ['category.relationable'],
            \App\Models\Quote::class => ['author.files.file'],
        ]);
        return $items;
    }

    public function showMoreMaterial(): void
    {
        $this->number_of_materials += 5;
        $this->dispatch('reloadOwlCarousel');
        $this->dispatch('reloadSwiper');
    }

    public function selectCategory($id): void
    {
        $this->category_id = $id;
        $this->dispatch('reloadOwlCarousel');
        $this->dispatch('reloadSwiper');
    }

    #[Computed]
    public function local_news()
    {
        return Category::query()
            ->where('category_title', 'أخبار محلية')
            ->with([
                'children' => function ($q) {
                    $q->whereHas('post_relation')
                        ->withCount('post_relation')
                        ->orderByDesc('post_relation_count')
                        ->with([
                            'post_relation' => fn($q) => $q
                                ->whereHas('post')
                                ->with([
                                    'post' => fn($q) => $q->select('id', 'slug', 'title', 'publish_date')->take(100)
                                ])
                                ->latest()
                                ->take(10)
                        ]);
                }
            ])
            ->first();
    }


    public function chooseLocalCategory(Category $category): void
    {
        $this->selectedCategory = $category;
        $this->dispatch('reloadOwlCarousel');
        $this->dispatch('reloadSwiper');
    }

    #[Computed]
    public function last_videos()
    {
        return Material::select('id', 'title', 'type', 'video_type', 'link')
            ->with(['files.file'])
            ->where('type', MaterialTypeEnum::VIDEO->value)
            ->latest()
            ->take(3)
            ->get();
    }

    public function playVideo($video_id): void
    {
        $this->playing_id = $video_id;
        $this->dispatch('reloadOwlCarousel');
        $this->dispatch('reloadSwiper');
    }

    #[Computed]
    public function last_podcasts()
    {
        return Material::select('id', 'title', 'type', 'album_id')
            ->with(['files.file', 'material_album'])
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->latest()
            ->take(2)
            ->get();
    }

    #[Computed]
    public function special_files()
    {
        return SpecialFile::whereHas('post_relation.post')
            ->with(['files.file'])
            ->with([
                'post_relation' => fn($q) => $q
                    ->whereHas('post')
                    ->with(['post' => fn($q) => $q->select('id', 'slug', 'title', 'publish_date')])
                    ->latest()
                    ->take(4)
            ])
            ->get();
    }

    #[Computed]
    public function articles()
    {
        return Post::query()
            ->with(['author.relationable', 'category.relationable'])
            ->whereHas('author')
            ->where('image_size', ImageSizeTypeEnum::COVER_ARTICLE->value)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderByDesc('publish_date')
            ->take(10)
            ->get();
    }

    #[Computed]
    public function quotes()
    {
        return Quote::orderByDesc('created_at')->with(['author.files.file'])->take(10)->get();
    }
}

