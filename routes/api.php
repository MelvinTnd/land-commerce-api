<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\VendorOrderController;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ============================================================
// ROUTES PUBLIQUES
// ============================================================

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Catégories
Route::get('/categories', function () {
    return response()->json(
        Category::orderBy('sort_order')->get()
    );
});

// Produits publics
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Boutiques publiques
Route::get('/shops', [ShopController::class, 'index']);
Route::get('/shops/{slug}', [ShopController::class, 'show']);

// Avis publics d'une boutique
Route::get('/shops/{slug}/reviews', [ReviewController::class, 'indexByShop']);

// Promotions publiques (produits locaux mis en avant)
Route::get('/promotions', function () {
    return response()->json(
        Promotion::where('actif', true)
            ->where('date_fin', '>=', now())
            ->orderBy('date_fin', 'asc')
            ->get()
    );
});

// ============================================================
// ROUTES SÉCURISÉES (token Sanctum requis)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::get('/user', fn (Request $req) => $req->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Boutique vendeur ---
    Route::post('/shops', [ShopController::class, 'store']);
    Route::put('/shops/{shop}', [ShopController::class, 'update']);

    // --- Produits vendeur ---
    Route::get('/vendor/products', [ProductController::class, 'vendorIndex']);
    Route::post('/vendor/products', [ProductController::class, 'store']);
    Route::put('/vendor/products/{product}', [ProductController::class, 'update']);
    Route::delete('/vendor/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/vendor/upload-image', [ProductController::class, 'uploadImage']);

    // --- Dashboard vendeur ---
    Route::get('/vendor/dashboard', function (Request $request) {
        $shop = $request->user()->shop;
        if (! $shop) {
            return response()->json([
                'shop' => null,
                'stats' => ['products' => 0, 'orders' => 0, 'revenue' => 0],
            ]);
        }

        return response()->json([
            'shop' => $shop,
            'stats' => [
                'products' => $shop->products()->count(),
                'orders'   => OrderItem::where('shop_id', $shop->id)->count(),
                'revenue'  => OrderItem::where('shop_id', $shop->id)
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', 'payee')
                    ->sum(DB::raw('unit_price * quantity')),
            ],
        ]);
    });

    // --- Commandes client ---
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // --- Commandes vendeur ---
    Route::get('/vendor/orders', [VendorOrderController::class, 'index']);
    Route::patch('/vendor/orders/{order}/status', [VendorOrderController::class, 'updateStatus']);

    // --- Messagerie vendeur ↔ client ---
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/unread', [ConversationController::class, 'unreadCount']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/reply', [ConversationController::class, 'reply']);
    Route::post('/shops/{shopId}/message', [ConversationController::class, 'sendMessage']);

    // --- Avis clients ---
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/eligible-shops', [ReviewController::class, 'eligibleShops']);
    Route::get('/shops/{shopId}/can-review', [ReviewController::class, 'canReview']);
});
