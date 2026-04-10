<?php

namespace App\Models;

use App\Enums\ReelTypeEnum;
use App\Traits\HasLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reel extends Model
{
    use HasFactory;
    use HasLanguage;


    protected $guarded = [];
//    protected $casts =
//        [
//            'reel_type' => ReelTypeEnum::class,
//         ];
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', Reel::class);
    }
}
