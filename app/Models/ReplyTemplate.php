<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplyTemplate extends Model
{
    protected $fillable = ['title', 'content', 'tags', 'active'];

    protected $casts = [
        'tags' => 'array',
        'active' => 'boolean',
    ];
}

