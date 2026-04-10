<?php

namespace App\Models;

use App\Enums\CategoryStyleEnum;
use App\Enums\CategoryTypeEnum;
use App\Traits\HasLanguage;
use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, Tenantable, Loggable;
    use HasLanguage;

    protected $guarded = [];
//    protected $casts =
//        [
//            'category_type' => CategoryTypeEnum::class ,
//            'category_style' => CategoryStyleEnum::class,
//        ];
    public function posts(): HasMany
    {
        return $this->relation(Post::class);
    }

    private function relation($type, $hasOne = false): HasOne|HasMany
    {
        return $hasOne
            ? $this->hasOne(PostRelation::class, 'post_id')->where('relationable_type', $type)->where('relationable_is_main', 1)
            : $this->hasMany(PostRelation::class, 'post_id')->where('relationable_type', $type);
    }


    public function post_relation(): HasMany
    {
        return $this->hasMany(PostRelation::class,'relationable_id')->where('relationable_type', Category::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    public function event_relation(): HasMany
    {
        return $this->hasMany(EventRelation::class, 'relationable_id')->where('relationable_type', Category::class);
    }

    public function material_relation(): HasMany
    {
        return $this->hasMany(MaterialRelation::class, 'relationable_id')->where('relationable_type', Category::class);
    }

    public function main_material_relation(): HasMany
    {
        return $this->hasMany(MaterialRelation::class, 'relationable_id')->where([['relationable_type', Category::class],['relationable_is_main',1]]);
    }

    public function material_album_relation(): HasMany
    {
        return $this->hasMany(MaterialAlbumRelation::class, 'relationable_id')->where('relationable_type', Category::class);
    }


    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', Category::class);
    }

    public function background()
    {
        return $this->hex2rgba($this->category_style, 0.1) ?? $this->hex2rgba('#000000', 0.1);
    }


    function hex2rgba($color, $opacity)
    {

        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $output = "rgba($r, $g, $b, $opacity)";

        return $output;
    }
}
