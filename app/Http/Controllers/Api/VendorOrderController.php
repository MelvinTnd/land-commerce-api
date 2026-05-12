<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    // Statuts valides et leurs transitions autorisées
    const TRANSITIONS = [
        'en_attente'  => ['payee', 'annulee'],
        'payee'       => ['en_livraison', 'annulee'],
        'en_livraison'=> ['livree'],
        'livree'      => [],
        'annulee'     => [],
    ];

    /**
     * Liste des commandes contenant au moins un article de la boutique du vendeur
     */
    public function index(Request $request)
    {
        $shop = $request->user()->shop;
        if (!$shop) {
            return response()->json(['message' => 'Vous n\'avez pas de boutique'], 403);
        }

        $orders = Order::whereHas('items', fn($q) => $q->where('shop_id', $shop->id))
            ->with([
                'items' => fn($q) => $q->where('shop_id', $shop->id)
                    ->select('id', 'order_id', 'product_id', 'shop_id', 'product_name', 'unit_price', 'quantity'),
            ])
            ->latest()
            ->get()
            ->map(fn($order) => [
                'id'               => $order->id,
                'reference'        => $order->reference,
                'status'           => $order->status,
                'total_amount'     => (float) $order->total_amount,
                'customer_name'    => $order->customer_name,
                'customer_phone'   => $order->customer_phone,
                'customer_email'   => $order->customer_email,
                'shipping_address' => $order->shipping_address,
                'payment_method'   => $order->payment_method,
                'created_at'       => $order->created_at,
                'items'            => $order->items,
                'items_subtotal'   => $order->items->sum(fn($i) => $i->unit_price * $i->quantity),
                'next_statuses'    => self::TRANSITIONS[$order->status] ?? [],
            ]);

        // Stats rapides
        $stats = [
            'total'       => $orders->count(),
            'en_attente'  => $orders->where('status', 'en_attente')->count(),
            'en_livraison'=> $orders->where('status', 'en_livraison')->count(),
            'livree'      => $orders->where('status', 'livree')->count(),
            'annulee'     => $orders->where('status', 'annulee')->count(),
            'revenue'     => $orders->whereIn('status', ['payee', 'en_livraison', 'livree'])->sum('items_subtotal'),
        ];

        return response()->json([
            'orders' => $orders,
            'stats'  => $stats,
        ]);
    }

    /**
     * Changer le statut d'une commande (vendeur uniquement)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $shop = $request->user()->shop;
        if (!$shop) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Vérifier que la commande contient des articles de cette boutique
        $hasItems = $order->items()->where('shop_id', $shop->id)->exists();
        if (!$hasItems) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:payee,en_livraison,livree,annulee',
        ]);

        $newStatus = $request->input('status');
        $allowed   = self::TRANSITIONS[$order->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'message' => "Transition invalide : {$order->status} → {$newStatus}",
            ], 422);
        }

        $order->update(['status' => $newStatus]);

        return response()->json([
            'message'       => 'Statut mis à jour',
            'status'        => $newStatus,
            'next_statuses' => self::TRANSITIONS[$newStatus] ?? [],
        ]);
    }
}
