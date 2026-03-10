<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfrastructureService extends Model
{
    protected $fillable = [
        'infrastructure_category_id',
        'infrastructure_subcategory_id',
        'name',
        'slug',
        'description',
        'price',
        'specifications',
        'sort_order',
        'is_active',
        'one_per_user',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'one_per_user' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(InfrastructureCategory::class, 'infrastructure_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(InfrastructureSubcategory::class, 'infrastructure_subcategory_id');
    }
}
