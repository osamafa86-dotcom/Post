<?php

namespace App\Livewire\Main\Maktoob;

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

    public $type, $id , $category_id;
    public $side_advert , $bottom_advert;
    public array $search_query = [];
    public string $category_name;


    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.all-posts');
    }


    public function mount($id = null): void
    {
        if ($id) {
            $this->search_query['category_id'] = $id;
            $this->category_name = Category::query()->where('id', $id)->first()?->category_title;
        } else {
            $this->search_query['category_id']  = 'all';
        }
        $this->side_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::SIDE_ADS->value)
            ->latest()->first();

        $this->bottom_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();
    }



    #[Computed]
    public function posts()
    {
        return Post::latest('publish_date')
            ->when(!empty($this->search_query['category_id']) && $this->search_query['category_id'] != 'all', function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->search_query['category_id']);
                        }
                    );
                });
            })->where('publish_status', PublishEnum::PUBLISHED->value)
//            ->where('news_type' , NewsTypeEnum::MAIN_NEWS->value)
            ->latest('publish_date')
            ->with(['categories.relationable', 'authors.relationable.files.file', 'files.file'])
            ->paginate(13);
    }


    #[Computed]
    public function otherCategoryPosts()
    {
        return Post::latest('publish_date')->where('publish_status', PublishEnum::PUBLISHED->value)
//            ->where('news_type' , NewsTypeEnum::MAIN_NEWS->value)
            ->latest('publish_date')
            ->with(['category.relationable', 'authors.relationable.files.file', 'files.file'])
            ->paginate(13);
    }
    #[Computed]
    public function latestPost()
    {
        return Post::latest('publish_date')->with(['files.file', 'category.relationable'])->take('10')->get();
    }

    #[Computed]
    public function side_advert()
    {

        return Advertisement::query()->where('place', AdvertisementPlaceEnum::SIDE_ADS->value)
            ->latest()->first();
    }
}
