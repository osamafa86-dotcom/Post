<?php

namespace App\Models;

use App\Enums\ParticipantTypeEnum;
use App\Traits\HasLanguage;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostRelation extends Model
{
    use HasFactory, SoftDeletes, Tenantable;
    use HasLanguage;

    protected $guarded = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function relationable(): MorphTo
    {
        return $this->morphTo();
    }

    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class,'relationable_id','id');
    }
    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class,'relationable_id','id');
    }

    public function authors(): BelongsTo
    {
        return $this->belongsTo(Participant::class,'relationable_id','id')->where('type',ParticipantTypeEnum::AUTHORS->value);
    }

    public function publishers(): BelongsTo
    {
        return $this->belongsTo(Participant::class,'relationable_id','id')->where('type',ParticipantTypeEnum::PUBLISHERS->value);
    }

    public function resources(): BelongsTo
    {
        return $this->belongsTo(Participant::class,'relationable_id','id')->where('type',ParticipantTypeEnum::RESOURCES->value);
    }

}
