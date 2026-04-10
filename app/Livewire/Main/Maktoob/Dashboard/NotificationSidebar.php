<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Models\SubscriberNotificationView;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationSidebar extends Component
{
    #[Computed]
    public function notifications()
    {
        return Auth::guard('web_user')->user()->unreadNotifications()->get();
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
    }

    public function render()
    {
        return view('livewire.main.maktoob.dashboard.notification-sidebar');
    }
}
