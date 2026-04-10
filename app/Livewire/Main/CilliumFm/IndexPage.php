<?php

namespace App\Livewire\Main\CilliumFm;

use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\DayEnum;
use App\Enums\LinkPosition;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Event;
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

    public $category_posts;
    public $email;
    public $currentCategoryId;

    public function mount(): void
    {
        if ($this->categories?->count() > 0)
            $this->changeCategory($this->categories?->first());
    }

    public function addNewsLetterEmail(): void
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset(['email']);
    }


    #[Computed]
    public function last_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->take(16)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->get();
    }

    #[Computed]
    public function social_medias(): Collection|array
    {
        return SocialMedia::query()->where('position',LinkPosition::AllPlaces->value)->get();
    }

    #[Computed]
    public function categories(): Collection|array
    {
        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->whereHas('post_relation')
            ->take(6)
            ->get();
    }


    #[Computed]
    public function categories_index(): Collection|array
    {
        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
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
                'post_relation.post.author.relationable',
                'post_relation.post.files.file',
            ])
            ->get();
    }
    public function changeCategory(Category $category): void
    {
        $this->currentCategoryId = $category->id;

        $relations = $category->post_relation()
            ->where('relationable_is_main', true)
            ->whereHas('post', function ($q) {
                $q->where('publish_status', PublishEnum::PUBLISHED->value);
            })
            // Order the relations by their post's publish_date (same pattern you used)
            ->orderByDesc(
                Post::select('publish_date')
                    ->whereColumn('posts.id', 'post_relations.post_id')
                    ->latest('publish_date')
                    ->take(1)
            )
            ->with([
                'post.files.file',
                'post.author.relationable',
                'post.category.relationable',
            ])
            ->limit(4)
            ->get();

        // Extract the Post models (already eager loaded)
        $this->category_posts = $relations->pluck('post');
    }



    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->withSum('views as views_sum', 'views_number')
        ->orderByRaw('COALESCE(views_sum, 0) DESC')
        ->orderByDesc('publish_date')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function various_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->orderBy('publish_date', 'desc')
            ->inRandomOrder()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function various_posts_other(): Collection|array
    {

        $fetchedPostIds = $this->various_posts->pluck('id')->toArray();

        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->whereNotIn('id', $fetchedPostIds)
            ->orderBy('publish_date', 'desc')
            ->inRandomOrder()
            ->take(5)
            ->get();
    }


    #[Layout('components.layouts.main.cillium_fm.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $today = Carbon::now()->locale('ar')->translatedFormat('l');

// تطبيع أسماء الأيام
        $map = [
            'الأحد' => DayEnum::SUNDAY->value,
            'الإثنين' => DayEnum::MONDAY->value,
            'الثلاثاء' => DayEnum::TUESDAY->value,
            'الأربعاء' => DayEnum::WEDNESDAY->value,
            'الخميس' => DayEnum::THURSDAY->value,
            'الجمعة' => DayEnum::FRIDAY->value,
            'السبت' => DayEnum::SATURDAY->value,
        ];

        $today = $map[$today] ?? $today;

        $now = Carbon::now()->format('H:i');

        $event = Event::query()
            ->where('date_type', DateTypeEnum::TIME->value)
            ->whereHas('event_dates', function ($q) use ($today, $now) {
                $q->where('day', $today)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now);
            })
            ->with(['presenter.relationable','presenters.relationable', 'event_dates','category.relationable','tags.relationable','tag.relationable','categories.relationable'])
            ->first();

        return view('livewire.main.cillium-fm.index-page', [
            'event' => $event,
        ]);
    }
}
