<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetailSocialMedia extends Model
{
    use HasFactory , SoftDeletes;
    use Tenantable;

    protected $guarded = [];


    public function userDetail(): BelongsTo
    {
        return $this->belongsTo(UserDetails::class);
    }


    public function icon(): BelongsTo
    {
        return $this->belongsTo(Icon::class , 'social_media_icon');
    }
}
