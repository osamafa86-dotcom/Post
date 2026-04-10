<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    use Tenantable;

    protected $guarded=[];

    public function isPinned()
    {
        return $this->hasOne(PinnedFile::class, 'file_id');
    }
    public function posts()
    {
        return $this->hasManyThrough(
            \App\Models\Post::class,
            \App\Models\ModelHasFile::class,
            'file_id', // Foreign key on model_has_files table
            'id', // Foreign key on posts table
            'id', // Local key on files table
            'model_id' // Local key on model_has_files table
        )->where('model_has_files.model_type', \App\Models\Post::class);
    }
}
