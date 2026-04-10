<?php

namespace App\Models;

use App\Enums\MaterialTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class MaterialAlbum extends Model
{
    use HasFactory , SoftDeletes;


    protected $guarded = [];

//    protected $casts =
//        [
//            'type' => MaterialTypeEnum::class,
//        ];
    public function album_materials(): HasMany
    {
        return $this->hasMany(Material::class , 'album_id');
    }

    public function latest_album_materials(): HasOne
    {
        return $this->hasOne(Material::class , 'album_id')->latest();
    }

    public function album_links(): HasMany
    {
        return $this->hasMany(AlbumLinks::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(MaterialAlbumRelation::class, 'material_album_id')
            ->where('relationable_type', Category::class);
    }

    public function getCategoriesAttribute(): ?Collection
    {
        return $this->categories()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function category(): HasOne
    {
        return $this->hasOne(MaterialAlbumRelation::class, 'material_album_id')
            ->where('relationable_type', Category::class)
            ->where('relationable_is_main', 1);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(MaterialAlbumRelation::class, 'material_album_id')
            ->where('relationable_type', Tag::class);
    }

    public function getTagsAttribute(): ?Collection
    {
        return $this->tags()?->get()->pluck('relationable');  // Accessing all related Category models
    }

    public function tag(): HasOne
    {
        return $this->hasOne(MaterialAlbumRelation::class, 'material_album_id')
            ->where('relationable_type', Tag::class)
            ->where('relationable_is_main', 1);
    }

    public function getTagAttribute()
    {
        return $this->tag()?->first()?->relationable;  // Accessing the Category model directly
    }


    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', MaterialAlbum::class);
    }

    public function specialFile(): BelongsTo
    {
        return $this->belongsTo(SpecialFile::class);
    }
}
