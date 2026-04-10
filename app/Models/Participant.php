<?php

namespace App\Models;

use App\Enums\ParticipantTypeEnum;
use App\Enums\UserDetailsTypeEnum;
use App\Enums\UserStatusEnum;
use App\Traits\HasLanguage;
use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable, Loggable;
    use HasLanguage;

    protected $guarded = [];

    protected $casts =
        [
//            'type' => ParticipantTypeEnum::class ,
            'participants_data' => 'array',
        ];

    public function participant_social_media(): HasMany
    {
        return $this->hasMany(ParticipantSocialMedia::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'author_id');
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', Participant::class);
    }


}
