<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipantSocialMedia extends Model
{
    use HasFactory , SoftDeletes;
    use Tenantable;

    protected $guarded = [];


    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }


    public function icon(): BelongsTo
    {
        return $this->belongsTo(Icon::class , 'social_media_icon');
    }
}
