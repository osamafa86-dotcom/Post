<?php

namespace App\Models;

use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NavbarLink extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;
    use HasLanguage;

    protected $guarded = [];
//    protected $casts =
//        [
//            'link_status' => LinkStatusEnum::class ,
//            'link_open' => LinkUrlTargetEnum::class ,
//         ];
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavbarLink::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavbarLink::class, 'parent_id')->orderBy('link_order');
    }
}
