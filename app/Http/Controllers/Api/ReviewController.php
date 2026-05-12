<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Avis d'une boutique (public)
     */
    public function indexByShop(string $slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        $reviews = Review::with('user:id,name,avatar')
            ->where('shop_id', $shop->id)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'created_at' => $r->created_at,
                'user'       => [
                    'name'   => $r->user->name ?? 'Anonyme',
                    'avatar' => $r->user->avatar
                        ?? "https://ui-avatars.com/api/?name=" . urlencode($r->user->name ?? 'A') . "&background=1B6B3A&color=fff&size=100",
                ],
            ]);

        // Distribution des notes
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Review::where('shop_id', $shop->id)->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'pct'   => $shop->total_reviews > 0
                    ? round($count / $shop->total_reviews * 100)
                    : 0,
            ];
        }

        return response()->json([
            'reviews'      => $reviews,
            'avg_rating'   => (float) $shop->avg_rating,
            'total_reviews'=> (int)   $shop->total_reviews,
            'distribution' => $distribution,
        ]);
    }

    /**
     * Laisser un avis sur une boutique (auth requis)
     * Vérifie que l'utilisateur a bien commandé dans cette boutique
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'order_id'=> 'nullable|exists:orders,id',
        ]);

        $user = $request->user();

        // Vérifier que l'utilisateur a commandé dans cette boutique
        $hasOrdered = Order::where('user_id', $user->id)
            ->whereHas('items', fn($q) => $q->where('shop_id', $request->shop_id))
            ->where('status', 'livree')
            ->exists();

        if (!$hasOrdered) {
            return response()->json([
                'message' => 'Vous devez avoir reçu une commande de cette boutique pour laisser un avis.',
            ], 403);
        }

        // Créer ou mettre à jour l'avis (1 seul avis par boutique)
        $review = Review::updateOrCreate(
            ['user_id' => $user->id, 'shop_id' => $request->shop_id],
            [
                'rating'   => $request->rating,
                'comment'  => $request->input('comment'),
                'order_id' => $request->input('order_id'),
            ]
        );

        // Recalculer les stats de la boutique
        Review::recalculateShop($request->shop_id);

        return response()->json([
            'message' => 'Avis enregistré avec succès',
            'review'  => $review,
        ], 201);
    }

    /**
     * Vérifier si l'utilisateur peut laisser un avis (a commandé dans la boutique)
     */
    public function canReview(Request $request, int $shopId)
    {
        $user = $request->user();

        $deliveredOrders = Order::where('user_id', $user->id)
            ->whereHas('items', fn($q) => $q->where('shop_id', $shopId))
            ->where('status', 'livree')
            ->with(['items' => fn($q) => $q->where('shop_id', $shopId)->select('id', 'order_id', 'product_name')])
            ->get(['id', 'reference', 'created_at']);

        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('shop_id', $shopId)
            ->first();

        return response()->json([
            'can_review'       => $deliveredOrders->isNotEmpty() && !$alreadyReviewed,
            'already_reviewed' => (bool) $alreadyReviewed,
            'existing_review'  => $alreadyReviewed,
            'delivered_orders' => $deliveredOrders,
        ]);
    }

    /**
     * Boutiques éligibles aux avis (pour /compte — commandes livrées)
     */
    public function eligibleShops(Request $request)
    {
        $user = $request->user();

        $reviewedShopIds = Review::where('user_id', $user->id)
            ->pluck('shop_id')
            ->toArray();

        $orders = Order::where('user_id', $user->id)
            ->where('status', 'livree')
            ->with(['items.shop:id,name,slug,logo'])
            ->get();

        $shops = collect();
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $shop = $item->shop;
                if ($shop && !$shops->contains('shop_id', $shop->id)) {
                    $shops->push([
                        'shop_id'          => $shop->id,
                        'shop_name'        => $shop->name,
                        'shop_slug'        => $shop->slug,
                        'shop_logo'        => $shop->logo,
                        'order_id'         => $order->id,
                        'order_reference'  => $order->reference,
                        'already_reviewed' => in_array($shop->id, $reviewedShopIds),
                    ]);
                }
            }
        }

        return response()->json($shops->values());
    }
}
