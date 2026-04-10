<?php

namespace App\Models;

use App\Enums\PublishEnum;
use App\Traits\Loggable;
use App\Traits\PublishableBoot;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PodcastTrack extends Model
{
    use HasFactory,SoftDeletes;
    use PublishableBoot;
    use Loggable;
    use Tenantable;

    public string $model_name = 'podcast_tracks';

    protected $guarded = [];
//    protected $casts =
//        [
//            'publish_status' => PublishEnum::class,
//         ];

    public function podcast_album(): BelongsTo
    {
        return $this->belongsTo(PodcastAlbum::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', PodcastTrack::class);
    }

    public function sortable(): MorphOne
    {
        return $this->morphOne(SortData::class, 'sortable');
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }
}
