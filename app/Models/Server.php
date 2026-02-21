<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'node_id',
        'ptero_server_id',
        'identifier',
        'name',
        'ip',
        'port',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $appends = [
        'endpoint',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function getEndpointAttribute()
    {
        $host = $this->node && $this->node->public_host ? $this->node->public_host : $this->ip;
        return $host . ':' . $this->port;
    }
}
