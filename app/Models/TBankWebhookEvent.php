<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TBankWebhookEvent extends Model
{
    protected $table = 'tbank_webhook_events';

    protected $fillable = [
        'event_hash',
        'order_id',
        'provider_payment_id',
        'status',
        'signature_valid',
        'payload',
        'processed_at',
        'process_result',
        'error_message',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
