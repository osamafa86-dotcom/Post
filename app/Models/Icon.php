<?php

namespace App\Models;

use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;


class Icon extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable;
    use HasLanguage;


    protected $guarded = [];

public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

}
