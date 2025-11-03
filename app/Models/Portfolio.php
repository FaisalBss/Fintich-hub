<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'type',
        'quantity',
        'average_price',
    ];


    protected $casts = [
        'quantity' => 'decimal:8',
        'average_price' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

