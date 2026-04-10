<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PodcastAlbum extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable, Loggable;

    protected $guarded = [];

    public function podcast_tracks(): HasMany
    {
        return $this->hasMany(PodcastTrack::class ,  'podcast_album_id');
    }
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function podcast_album_links(): HasMany
    {
        return $this->hasMany(PodcastAlbumLinks::class);
    }
    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', PodcastAlbum::class);
    }
}
