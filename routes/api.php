<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ForumTopicController;
use App\Http\Controllers\Api\OrderController;

// ============================================================
// ROUTES PUBLIQUES
// ============================================================

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Catégories
Route::get('/categories', function () {
    return response()->json(
        \App\Models\Category::orderBy('sort_order')->get()
    );
});

// Produits publics
Route::get('/products',      [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Boutiques publiques
Route::get('/shops',         [ShopController::class, 'index']);
Route::get('/shops/{slug}',  [ShopController::class, 'show']);

// Blog - Articles
Route::get('/articles',         [ArticleController::class, 'index']);
Route::get('/articles/{id}',    [ArticleController::class, 'show']);

// Forum - Topics
Route::get('/forum-topics',        [ForumTopicController::class, 'index']);
Route::get('/forum-topics/{topic}', [ForumTopicController::class, 'show']);

// ============================================================
// ROUTES SÉCURISÉES (token Sanctum requis)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::get('/user',     fn(Request $req) => $req->user());
    Route::post('/logout',  [AuthController::class, 'logout']);

    // --- Boutique vendeur ---
    Route::post('/shops',         [ShopController::class, 'store']);
    Route::put('/shops/{shop}',   [ShopController::class, 'update']);

    // --- Produits vendeur ---
    Route::get('/vendor/products',              [ProductController::class, 'vendorIndex']);
    Route::post('/vendor/products',             [ProductController::class, 'store']);
    Route::put('/vendor/products/{product}',    [ProductController::class, 'update']);
    Route::delete('/vendor/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/vendor/upload-image',         [ProductController::class, 'uploadImage']);

    // --- Dashboard vendeur ---
    Route::get('/vendor/dashboard', function (Request $request) {
        $shop = $request->user()->shop;
        if (!$shop) {
            return response()->json([
                'shop'  => null,
                'stats' => ['products' => 0, 'orders' => 0, 'revenue' => 0],
            ]);
        }

        return response()->json([
            'shop'  => $shop,
            'stats' => [
                'products' => $shop->products()->count(),
                'orders'   => \App\Models\OrderItem::where('shop_id', $shop->id)->count(),
                'revenue'  => \App\Models\OrderItem::where('shop_id', $shop->id)
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', 'payee')
                    ->sum(\Illuminate\Support\Facades\DB::raw('unit_price * quantity')),
            ],
        ]);
    });

    // --- Commandes ---
    Route::get('/orders',               [OrderController::class, 'index']);
    Route::post('/checkout',            [OrderController::class, 'store']);
    Route::get('/orders/{order}',       [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // --- Forum (créer un sujet + voter) ---
    Route::post('/forum-topics',                       [ForumTopicController::class, 'store']);
    Route::post('/forum-topics/{forumTopic}/vote',     [ForumTopicController::class, 'vote']);

    // --- Articles (créer/modifier - admin) ---
    Route::post('/articles',            [ArticleController::class, 'store']);
    Route::put('/articles/{article}',   [ArticleController::class, 'update']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);
});
