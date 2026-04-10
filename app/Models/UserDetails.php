<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\TagTypeEnum;
use App\Traits\PublishableBoot;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

class UserDetails extends Model
{
    use HasFactory;
    use Tenantable;

    public string $model_name = 'UserDetails';

    protected $table = 'user_details';

    protected $dates = [
        'birthday',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected $casts =
        [
            'gender' => GenderEnum::class,
         ];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function dialects(): HasMany
    {
//        $this->relation(Participant::class)
//            ->whereHasMorph('relationable', [Participant::class], function ($query) {
//                $query->where('type',  ParticipantTypeEnum::PRESENTERS->value);
//            });
        return $this->hasMany(UserDetailRelation::class, 'user_detail_id')
            ->whereHasMorph('relationable', [Tag::class], function ($query) {
                $query->where('tag_type',  TagTypeEnum::DIALECTS->value);
            });
    }

    public function getDialectsAttribute(): ?Collection
    {
        return $this->dialects()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function dialect(): HasOne
    {
        return $this->hasOne(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getDialectAttribute()
    {
        return $this->dialect()?->first()?->relationable;  // Accessing the Category model directly
    }

    public function languages(): HasMany
    {
//        $this->relation(Participant::class)
//            ->whereHasMorph('relationable', [Participant::class], function ($query) {
//                $query->where('type',  ParticipantTypeEnum::PRESENTERS->value);
//            });
        return $this->hasMany(UserDetailRelation::class, 'user_detail_id')
            ->whereHasMorph('relationable', [Tag::class], function ($query) {
                $query->where('tag_type',  TagTypeEnum::LANGUAGES->value);
            });
    }

    public function getLanguagesAttribute(): ?Collection
    {
        return $this->languages()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function language(): HasOne
    {
        return $this->hasOne(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getLanguageAttribute()
    {
        return $this->language()?->first()?->relationable;  // Accessing the Category model directly
    }

    public function interests(): HasMany
    {
//        $this->relation(Participant::class)
//            ->whereHasMorph('relationable', [Participant::class], function ($query) {
//                $query->where('type',  ParticipantTypeEnum::PRESENTERS->value);
//            });
        return $this->hasMany(UserDetailRelation::class, 'user_detail_id')
            ->whereHasMorph('relationable', [Tag::class], function ($query) {
                $query->where('tag_type',  TagTypeEnum::INTEREST->value);
            });
    }

    public function getInterestsAttribute(): ?Collection
    {
        return $this->interests()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function interest(): HasOne
    {
        return $this->hasOne(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getInterestAttribute()
    {
        return $this->interest()?->first()?->relationable;  // Accessing the Category model directly
    }


    public function tags(): HasMany
    {
        return $this->hasMany(UserDetailRelation::class, 'user_detail_id')
            ->whereHasMorph('relationable', [Tag::class], function ($query) {
                $query->where('tag_type',  TagTypeEnum::USERS->value);
            });
    }

    public function getTagsAttribute(): ?Collection
    {
        return $this->tags()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function tag(): HasOne
    {
        return $this->hasOne(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getTagAttribute()
    {
        return $this->tag()?->first()?->relationable;  // Accessing the Category model directly
    }


    public function categories(): HasMany
    {
        return $this->hasMany(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Category::class);
    }

    public function getCategoriesAttribute(): ?Collection
    {
        return $this->categories()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function category(): HasOne
    {
        return $this->hasOne(UserDetailRelation::class, 'user_detail_id')
            ->where('relationable_type', Category::class)
            ->where('relationable_is_main', 1);
    }

    public function getCategoryAttribute()
    {
        return $this->category()?->first()?->relationable;  // Accessing the Category model directly
    }


    public function userDetails_social_media(): HasMany
    {
        return $this->hasMany(UserDetailSocialMedia::class , 'user_detail_id');
    }
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }


}
