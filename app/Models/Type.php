<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable, Loggable;


    protected $guarded = [];
    protected $casts= [
        'show_index' => 'boolean',
    ];
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
