<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessageEdit extends Model
{
    public $timestamps = false;

    protected $fillable = ['ticket_message_id', 'user_id', 'old_message', 'new_message', 'edited_at'];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

