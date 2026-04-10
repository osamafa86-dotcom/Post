<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\PublishableBoot;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class View extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function viewable()
    {
        return $this->morphTo();
    }

}
