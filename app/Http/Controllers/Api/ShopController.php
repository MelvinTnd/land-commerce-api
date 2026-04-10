<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Liste des boutiques actives
     */
    public function index()
    {
        return response()->json(
            Shop::where('status', 'active')
                ->withCount('products')
                ->latest()
                ->get()
        );
    }

    /**
     * Détail d'une boutique
     */
    public function show($slug)
    {
        $shop = Shop::with([
            'products' => fn ($q) => $q->where('status', 'publié')->latest(),
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($shop);
    }

    /**
     * Création d'une nouvelle boutique (vendor setup)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($request->user()->shop) {
            return response()->json(['message' => 'Vous avez déjà une boutique'], 409);
        }

        $slug = Str::slug($request->name);
        $original = $slug;
        $counter = 1;
        while (Shop::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter++;
        }

        $request->user()->update(['role' => 'vendeur']);

        $shop = Shop::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => $slug,
            'location' => $request->location,
            'description' => $request->description,
            'status' => 'pending', // en attente validation admin
        ]);

        return response()->json([
            'message' => 'Boutique créée avec succès',
            'shop' => $shop,
        ], 201);
    }

    /**
     * Mise à jour de la boutique
     */
    public function update(Request $request, Shop $shop)
    {
        if ($shop->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|string|max:255',
            'logo' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        $shop->update($request->only(['name', 'description', 'location', 'logo', 'banner']));

        return response()->json([
            'message' => 'Boutique mise à jour',
            'shop' => $shop->fresh(),
        ]);
    }
}
