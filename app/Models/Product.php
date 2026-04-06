<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'promo_price',
        'stock',
        'sku',
        'image',
        'images',
        'status',
        'is_featured',
        'avg_rating',
        'total_reviews',
    ];

    protected $casts = [
        'price'        => 'float',
        'promo_price'  => 'float',
        'is_featured'  => 'boolean',
        'avg_rating'   => 'float',
        'images'       => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Prix effectif (promo si défini)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->promo_price ?? $this->price;
    }
}
