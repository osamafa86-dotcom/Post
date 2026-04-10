<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinnedFile extends Model
{
    //
    protected $guarded=[];

    protected $casts = [
        'pinned_at' => 'datetime',
    ];
    public function file()
    {
        return $this->belongsTo(Files::class,'file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
