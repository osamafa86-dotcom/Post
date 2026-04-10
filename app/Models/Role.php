<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable;

    public $table = 'notification_roles';

    protected $fillable = [
        'model',
        'action',
        'permission_target_id',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'permission_target_id', 'id');
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
}
