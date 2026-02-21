<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ptero_id',
        'ip',
        'public_host',
        'port_range_start',
        'port_range_end',
        'is_active',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }
}
