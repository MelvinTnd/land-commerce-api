<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\Article;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
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
        ];

        // ForumTopic fields: titre, auteur, votes, commentaires, tag, image
        $discussions = ForumTopic::latest()->take(4)->get();

        // Article fields: titre, auteur, categorie, content, description, image, read_time
        $articles = Article::latest()->take(2)->get();

        // Shop fields: name, slug, location, status, user_id (-> user relation)
        $pendingShops = Shop::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'discussions', 'articles', 'pendingShops'
        ));
    }

    public function exportPdf()
    {
        return back()->with('info', 'Export PDF à venir.');
    }
}
