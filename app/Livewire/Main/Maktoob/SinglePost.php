<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Favoirate;
use App\Models\NewsLetterEmails;
use App\Models\Participant;
use App\Models\Post;
use App\Models\SpecialPage;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class SinglePost extends Component
{

    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;
    public $post;
    public $latestPost, $sameCategoryPost;

    public $side_advert, $bottom_advert;

    public $email;

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.single-post');
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');
        $this->post = Post::with(['categories.relationable', 'tags.relationable','authors.relationable.files.file','authors.relationable.participant_social_media','files.file'])->withTrashed()->where(function ($q) {
            $q->where('slug', $this->slug)
                ->where('id', $this->id);
        })->first();

        $this->latestPost = Post::where('id', '!=', $this->post?->id)->with(['files.file', 'category.relationable'])->latest('publish_date')->take('5')->get();

        $this->sameCategoryPost = Post::where('id', '!=', $this->post?->id)
            ->WhereHas('categories', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('id', $this->post?->category?->relationable?->id);
                    }
                );
            })->with(['files.file','category.relationable','authors.relationable.files.file'])->latest('publish_date')->take(10)->get();
        $this->side_advert = Advertisement::query()->where('place', AdvertisementPlaceEnum::SIDE_ADS->value)
            ->latest()->first();

        $this->bottom_advert = Advertisement::query()->where('place', AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();

        if (!Session::has('viewed_post_' . $this->post?->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->post->views()->create([
                    'last_viewed_at' => $now,
                    'views_number' => 1,
                ]);
            }
            Session::put('viewed_post_' . $this->post?->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int)config('app.view_expiration_hours'))
            ]);
        }
    }

    public function toggleBookmark(int $postId, string $type = 'bookmark'): void
    {
        $userId = auth('web_user')->id();

        if ($userId) {
            $bookmark = Favoirate::where([
                ['user_id', $userId],
                ['post_id', $postId],
                ['type', $type],
            ])->first();

            if ($bookmark) {
                $bookmark->delete();
            } else {
                Favoirate::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function addNewsLetterEmail(): void
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset(['email']);
    }

}
