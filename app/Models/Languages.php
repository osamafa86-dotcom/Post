<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Languages extends Model
{
    use HasFactory;
    use Tenantable;

    protected $table = 'languages';

    protected $fillable = [
        'name'
    ];

    public function user_details():HasMany
    {
        return $this->hasMany(UserDetails::class, 'user_details_id');
    }

    public function users()
    {
        return User::whereIn('id',$this->user_details()->pluck('user_id'))->get();
    }
}
