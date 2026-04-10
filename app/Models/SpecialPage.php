<?php

namespace App\Models;

use App\Enums\PublishEnum;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialPage extends Model
{
    use HasFactory,SoftDeletes;
    use Tenantable;
    use HasLanguage;

    protected $guarded = [];
//    protected $casts =
//        [
//            'publish_status' => PublishEnum::class,
//         ];

    public function user():belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }
    public function files(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')->where('model_type', SpecialPage::class);
    }
}
