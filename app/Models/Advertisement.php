<?php

namespace App\Models;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\AdvertisementTypeEnum;
use App\Enums\AdvertisementUrlTargetEnum;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;
    use HasLanguage;

    protected $guarded = [];
    protected $appends = [
//        "place",
//        "type",
//        "url_target",
    ];
//     protected $casts =
//         [
//             'type' => AdvertisementTypeEnum::class ,
//             'place' => AdvertisementPlaceEnum::class,
//             'url_target' => AdvertisementUrlTargetEnum::class,
//         ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', Advertisement::class);
    }
}
