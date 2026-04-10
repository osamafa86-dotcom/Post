<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Enums\NotificationTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Favoirate;
use App\Models\Post;
use App\Models\SubscriberNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MainDashboard extends Component
{

    #[Computed]
    public function favouritePosts()
    {
        return auth('web_user')->user()->favourites()->count();
    }

    #[Computed]
    public function isSubscribed()
    {
        return auth('web_user')->user()->subscription;
    }

    #[Computed]
    public function bookmarkedPosts()
    {
        return auth('web_user')->user()->bookmarks()->count();
    }

    #[Computed]
    public function events()
    {
        return SubscriberNotification::where('type', NotificationTypeEnum::EVENT->value)->with(['files.file'])->paginate(10, ['*'], 'events');
    }

    #[Computed]
    public function newsCount()
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)->count();
    }

    #[Computed]
    public function latestNews()
    {
//        return Post::where('publish_status', PublishEnum::PUBLISHED->value)->with(['files.file', 'category.relationable', 'authors.relationable'])->latest()->take(6)->get();
        $hours = config('app.available_date_after');
        return Post::whereHas('post_special_content', function ($q) use ($hours) {
            $q->whereNotNull('available_date')
                ->where('available_date', '<=', now())
                ->when($hours > 0, function ($q) use ($hours) {
                    $q->where('available_date', '>=', now()->subHours($hours));
                });
        })
            ->with(['files.file', 'category.relationable', 'authors.relationable'])
            ->latest()
            ->take(6)
            ->get();
    }

    #[Computed]
    public function notificationCount()
    {
        $user = Auth::guard('web_user')->user();
        if (!$user) {
            return 0;
        }
        $notifications = SubscriberNotification::where('type', NotificationTypeEnum::NOTIFICATION->value);
        $notificationsCount = $notifications->count();
        $viewCount = $user->viewedNotifications()
            ->whereIn('subscriber_notification_id', $notifications->pluck('id'))
            ->count();
        return $notificationsCount - $viewCount;
    }

    public function toggleBookmark(int $postId, string $type = 'bookmark'): void
    {
        $userId = auth('web_user')->id();

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


    #[Layout('components.layouts.main.maktoob.user_dashboard.main')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.main-dashboard');
    }
}
