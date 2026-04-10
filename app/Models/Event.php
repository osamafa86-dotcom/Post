<?php

namespace App\Models;

use App\Enums\DateTypeEnum;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Event extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable;
    use HasLanguage;

    protected $guarded = [];
//    protected $casts =
//        [
//            'date_type' => DateTypeEnum::class ,
//        ];


    public function categories(): HasMany
    {
        return $this->hasMany(EventRelation::class, 'event_id')
            ->where('relationable_type', Category::class);
    }

    public function category(): HasOne
    {
        return $this->hasOne(EventRelation::class, 'event_id')
            ->where('relationable_type', Category::class)
            ->where('relationable_is_main', 1);
    }

    public function presenters(): HasMany
    {
        return $this->hasMany(EventRelation::class, 'event_id')
            ->where('relationable_type', Participant::class);
    }

    public function presenter(): HasOne
    {
        return $this->hasOne(EventRelation::class, 'event_id')
            ->where('relationable_type', Participant::class)
            ->where('relationable_is_main', 1);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(EventRelation::class, 'event_id')
            ->where('relationable_type', Tag::class);
    }

    public function tag(): HasOne
    {
        return $this->hasOne(EventRelation::class, 'event_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function event_dates(): HasMany
    {
        return $this->hasMany(EventDates::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', Event::class);
    }
}
