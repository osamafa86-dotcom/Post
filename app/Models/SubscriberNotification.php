<?php

namespace App\Models;

use App\Enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SubscriberNotification extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'date'=>'datetime',
//        'type' => NotificationTypeEnum::class,
    ];
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', SubscriberNotification::class);
    }
    public function views()
    {
        return $this->hasMany(SubscriberNotificationView::class, 'subscriber_notification_id');
    }

    public function viewers()
    {
        return $this->belongsToMany(
            User::class,
            'subscriber_notification_views',
            'subscriber_notification_id',
            'subscriber_id'
        )->withPivot(['viewing_date', 'is_favorite'])->withTimestamps();
    }
}
