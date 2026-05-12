<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Recalcule avg_rating et total_reviews sur la boutique associée
     */
    public static function recalculateShop(int $shopId): void
    {
        $agg = self::where('shop_id', $shopId)
            ->selectRaw('AVG(rating) as avg, COUNT(*) as total')
            ->first();

        Shop::where('id', $shopId)->update([
            'avg_rating'    => round($agg->avg ?? 0, 2),
            'total_reviews' => $agg->total ?? 0,
        ]);
    }
}
