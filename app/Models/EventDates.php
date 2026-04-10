<?php

namespace App\Models;

use App\Enums\DayEnum;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class EventDates extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;

    protected $guarded = [];
//    protected $casts =
//        [
//            'day' => DayEnum::class ,
//        ];
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
