<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PodcastAlbumLinks extends Model
{
    use HasFactory;
    use Tenantable;

    protected $guarded = [];

    public function podcast_album(): BelongsTo
    {
        return $this->belongsTo(PodcastAlbum::class);
    }
}
