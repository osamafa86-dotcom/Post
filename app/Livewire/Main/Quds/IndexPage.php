<?php

namespace App\Livewire\Main\Quds;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\VideoTypeEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Event;
use App\Models\Material;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use App\Models\SocialMedia;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    #[Computed]
    public function categories_index(): Collection|array
    {
        $categories = Category::query()
            ->where('show_index', 1)
            ->where(function ($query) {
                $query->whereHas('post_relation', function ($q) {
                    $q->where('relationable_is_main', 1)
                        ->whereHas('post', function ($q2) {
                            $q2->where('publish_status', PublishEnum::PUBLISHED->value);
                        });
                })
                    ->orWhereHas('material_relation', function ($q) {
                        $q->where('relationable_is_main', 1)
                            ->whereHas('material', function ($q2) {
                                $q2->where('publish_status', PublishEnum::PUBLISHED->value);
                            });
                    });
            })
            ->with([
                'post_relation' => fn($q) => $q
                    ->where('relationable_is_main', true)
                    ->whereHas('post', function ($q) {
                        $q->where('publish_status', PublishEnum::PUBLISHED->value)->latest('publish_date');
                    })
                    ->orderByDesc(
                        Post::select('publish_date')
                            ->whereColumn('posts.id', 'post_relations.post_id')
                            ->latest('publish_date')
                            ->take(1)
                    )
                    ->limit(15),
                'post_relation.post.category.relationable',
                'post_relation.post.author.relationable',
                'post_relation.post.files.file',
                'material_relation' => fn($q) => $q
                    ->where('relationable_is_main', true)
                    ->whereHas('material', function ($q) {
                        $q->where('publish_status', PublishEnum::PUBLISHED->value)->latest('created_at');
                    })
                    ->orderByDesc(
                        Material::select('created_at')
                            ->whereColumn('materials.id', 'material_relations.material_id')
                            ->latest('created_at')
                            ->take(1)
                    )
                    ->limit(15),
                'material_relation.material.files.file',
                'material_relation.material.presenter'
            ])
            ->orderBy('order')
            ->get();
        return $categories;
    }

    #[Computed]
    public function sliderPost(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->with(['author.relationable', 'files.file'])
            ->get();
    }

    #[Layout('components.layouts.main.quds.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.quds.index-page');
    }
}
