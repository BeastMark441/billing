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
        'sync_attempts',
        'last_sync_at',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
        'credited_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'sync_attempts' => 'integer',
    ];

    public function getStatusLabelAttribute(): string
    {
        $status = strtoupper((string) $this->status);
        $statusLabels = [
            'PENDING' => 'В обработке',
            'NEW' => 'Новый',
            'AUTHORIZED' => 'Ожидает списания',
            'CONFIRMED' => 'Подтвержден',
            'REJECTED' => 'Отклонен',
            'CANCELED' => 'Отменен',
            'ERROR' => 'Ошибка',
            'REFUNDED' => 'Возвращен',
        ];

        return $statusLabels[$status] ?? $status;
    }

    public function getStatusColorAttribute(): string
    {
        $status = strtoupper((string) $this->status);
        $statusColors = [
            'PENDING' => 'bg-yellow-500/10 text-yellow-300',
            'NEW' => 'bg-blue-500/10 text-blue-300',
            'REJECTED' => 'bg-red-500/10 text-red-400',
            'CANCELED' => 'bg-gray-500/10 text-gray-400',
            'ERROR' => 'bg-red-500/10 text-red-400',
            'CONFIRMED' => 'bg-green-500/10 text-green-400',
        ];

        return $statusColors[$status] ?? 'bg-gray-500/10 text-gray-300';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
