<?php

namespace App\Models;

use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialFile extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable;

    use HasLanguage;
    protected $guarded = [];

    public function post_relation(): HasMany
    {
        return $this->hasMany(PostRelation::class, 'relationable_id')->where('relationable_type', SpecialFile::class);
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', SpecialFile::class);
    }

    public function getThumbnailAttribute()
    {
        return $this->files()->first()?->file?->path;
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
