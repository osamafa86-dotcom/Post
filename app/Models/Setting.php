<?php

namespace App\Models;

use App\Traits\HasLanguage;
use App\Traits\Loggable;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory, SoftDeletes;
    use Tenantable, Loggable;
    use HasLanguage;

    protected $guarded = [];


    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', Setting::class);
    }
    public function footerLogo(): ?string
    {
        $file = $this->files()
            ->where('model_column', 'footer_logo')
            ->with('file')
            ->first();

        return $file?->file ? Storage::url($file->file->path) : null;
    }
}
