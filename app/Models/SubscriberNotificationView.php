<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SubscriberNotificationView extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function notification()
    {
        return $this->belongsTo(SubscriberNotification::class, 'subscriber_notification_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
