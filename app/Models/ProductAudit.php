<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'changes',
        'servers_updated',
    ];

    protected $casts = [
        'changes' => 'array',
    ];
}

