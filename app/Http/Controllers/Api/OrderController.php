<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Lister les commandes de l'utilisateur connecte.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items' => fn ($q) => $q->select(
                'id',
                'order_id',
                'product_id',
                'shop_id',
                'product_name',
                'unit_price',
                'quantity'
            )])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Detail d'une commande.
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorise'], 403);
        }

        return response()->json($order->load('items'));
    }

    /**
     * Creer une commande.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'customer_phone'   => 'required|string',
            'customer_name'    => 'nullable|string',
            'customer_email'   => 'nullable|email',
            'payment_method'   => 'nullable|string',
            'payment_phone'    => 'nullable|string',
            'notes'            => 'nullable|string',
            'address_id'       => 'nullable|exists:addresses,id',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($validated, $user) {
            $requestedItems = collect($validated['items']);
            $products = Product::whereIn('id', $requestedItems->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lines = [];

            foreach ($requestedItems as $item) {
                $product = $products->get($item['id']);
                $quantity = (int) $item['quantity'];

                if (! $product || ! in_array($product->status, ['publié', 'publie', 'active', 'mis_en_avant'], true)) {
                    throw ValidationException::withMessages([
                        'items' => ["Le produit {$item['id']} n'est pas disponible."],
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuffisant pour {$product->name}."],
                    ]);
                }

                $unitPrice = (float) ($product->promo_price ?? $product->price);
                $subtotal += $unitPrice * $quantity;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            $shippingFee = $subtotal > 50000 ? 0 : 2500;

            $order = Order::create([
                'reference'        => 'BM-' . date('Y') . '-' . strtoupper(uniqid()),
                'user_id'          => $user->id,
                'total_amount'     => $subtotal + $shippingFee,
                'shipping_address' => $validated['shipping_address'],
                'customer_phone'   => $validated['customer_phone'],
                'customer_name'    => $validated['customer_name'] ?? $user->name,
                'customer_email'   => $validated['customer_email'] ?? $user->email,
                'payment_method'   => $validated['payment_method'] ?? 'mobile_money',
                'payment_ref'      => $validated['payment_phone'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'status'           => 'payee',
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'shop_id'      => $product->shop_id,
                    'product_name' => $product->name,
                    'unit_price'   => $line['unit_price'],
                    'quantity'     => $line['quantity'],
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            return $order;
        });

        return response()->json([
            'message'   => 'Commande creee avec succes',
            'order'     => $order->load('items'),
            'reference' => $order->reference,
        ], 201);
    }

    /**
     * Annuler une commande si elle est encore en attente.
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorise'], 403);
        }

        if ($order->status !== 'en_attente') {
            return response()->json(['message' => 'Impossible d\'annuler cette commande'], 422);
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'annulee']);

        return response()->json(['message' => 'Commande annulee']);
    }
}
