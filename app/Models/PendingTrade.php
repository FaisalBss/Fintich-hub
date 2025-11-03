<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'type',
        'quantity',
        'price_per_unit',
        'total_cost',
        'otp',
        'expires_at',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];
}

