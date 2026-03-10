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
    ];

    protected $casts = [
        'payload' => 'array',
        'price' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(InfrastructureService::class, 'infrastructure_service_id');
    }
}
