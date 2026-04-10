<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoAlbum extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable, Loggable;

    protected $guarded = [];

    public function video_tracks(): HasMany
    {
        return $this->hasMany(VideoTrack::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', VideoAlbum::class);
    }
}
