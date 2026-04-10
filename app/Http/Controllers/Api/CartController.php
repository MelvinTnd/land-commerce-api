<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = CartItem::where('user_id', $request->user()->id)
            ->with(['product:id,name,slug,price,promo_price,image,stock,shop_id', 'product.shop:id,name,slug'])
            ->get();

        $total = $cart->sum(fn ($item) => ($item->product->promo_price ?? $item->product->price) * $item->quantity);

        return response()->json([
            'items' => $cart,
            'total' => $total,
            'count' => $cart->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->status !== 'publié') {
            return response()->json(['message' => 'Produit non disponible'], 400);
        }

        if ($product->stock < ($validated['quantity'] ?? 1)) {
            return response()->json(['message' => 'Stock insuffisant'], 422);
        }

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
            ],
            [
                'quantity' => ($validated['quantity'] ?? 1),
            ]
        );

        return response()->json([
            'message' => 'Produit ajouté au panier',
            'cart_item' => $cartItem->load('product'),
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'message' => 'Panier mis à jour',
            'cart_item' => $cartItem->fresh('product'),
        ]);
    }

    public function remove(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Produit supprimé du panier']);
    }

    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Panier vidé']);
    }
}
