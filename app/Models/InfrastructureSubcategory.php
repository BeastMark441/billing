<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfrastructureSubcategory extends Model
{
    protected $fillable = ['infrastructure_category_id', 'name', 'slug', 'description', 'sort_order', 'is_active'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = Str::slug($subcategory->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(InfrastructureCategory::class, 'infrastructure_category_id');
    }

    public function services()
    {
        return $this->hasMany(InfrastructureService::class, 'infrastructure_subcategory_id');
    }
}
