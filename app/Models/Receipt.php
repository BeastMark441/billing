<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'receipt_number',
        'type',
        'amount',
        'currency',
        'payment_method',
        'related_type',
        'related_id',
        'seller',
        'buyer',
        'items',
        'meta',
        'public_token',
        'signature',
        'pdf_path',
        'pdf_sha256',
        'issued_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'seller' => 'array',
        'buyer' => 'array',
        'items' => 'array',
        'meta' => 'array',
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
