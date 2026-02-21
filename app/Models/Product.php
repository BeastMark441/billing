<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'category_id',
        'price_monthly',
        'resources',
        'is_active',
        'is_hidden',
    ];

    protected $casts = [
        'resources' => 'array',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function nodes()
    {
        return $this->belongsToMany(Node::class, 'product_node');
    }

    public function trials()
    {
        return $this->hasMany(Trial::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
