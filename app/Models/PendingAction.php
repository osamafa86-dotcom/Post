<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendingAction extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;

    protected $guarded = [];

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

}
