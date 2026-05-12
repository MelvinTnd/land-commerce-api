<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stats globales ──
        $stats = [
            'commissions'      => DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', 'payee')
                ->sum(DB::raw('unit_price * quantity * 0.05')),
            'users'            => User::where('is_active', true)->count(),
            'new_users'        => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'products'         => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'orders_total'     => Order::count(),
            'orders_pending'   => Order::whereIn('status', ['pending', 'en_attente'])->count(),
            'shops_total'      => Shop::count(),
            'shops_active'     => Shop::where('status', 'active')->count(),
        ];

        // Discussions : fonctionnalité non implémentée → collection vide
        $discussions = collect([]);

        // Articles de blog
        $articles = collect([]);

        // Boutiques récentes
        $pendingShops = Shop::with('user')->latest()->take(6)->get();

        // Commandes récentes
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'discussions', 'articles', 'pendingShops', 'recentOrders'
        ));
    }

    public function exportPdf()
    {
        return back()->with('info', 'Export PDF à venir.');
    }
}
