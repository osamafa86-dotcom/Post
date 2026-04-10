<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'period',
        'item_id',
        'item_name',
        'item_description',
        'item_amount',
        'item_currency',
        'item',
        'notes',
    ];

    protected $casts = [
        'item' => 'array',
        'notes' => 'array',
    ];


}
