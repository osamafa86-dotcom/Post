<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialAlbumRelation extends Model
{
    use HasFactory ,SoftDeletes;
    use Tenantable;

    protected $guarded = [];

    public function materialAlbum(): BelongsTo
    {
        return $this->belongsTo(MaterialAlbum::class);
    }

    public function relationable(): MorphTo
    {
        return $this->morphTo();
    }
}
