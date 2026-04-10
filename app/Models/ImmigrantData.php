<?php

namespace App\Models;

use App\Enums\DataPageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ImmigrantData extends Model
{
    use HasFactory;

    protected $guarded = [];

//    protected $casts =
//        [
//            'type' => DataPageEnum::class,
//         ];


    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', ImmigrantData::class);
    }

}
