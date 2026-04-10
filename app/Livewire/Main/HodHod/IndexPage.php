<?php

namespace App\Livewire\Main\HodHod;

use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    #[Computed]
    public function categories_index(): Collection|array
    {
        return Category::query()
            ->where('show_index', 1)
            ->whereHas('post_relation', function ($query) {
                $query->whereHas('post', function ($q) {
                    $q->where('publish_status', PublishEnum::PUBLISHED->value);
                });
            })
            ->with([
                'post_relation' => function ($query) {
                    $query->whereHas('post', function ($q) {
                        $q->where('publish_status', PublishEnum::PUBLISHED->value);
                    })
                    ->join('posts', 'post_relations.post_id', '=', 'posts.id')
                    ->orderBy('posts.publish_date', 'desc')
                    ->select('post_relations.*');
                },
                'post_relation.post.category.relationable', 
                'post_relation.post.author.relationable', 
                'post_relation.post.files.file',
            ])
            ->orderBy('order')
            ->get();
    }
    #[Computed]
    public function main_post(){
        return Post::with(['category.relationable','author.relationable','files.file'])
            ->where('publish_status',PublishEnum::PUBLISHED)
            ->where('news_type',NewsTypeEnum::MAIN_NEWS->value)
            ->latest('publish_date')
            ->first();
    }

    #[Computed]
    public function sub_posts(){
        return Post::with(['category.relationable','author.relationable','files.file'])
            ->where('publish_status',PublishEnum::PUBLISHED)
            ->where('news_type',NewsTypeEnum::SUB_NEWS->value)
            ->latest('publish_date')
            ->take(3)
            ->get();
    }


    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.index-page');
    }
}
