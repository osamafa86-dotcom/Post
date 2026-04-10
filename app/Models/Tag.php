<?php

namespace App\Models;

use App\Enums\TagTypeEnum;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable;
    use HasLanguage;


    protected $guarded = [];
//    protected $casts =
//        [
//            'tag_type' => TagTypeEnum::class,
//         ];
    public function post_relation(): HasMany
    {
        return $this->hasMany(PostRelation::class,'relationable_id')->where('relationable_type', Category::class);
    }
    public function material_relation(): HasMany
    {
        return $this->hasMany(MaterialRelation::class,'relationable_id')->where('relationable_type', Tag::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
