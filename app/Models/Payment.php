<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'amount',
        'status',
        'credited_at',
        'payment_url',
        'payload',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
        'credited_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
