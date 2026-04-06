<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    /* ─────────────────────────────────────────
     | INDEX — catalogue complet
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $tab   = $request->get('tab', 'all');
        $query = Product::with(['shop', 'category'])->latest();

        if ($tab === 'pending')  $query->where('status', 'pending');
        if ($tab === 'flagged')  $query->where('status', 'flagged');

        $products = $query->paginate(15)->withQueryString();

        $miniStats = [
            'total'        => Product::count(),
            'under_review' => Product::where('status', 'pending')->count(),
            'flagged'      => Product::where('status', 'flagged')->count(),
            'top_category' => \App\Models\Category::withCount('products')
                ->orderByDesc('products_count')
                ->value('name') ?? 'Textiles',
        ];

        return view('admin.products.index', compact('products', 'miniStats'));
    }

    /* ─────────────────────────────────────────
     | APPROVE — publie le produit
     | → Frontend : visible dans /api/products
     |   car status devient 'publié'
     ───────────────────────────────────────── */
    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'publié']);

        return back()->with('success',
            "✅ Produit « {$product->name} » approuvé — visible sur le frontend.");
    }

    /* ─────────────────────────────────────────
     | REJECT — masque du frontend
     ───────────────────────────────────────── */
    public function reject($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected']);

        return back()->with('success',
            "❌ Produit « {$product->name} » rejeté — masqué du frontend.");
    }

    /* ─────────────────────────────────────────
     | FLAG — signaler
     ───────────────────────────────────────── */
    public function flag($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'flagged']);

        return back()->with('success',
            "🚩 Produit « {$product->name} » signalé pour révision.");
    }

    /* ─────────────────────────────────────────
     | DESTROY — supprimer définitivement
     ───────────────────────────────────────── */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $name    = $product->name;
        $product->delete();

        return back()->with('success', "Produit « {$name} » supprimé définitivement.");
    }
}
