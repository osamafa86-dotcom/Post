<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\PublishEnum;
use App\Enums\UserStatusEnum;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Tenantable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'user_status',
        'password',
        'default_dashboard_language',
        'manager_id',
        'avatar',
        'team_id',
        'old_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
//        'user_status' => UserStatusEnum::class,
        'preferences' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
    public function getPreference($key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
    }


    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getAvatarAttribute(): string
    {
        return $this->attributes['avatar']
            ? Storage::url($this->attributes['avatar'])
            : asset('assets/dashboard/images/user.webp');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function pendingPosts(): HasMany
    {
        return $this->hasMany(Post::class)
            ->where('publish_status', PublishEnum::DRAFT->value);
    }

    public function employeesPendingPosts()
    {
        return Post::where('publish_status','=', PublishEnum::DRAFT->value)
            ->whereIn('publisher_id',$this->employees()->pluck('id'));
    }

    public function employeesPendingVideos()
    {
        return VideoTrack::where('publish_status','=', PublishEnum::DRAFT->value)
            ->whereIn('publisher_id',$this->employees()->pluck('id'));
    }

    public function employeesPendingPodcasts()
    {
        return PodcastTrack::where('publish_status','=', PublishEnum::DRAFT->value)
            ->whereIn('publisher_id',$this->employees()->pluck('id'));
    }

    public function special_pages(): HasMany
    {
        return $this->hasMany(SpecialPage::class);
    }

    public function is_admin()
    {
        return $this->id == 1;
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favoirate::class)->where('type','favorite');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Favoirate::class)->where('type','bookmark');
    }

    public function employees()
    {
        return User::whereIn('manager_id',$this->roles->pluck('id'));
    }

    public function managerRole(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class,'manager_id','id');
    }

    public function managers()
    {
        return $this->managerRole->users;
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class,'user_notification');
    }

    public function newNotifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class,'user_notification')->where('read','=',0)
            ->orderBy('id','desc');
    }


    public function details()
    {
        return $this->hasOne(UserDetails::class);
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class,'subscriber_id','id')->where('subscription_end' ,'>=' , today());
    }
    public function subscriberNotifications()
    {
        return $this->belongsToMany(
            SubscriberNotification::class,
            'subscriber_notification_views',
            'subscriber_id',
            'subscriber_notification_id'
        )
            ->withPivot(['viewing_date', 'is_favorite'])
            ->withTimestamps();
    }

    public function viewedNotifications()
    {
        return $this->hasMany(SubscriberNotificationView::class, 'subscriber_id');
    }

    public function unreadNotifications()
    {
        return SubscriberNotification::where('type', \App\Enums\NotificationTypeEnum::NOTIFICATION->value)
            ->whereDoesntHave('views', function ($q) {
                $q->where('subscriber_id', $this->id);
            });
    }
}
