<?php

namespace App\Models;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Application extends Model
{
    use HasFactory;

    protected $guarded = [];

//    protected $casts =
//        [
//            'gender' => GenderEnum::class ,
//        ];
    public function course_application(): BelongsTo
    {
        return $this->belongsTo(Event::class , 'course');
    }
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
