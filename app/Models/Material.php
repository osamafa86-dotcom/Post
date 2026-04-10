<?php

namespace App\Models;

use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\VideoTypeEnum;
use App\Traits\HasLanguage;
use App\Traits\Loggable;
use App\Traits\PublishableBoot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Psy\VarDumper\Presenter;

class Material extends Model
{
    use HasFactory, SoftDeletes, Loggable;
    use HasLanguage;
    use PublishableBoot;


    protected $guarded = [];

//    protected $casts =
//        [
//            'type' => MaterialTypeEnum::class,
//            'video_type' => VideoTypeEnum::class,
//            'publish_status' => PublishEnum::class,
//         ];
    private function relation($type, $hasOne = false): HasOne|HasMany
    {
        return $hasOne
            ? $this->hasOne(MaterialRelation::class, 'material_id')->where('relationable_type', $type)->where('relationable_is_main', 1)
            : $this->hasMany(MaterialRelation::class, 'material_id')->where('relationable_type', $type);
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(MaterialRelation::class, 'material_id')
            ->where('relationable_type', Category::class);
    }

    public function category(): HasOne
    {
        return $this->hasOne(MaterialRelation::class, 'material_id')
            ->where('relationable_type', Category::class)
            ->where('relationable_is_main', 1);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(MaterialRelation::class, 'material_id')
            ->where('relationable_type', Tag::class);
    }

    public function tag(): HasOne
    {
        return $this->hasOne(MaterialRelation::class, 'material_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getTagAttribute()
    {
        return $this->tag()?->first()?->relationable;  // Accessing the Category model directly
    }


    public function presenters(): HasMany
    {
        return $this->relation(Participant::class)
            ->whereHasMorph('relationable', [Participant::class], function ($query) {
                $query->where('type', ParticipantTypeEnum::PRESENTERS->value);
            });
    }

    public function presenter(): HasOne
    {
        return $this->relation(Participant::class, true);
    }

    public function guests(): HasMany
    {

        return $this->relation(Participant::class)
            ->whereHasMorph('relationable', [Participant::class], function ($query) {
                $query->where('type', ParticipantTypeEnum::GUESTS->value);
            });
    }

    public function guest(): HasOne
    {
        return $this->relation(Participant::class, true);
    }

    public function getGuestAttribute()
    {
        return $this->guest()->first()?->relationable;
    }

    public function material_album(): BelongsTo
    {
        return $this->belongsTo(MaterialAlbum::class, 'album_id');
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', Material::class);
    }

    public function sortable(): MorphOne
    {
        return $this->morphOne(SortData::class, 'sortable');
    }

    public function specialFile(): BelongsTo
    {
        return $this->belongsTo(SpecialFile::class);
    }


}
