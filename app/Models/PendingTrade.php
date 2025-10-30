<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stock_symbol',
        'quantity',
        'price_per_share',
        'total_cost',
        'otp',
        'otp_expires_at'
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];
}
