<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    use Tenantable;

    protected $table = 'notifications';

    protected $fillable = [
        'alert_text',
        'alert_link',
    ];

}
