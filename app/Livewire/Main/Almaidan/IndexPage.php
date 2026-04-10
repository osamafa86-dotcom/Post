<?php

namespace App\Livewire\Main\Almaidan;

use App\Enums\CategoryTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    #[Computed]
    public function categories_index(): Collection|array
    {
        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->where('show_index', 1)
            ->whereHas('post_relation.post', function ($query) {
                $query->where('publish_status', PublishEnum::PUBLISHED->value);
            })
            ->with([
                'post_relation' => function ($q) {
                    $q->whereHas('post', fn($qq) => $qq->where('publish_status', PublishEnum::PUBLISHED->value)
                    )
                        ->join('posts', 'posts.id', '=', 'post_relations.post_id')
                        ->orderByDesc('posts.publish_date')
                        ->select('post_relations.*')
                        ->with([
                            'post' => fn($qPost) => $qPost->with([
                                'category.relationable',
                                'author.relationable.files.file',
                                'files.file',
                            ])
                        ])->limit(15);
                }
            ])
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function main_large_image_posts()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->orderBy('publish_date', 'desc')
            ->with(['author.relationable.files.file', 'files.file'])
            ->take('5')
            ->get();
    }


    #[Computed]
    public function authors()
    {
        return Participant::with('files.file')->where('type', ParticipantTypeEnum::AUTHORS->value)->get();
    }


    #[Layout('components.layouts.main.almaidan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almaidan.index-page');
    }
}
