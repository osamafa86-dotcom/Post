<?php

namespace App\Models;

use App\Enums\LinkPosition;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;
    use Tenantable;
    use HasLanguage;

    protected $guarded = [];
//    protected $casts =
//        [
//            'position' => LinkPosition::class,
//         ];
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }
}
