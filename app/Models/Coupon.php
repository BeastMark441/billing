<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'max_uses',
        'expires_at',
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];
}

