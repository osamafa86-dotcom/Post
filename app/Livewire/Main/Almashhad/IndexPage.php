<?php

namespace App\Livewire\Main\Almashhad;

use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    #[Computed]
    public function categories()
    {
        return Category::where(function ($query) {
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
        })->with([
            'files.file',
            'post_relation' => fn($q) => $q->limit(15),
            'post_relation.post.files.file',
            'post_relation.post.author.relationable',
            'material_relation' => fn($q) => $q->limit(15),
            'material_relation.material.files.file',
            'material_relation.material.category.relationable',
            'material_relation.material.presenter.relationable'
        ])
            ->where('show_index', 1)
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function mainNews()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->with(['files.file', 'category.relationable'])
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();
    }

    #[Computed]
    public function subNews()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('news_type', NewsTypeEnum::SUB_NEWS->value)
            ->with(['files.file', 'category.relationable'])
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();
    }

    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.index-page');
    }
}
