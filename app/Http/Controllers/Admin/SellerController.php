<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $shops = Shop::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.sellers.index', compact('shops'));
    }

    public function show($id)
    {
        $shop = Shop::with(['user', 'products'])->findOrFail($id);
        return view('admin.sellers.show', compact('shop'));
    }

    public function approve($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'approved']);
        return back()->with('success', "La boutique « {$shop->name} » a été approuvée.");
    }

    public function reject(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'rejected']);
        return back()->with('success', "La boutique « {$shop->name} » a été rejetée.");
    }
}
