<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Enums\NotificationTypeEnum;
use App\Models\SubscriberNotification;
use App\Models\SubscriberNotificationView;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class Notifications extends Component
{
    public array $groupedNotifications = [];
    public array $groupedEvents = [];
    public $notificationCount = 5;
    public $eventCount = 5;
    public int $totalNotifications;
    public int $totalEvents;

    #[Computed]
    public function notifications()
    {
        return \App\Models\SubscriberNotification::with(['files.file'])
            ->where('type', NotificationTypeEnum::NOTIFICATION->value)
            ->latest('date')
            ->take($this->notificationCount)->get();
    }

    #[On('resetNotification')]
    public function resetNotificationCount()
    {
        $this->reset(['notificationCount', 'eventCount']);
        $this->groupedNotifications = $this->buildGroupedNotifications();
    }

    #[On('resetEvent')]
    public function resetEventCount()
    {
        $this->reset(['notificationCount', 'eventCount']);
        $this->groupedEvents = $this->buildGroupedEvents();
    }

    public function loadMoreNotifications($type = 'notifications')
    {
        if ($type == 'notifications') {
            $this->notificationCount += 10;
            $this->groupedNotifications = $this->buildGroupedNotifications();
        } elseif ($type == 'events') {
            $this->eventCount += 10;
            $this->groupedEvents = $this->buildGroupedEvents();
        }
    }

    private function buildGroupedNotifications(): array
    {
        return $this->notifications
            ->map(function ($n) {
                $label = $this->dateLabel($n->date);
                return [
                    'group' => $label,
                    'id' => $n->id,
                    'title' => $n->title,
                    'description' => $n->description,
                    'url' => $n->url,
                    'icon' => $this->iconClass($n),
                    'created_at' => $n->date,
                    'file' => $n->files?->file,
                    'read' => SubscriberNotificationView::where([
                        ['subscriber_id', \Auth::guard('web_user')->id()],
                        ['subscriber_notification_id', $n->id]
                    ])->exists(),
                ];
            })
            ->groupBy('group')
            ->toArray();
    }

    private function buildGroupedEvents(): array
    {
        return $this->events
            ->map(function ($n) {
                $label = $this->dateLabel($n->date);
                return [
                    'group' => $label,
                    'id' => $n->id,
                    'title' => $n->title,
                    'description' => $n->description,
                    'url' => $n->url,
                    'icon' => $this->iconClass($n),
                    'created_at' => $n->date,
                    'file' => $n->files?->file,
                    'read' => SubscriberNotificationView::where([
                        ['subscriber_id', \Auth::guard('web_user')->id()],
                        ['subscriber_notification_id', $n->id]
                    ])->exists(),
                ];
            })
            ->groupBy('group')
            ->toArray();
    }

    #[Computed]
    public function events()
    {
        return \App\Models\SubscriberNotification::with(['files.file'])
            ->where('type', NotificationTypeEnum::EVENT->value)
            ->latest('date')
            ->take($this->eventCount)->get();
    }

    public function mount()
    {
        $this->totalNotifications = \App\Models\SubscriberNotification::where('type', NotificationTypeEnum::NOTIFICATION->value)->count();
        $this->totalEvents = \App\Models\SubscriberNotification::where('type', NotificationTypeEnum::EVENT->value)->count();

        $this->groupedNotifications = $this->buildGroupedNotifications();
        $this->groupedEvents = $this->buildGroupedEvents();

    }

    public function markAllAsRead()
    {
        foreach ($this->notifications as $notification) {
            SubscriberNotificationView::updateOrCreate([
                'subscriber_id' => \Auth::guard('web_user')->id(),
                'subscriber_notification_id' => $notification?->id,
            ], [
                'viewing_date' => now(),
            ]);
        }
        unset($this->notifications);
        $this->groupedNotifications = $this->buildGroupedNotifications();
    }


    public function markAllEventsAsRead()
    {
        foreach ($this->events as $notification) {
            SubscriberNotificationView::updateOrCreate([
                'subscriber_id' => \Auth::guard('web_user')->id(),
                'subscriber_notification_id' => $notification?->id,
            ], [
                'viewing_date' => now(),
            ]);
        }
        unset($this->events);
        $this->groupedEvents = $this->buildGroupedEvents();
    }

    public function markAsRead($id)
    {
        SubscriberNotificationView::updateOrCreate([
            'subscriber_id' => \Auth::guard('web_user')->id(),
            'subscriber_notification_id' => $id,
        ], [
            'viewing_date' => now(),
        ]);
        unset($this->notifications);
    }

    private function dateLabel(Carbon $dt): string
    {
        if ($dt->isToday()) return __('Today');
        if ($dt->isYesterday()) return __('Yesterday');
        return $dt->translatedFormat('d M Y');
    }

    private function iconClass($notif): string
    {
        return match ($notif->type ?? 'info') {
            'article' => 'article',
            'comment' => 'comment',
            'follow' => 'follow',
            default => 'info',
        };
    }

    #[Layout('components.layouts.main.maktoob.user_dashboard.main')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.notifications');
    }
}
