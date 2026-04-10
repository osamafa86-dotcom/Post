<?php

namespace App\Models;

use App\Enums\AlertTypeEnum;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;

    protected $guarded = [];
//    protected $casts =
//        [
//            'type' => AlertTypeEnum::class ,
//        ];
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
