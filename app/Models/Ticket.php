<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'server_id', 'subject', 'status', 'priority',
        'category', 'status_v2', 'assigned_to', 'sla_due_at', 'last_reply_at', 'tags'
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'last_reply_at' => 'datetime',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(TicketAudit::class);
    }
}
