<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCommunityController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminPromotionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SellerController;
use Illuminate\Support\Facades\Route;

// ── Page d'accueil → Login Admin ──
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// ══════════════════════════════════════════════
// AUTH ADMIN — Connexion / Déconnexion
// ══════════════════════════════════════════════
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ══════════════════════════════════════════════
// ADMIN DASHBOARD — Heritage Modernist Marketplace
// Protégé par authentification (rôle admin)
// ══════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // ── Dashboard ──────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/export-pdf', [DashboardController::class, 'exportPdf'])->name('export.pdf');

    // ── Vendeurs ────────────────────────────────
    Route::get('/sellers', [SellerController::class, 'index'])->name('sellers');
    Route::get('/sellers/{id}', [SellerController::class, 'show'])->name('sellers.show');
    Route::post('/sellers/{id}/approve', [SellerController::class, 'approve'])->name('sellers.approve');
    Route::post('/sellers/{id}/reject', [SellerController::class, 'reject'])->name('sellers.reject');

    // ── Produits ─────────────────────────────────
    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::post('/products/{id}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{id}/reject', [AdminProductController::class, 'reject'])->name('products.reject');
    Route::post('/products/{id}/flag', [AdminProductController::class, 'flag'])->name('products.flag');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // ── Commandes ────────────────────────────────
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // ── Promotions ────────────────────────────────
    Route::get('/promotions', [AdminPromotionController::class, 'index'])->name('promotions');
    Route::post('/promotions', [AdminPromotionController::class, 'store'])->name('promotions.store');
    Route::put('/promotions/{id}', [AdminPromotionController::class, 'update'])->name('promotions.update');
    Route::patch('/promotions/{id}/toggle', [AdminPromotionController::class, 'toggle'])->name('promotions.toggle');
    Route::delete('/promotions/{id}', [AdminPromotionController::class, 'destroy'])->name('promotions.destroy');

    // ── Blog / Actualités ────────────────────────
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{id}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{id}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{id}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
    Route::patch('/blog/{id}/toggle', [AdminBlogController::class, 'toggleStatus'])->name('blog.toggle');

    // ── Communauté ───────────────────────────────
    Route::get('/community', [AdminCommunityController::class, 'index'])->name('community');
    Route::delete('/community/{id}', [AdminCommunityController::class, 'destroy'])->name('community.delete');
    Route::patch('/community/{id}/ignore', [AdminCommunityController::class, 'ignore'])->name('community.ignore');
    Route::patch('/community/{id}/pin', [AdminCommunityController::class, 'pin'])->name('community.pin');

    // ── Utilisateurs ─────────────────────────────
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');

    // ── Paramètres ────────────────────────────────
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
});
