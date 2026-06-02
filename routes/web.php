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
// SETUP TEMPORAIRE — Créer le compte admin prod
// Appeler UNE SEULE FOIS puis supprimer cette route
// URL : /setup-admin?token=BeninMarket2026!
// ══════════════════════════════════════════════
Route::get('/setup-admin', function () {
    $secret = 'BeninMarket2026!';

    if (request('token') !== $secret) {
        abort(403, 'Token invalide.');
    }

    $email = 'admin@beninmarket.com';

    $existing = \App\Models\User::where('email', $email)->first();
    if ($existing) {
        return response()->json([
            'status'  => 'already_exists',
            'message' => 'Le compte admin existe déjà.',
            'email'   => $existing->email,
            'role'    => $existing->role,
        ]);
    }

    $admin = \App\Models\User::create([
        'name'     => 'Admin BéninMarket',
        'email'    => $email,
        'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        'role'     => 'admin',
        'phone'    => '+22901000000',
    ]);

    return response()->json([
        'status'   => 'created',
        'message'  => 'Compte admin créé avec succès !',
        'email'    => $admin->email,
        'password' => 'admin123',
        'login'    => url('/admin/login'),
    ]);
});

// ══════════════════════════════════════════════
// DEPLOYMENT — Migration & Seeding en production
// URL : /deploy/seed-production-real-data-secret-99bf?token=Blackmaket2026!
// ══════════════════════════════════════════════
Route::get('/deploy/seed-production-real-data-secret-99bf', function () {
    $secret = 'Blackmaket2026!';
    if (request('token') !== $secret) abort(403);

    try {
        // 1. Migrations (force car production)
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        
        // 2. Seeding des données réelles
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Database\Seeders\AdminSeeder',
            '--force' => true
        ]);
        
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Database\Seeders\RealDataSeeder',
            '--force' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Base de données réinitialisée et boutiques réelles créées sur Render !',
            'artisan_output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
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
    Route::get('/orders/export-pdf', [AdminOrderController::class, 'exportPdf'])->name('orders.pdf');
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
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');

    // ── Vendeurs (Boutiques) ──────────────────────
    Route::post('/sellers', [SellerController::class, 'store'])->name('sellers.store');

    // ── Paramètres ────────────────────────────────
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
});
