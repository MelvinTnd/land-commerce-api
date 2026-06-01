<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('id', 'like', "%{$s}%")
                ->orWhereHas('user', function ($u) use ($s) {
                    $u->where('name', 'like', "%{$s}%");
                });
        }

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Order::count(),
            'pending'    => Order::whereIn('status', ['en_attente', 'pending'])->count(),
            'processing' => Order::whereIn('status', ['payee', 'en_livraison', 'processing'])->count(),
            'delivered'  => Order::whereIn('status', ['livree', 'delivered'])->count(),
            'revenue'    => Order::whereIn('status', ['livree', 'delivered'])->sum('total_amount') ?? 0,
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:en_attente,pending,payee,processing,en_livraison,shipped,livree,delivered,annulee,cancelled']);
        $order->update(['status' => $request->status]);

        return back()->with('success',
            "Commande #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " → statut mis à jour.");
    }

    /**
     * Export commandes en PDF (via page HTML print-ready)
     */
    public function exportPdf(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Max 500 lignes pour le PDF
        $orders = $query->limit(500)->get();

        $stats = [
            'total'   => Order::count(),
            'revenue' => Order::whereIn('status', ['livree', 'delivered'])->sum('total_amount') ?? 0,
        ];

        return view('admin.orders.pdf', compact('orders', 'stats'));
    }
}

