<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    protected $fillable = [
        'ip',
        'user_agent',
        'device_type',
        'referrer',
        'country',
        'visited_at',
    ];
}
