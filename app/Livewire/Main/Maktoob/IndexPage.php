<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\AdvertisementTypeEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
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
    public $email;
    #[Computed]
    public function ads(): Collection|array
    {
        return Advertisement::where('type',AdvertisementTypeEnum::PHOTO)->with(['files.file'])->get();
    }
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
                    ->limit(25),
                'post_relation.post.category.relationable',
                'post_relation.post.author.relationable.files.file',
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
                'material_relation.material.category.relationable',
                'material_relation.material.files.file',
                'material_relation.material.presenter'
            ])
            ->orderBy('order')
            ->get();
        return $categories;
    }

    #[Computed]
    public function headerPosts()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->latest('publish_date')
            ->take(10)
            ->with(['category.relationable', 'authors.relationable.files.file', 'files.file'])
            ->get();
    }

    public function addNewsLetterEmail(): void
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'Email added successfully');
        $this->reset(['email']);
    }

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.index-page');
    }
}
