<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8',
            'shop_name'   => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'vendeur',
            'phone'     => $validated['phone'],
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'user_id'     => $user->id,
            'name'        => $validated['shop_name'],
            'slug'        => Str::slug($validated['shop_name']),
            'location'    => $validated['location'],
            'description' => $validated['description'],
            'status'      => 'active',
        ]);

        return back()->with('success', "✅ Le vendeur « {$user->name} » et sa boutique « {$shop->name} » ont été créés avec succès.");
    }

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
