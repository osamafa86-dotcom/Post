<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialRelation extends Model
{
    use HasFactory ,SoftDeletes;
    use Tenantable;

    protected $guarded = [];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class,'relationable_id','id');
    }
    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class,'relationable_id','id');
    }
    public function relationable(): MorphTo
    {
        return $this->morphTo();
    }
}
