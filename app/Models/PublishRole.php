<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class PublishRole extends Model
{
    use HasFactory , SoftDeletes;
    use Tenantable;

    protected $guarded = [];

    public function from_role(): BelongsTo
    {
        return $this->belongsTo(Role::class,'from_role_id','id');
    }

    public function to_role(): BelongsTo
    {
        return $this->belongsTo(Role::class,'to_role_id','id');
    }
}
