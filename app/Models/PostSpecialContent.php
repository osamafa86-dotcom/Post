<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostSpecialContent extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];
    protected $casts=[
        'available_date'=>'datetime'
    ];
    public function post(): BelongsTo{
        return $this->belongsTo(Post::class);
    }
}
