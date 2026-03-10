<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'infrastructure_service_id',
        'status',
        'last_error',
        'price',
        'payload',
        'pterodactyl_server_id',
        'pterodactyl_server_identifier',
        'server_ip',
        'server_port',
        'paid_at',
        'expires_at',
        'auto_renewal',
    ];

    protected $casts = [
        'payload' => 'array',
        'price' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_renewal' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(InfrastructureService::class, 'infrastructure_service_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $order->statusHistory()->create([
                    'status_from' => $order->getOriginal('status'),
                    'status_to' => $order->status,
                ]);
            }
        });

        static::created(function ($order) {
            $order->statusHistory()->create([
                'status_from' => null,
                'status_to' => $order->status,
                'comment' => 'Заказ создан',
            ]);
        });
    }
}
