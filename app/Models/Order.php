<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'infrastructure_service_id',
        'status',
        'cart_added_at',
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
        'cart_added_at' => 'datetime',
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

    public function getStatusLabelAttribute(): string
    {
        $statusLabels = [
            'cart' => 'В корзине',
            'paid' => 'Оплачен',
            'active' => 'Активен',
            'pending' => 'Ожидает оплаты',
            'suspended' => 'Приостановлен',
            'provisioning' => 'Установка',
            'expired' => 'Истек',
            'cancelled' => 'Отменен',
            'failed' => 'Ошибка',
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $statusColors = [
            'cart' => 'text-yellow-400',
            'paid' => 'text-blue-400',
            'active' => 'text-green-400',
            'pending' => 'text-yellow-500',
            'suspended' => 'text-red-500',
            'provisioning' => 'text-purple-400',
            'expired' => 'text-red-400',
            'cancelled' => 'text-gray-400',
            'failed' => 'text-red-500',
        ];

        return $statusColors[$this->status] ?? 'text-gray-300';
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                // Не пишем в историю статус 'cart', он технический
                if ($order->status === 'cart') {
                    return;
                }

                $order->statusHistory()->create([
                    'status_from' => $order->getOriginal('status'),
                    'status_to' => $order->status,
                ]);
            }
        });

        static::created(function ($order) {
            // Не создаем историю для товаров в корзине
            if ($order->status === 'cart') {
                return;
            }

            $order->statusHistory()->create([
                'status_from' => null,
                'status_to' => $order->status,
                'comment' => 'Заказ создан',
            ]);
        });
    }
}
