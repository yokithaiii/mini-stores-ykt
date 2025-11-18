<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'image',
        'images',
        'description',
        'gender',
        'discount_type',
        'discount_value',
        'attributes',
        'store_id',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'attributes' => 'array',
        'images' => 'array',
        'discount_value' => 'decimal:2',
    ];

    protected $appends = [
        'discounted_price',
        'total_quantity',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Вычисляемая цена со скидкой
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_type === 'none' || !$this->discount_value) {
            return $this->price;
        }

        if ($this->discount_type === 'percent') {
            return $this->price - ($this->price * $this->discount_value / 100);
        }

        if ($this->discount_type === 'fixed') {
            return max(0, $this->price - $this->discount_value);
        }

        return $this->price;
    }

    // Общее количество всех вариантов
    public function getTotalQuantityAttribute()
    {
        if ($this->variants()->count() > 0) {
            return $this->variants()->sum('quantity');
        }
        return $this->quantity;
    }
}
