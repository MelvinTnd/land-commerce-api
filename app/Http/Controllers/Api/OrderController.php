<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Lister les commandes de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items' => fn($q) => $q->select(
                'id', 'order_id', 'product_id', 'shop_id',
                'product_name', 'unit_price', 'quantity'
            )])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Détail d'une commande
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($order->load('items'));
    }

    /**
     * Créer une commande (checkout)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_amount'     => 'required|numeric|min:1',
            'shipping_address' => 'required|string',
            'customer_phone'   => 'required|string',
            'payment_method'   => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.prix'     => 'required|numeric|min:0',
        ]);

        $user = $request->user();

        $order = Order::create([
            'reference'        => 'BM-' . date('Y') . '-' . strtoupper(uniqid()),
            'user_id'          => $user->id,
            'total_amount'     => $validated['total_amount'],
            'shipping_address' => $validated['shipping_address'],
            'customer_phone'   => $validated['customer_phone'],
            'customer_name'    => $user->name,
            'customer_email'   => $user->email,
            'payment_method'   => $validated['payment_method'] ?? 'mobile_money',
            'status'           => 'en_attente',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['id']);

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'shop_id'      => $product->shop_id,
                'product_name' => $product->name,
                'unit_price'   => $item['prix'],
                'quantity'     => $item['quantity'],
            ]);

            // Décrémenter le stock
            $product->decrement('stock', $item['quantity']);
        }

        return response()->json([
            'message'   => 'Commande créée avec succès',
            'order'     => $order->load('items'),
            'reference' => $order->reference,
        ], 201);
    }

    /**
     * Annuler une commande (si encore en_attente)
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($order->status !== 'en_attente') {
            return response()->json(['message' => 'Impossible d\'annuler cette commande'], 422);
        }

        // Remettre le stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'annulée']);

        return response()->json(['message' => 'Commande annulée']);
    }
}
