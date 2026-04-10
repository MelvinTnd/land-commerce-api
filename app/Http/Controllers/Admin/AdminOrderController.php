<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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
            'total' => Order::count(),
            'pending' => Order::where('status', 'en_attente')->count(),
            'processing' => Order::whereIn('status', ['payee', 'en_livraison'])->count(),
            'delivered' => Order::where('status', 'livree')->count(),
            'revenue' => Order::where('status', 'livree')->sum('total_amount') ?? 0,
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
        $request->validate(['status' => 'required|in:en_attente,payee,en_livraison,livree,annulee']);
        $order->update(['status' => $request->status]);

        return back()->with('success',
            "✅ Commande #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " → statut mis à jour : « {$request->status} »");
    }
}
