<?php

namespace App\Livewire\Main\Alahed;

use App\Enums\ImageSizeTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\VideoTypeEnum;
use App\Models\Category;
use App\Models\Statistic;
use App\Models\VideoAlbum;
use App\Models\PodcastAlbum;
use App\Models\Quote;
use App\Models\Post;
use App\Models\VideoTrack;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class IndexPage extends Component
{
    public ?string $selected_video = null;
    public ?string $selected_pdf = null;
    public array $state = [];

    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $main_news = Post::whereHas('category')
            ->where('news_type', NewsTypeEnum::MAIN_NEWS->value)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['files.file', 'category.relationable'])
            ->orderByDesc('publish_date')
            ->first();

        $sub_news = Post::whereHas('category')
            ->where('news_type', NewsTypeEnum::SUB_NEWS->value)
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['category.relationable','files.file'])
            ->orderByDesc('publish_date')
            ->take(4)
            ->get();

        $excludedPostIds = collect([$main_news?->id])
            ->merge($sub_news->pluck('id'))
            ->filter() // للتأكد من إزالة null إن وُجد
            ->toArray();

        $last_posts = Category::whereNotIn('category_title', ['قصص إنسانية', 'اصدارات العهد'])
            ->whereHas('post_relation.post', function ($q) {
                $q->where('publish_status', PublishEnum::PUBLISHED->value);
            })
            ->with(['post_relation.post' => function ($query) {
                $query->where('publish_status', PublishEnum::PUBLISHED->value);
            }, 'post_relation.post.files.file', 'post_relation.post.category.relationable'])
            ->first()?->post_relation
            ->filter(fn($relation) => $relation->post &&
                !in_array($relation->post->id, $excludedPostIds)
            )
            ->sortByDesc(fn($relation) => $relation->post->publish_date ?? null)
            ->take(5);

        $humanity_stories = $this->getLastCategoryPosts('قصص إنسانية');
        $alahed_release = $this->getLastCategoryPosts('اصدارات العهد');
        $last_videos = VideoTrack::query()->with(['files.file'])->latest()->take(5)->get();
        return view('livewire.main.alahed.index-page',
            [
                'main_news' => $main_news ?? collect(),
                'sub_news' => $sub_news ?? collect(),
                'last_videos' => $last_videos ?? collect(),
                'statistics' => $statistics ?? collect(),
                'humanity_stories' => $humanity_stories ?? collect(),
                'alahed_release' => $alahed_release ?? collect(),
                'last_posts' => $last_posts ?? collect(),
            ]);
    }

    function getLastCategoryPosts(string $categoryTitle): ?Collection
    {
        return Category::where('category_title', $categoryTitle)
            ->whereHas('post_relation.post', function ($q) {
                $q->where('publish_status', PublishEnum::PUBLISHED->value);
            })
            ->with(['post_relation.post' => function ($query) {
                $query->where('publish_status', PublishEnum::PUBLISHED->value);
            }, 'post_relation.post.files.file', 'post_relation.post.category.relationable'])
            ->first()?->post_relation
            ->sortByDesc(fn($relation) => $relation->post->publish_date ?? null)
            ->take(5);
    }

    public function openModal($video_id, $video_type): void
    {
        if ($video_type == VideoTypeEnum::YOUTUBE->value) {
            $this->openYoutubeModal($video_id);
        }
        if ($video_type == VideoTypeEnum::LOCAL->value) {
            $this->openVideoModal($video_id);
        }
    }

    public function openYoutubeModal($video_id): void
    {
        $this->selected_video = VideoTrack::find($video_id)?->url;
        $this->dispatch('open_youtube_modal', video_path: $this->selected_video);
    }

    public function openVideoModal($video_id): void
    {
        $this->selected_video = VideoTrack::find($video_id)?->url;
        $this->dispatch('open_video_modal', video_path: file_url($this->selected_video));
    }

    public function openPDFModal($post_id): void
    {
        $this->selected_pdf = Post::find($post_id)?->pdf_file;
        $this->dispatch('open_pdf_modal', pdf_path: file_url($this->selected_pdf));
    }

    /**
     * @throws ValidationException
     */
    public function createContactUs(): void
    {
        $validate = Validator::make($this->state, [
            'full_name' => 'required|string|max:50',
            'email' => 'required|email',
            'message' => 'required|string|max:300',
        ])->validate();

        \App\Models\ContactUs::create($validate);

        $this->reset('state');
        session()->flash('contact_success', 'تم إرسال البيانات بنجاح');
        $this->dispatch('hide_contact_form');
    }
}
