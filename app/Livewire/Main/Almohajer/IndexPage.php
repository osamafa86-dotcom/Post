<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{
    public string $email;

    #[Computed]
    public function sub_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->with(['files.file','category.relationable'])
            ->where([['publish_status', PublishEnum::PUBLISHED->value],['news_type', NewsTypeEnum::SUB_NEWS->value]])
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();
    }

    #[Computed]
    public function main_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->with(['category.relationable', 'authors.relationable.files.file', 'files.file'])
            ->where([['publish_status', PublishEnum::PUBLISHED->value],['news_type', NewsTypeEnum::MAIN_NEWS->value]])
            ->latest('publish_date')
            ->take(3)
            ->get();
    }

    #[Computed]
    public function last_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->with(['files.file', 'category.relationable'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereNull('news_type')
            ->latest('publish_date')
            ->take(4)
            ->get();
    }

    #[Computed]
    public function categories_index(): Collection|array
    {
        return Category::query()
            ->where('show_index', 1)
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
                'post_relation.post.authors.relationable.files.file',
                'post_relation.post.files.file',
            ])
            ->orderBy('order')
            ->get();
    }

    public function addNewsLetterEmail(): void
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset(['email']);
    }

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.index-page');
    }
}
