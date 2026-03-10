<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = ['order_id', 'status_from', 'status_to', 'comment'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
