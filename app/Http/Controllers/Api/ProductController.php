<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Catalogue public avec filtres
     */
    public function index(Request $request)
    {
        $query = Product::with(['shop:id,name,slug,location', 'category:id,name,slug,icon'])
            ->where('status', 'publié');

        // Filtre par catégorie (slug)
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Filtre par boutique (slug)
        if ($request->filled('shop')) {
            $query->whereHas('shop', fn ($q) => $q->where('slug', $request->shop));
        }

        // Filtre produits à la une
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filtre prix max
        if ($request->filled('prix_max')) {
            $query->where('price', '<=', $request->prix_max);
        }

        // Tri
        switch ($request->get('tri', 'recent')) {
            case 'prix_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'note':
                $query->orderBy('avg_rating', 'desc');
                break;
            default:
                $query->latest();
        }

        return response()->json($query->paginate(12));
    }

    /**
     * Détail d'un produit (par slug ou id)
     */
    public function show($slugOrId)
    {
        $product = Product::with(['shop', 'category'])
            ->where('slug', $slugOrId)
            ->orWhere('id', is_numeric($slugOrId) ? $slugOrId : 0)
            ->where('status', 'publié')
            ->firstOrFail();

        return response()->json($product);
    }

    // =====================
    // Routes vendeur
    // =====================

    /**
     * Lister les produits du vendeur connecté
     */
    public function vendorIndex(Request $request)
    {
        $shop = $request->user()->shop;
        if (! $shop) {
            return response()->json(['data' => [], 'message' => 'Boutique non trouvée'], 200);
        }

        return response()->json(
            $shop->products()->with('category')->latest()->get()
        );
    }

    /**
     * Créer un produit (vendeur)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock'       => 'integer|min:0',
            'image'       => 'nullable|string',   // URL ou base64
        ]);

        $shop = $request->user()->shop;
        if (! $shop) {
            return response()->json(['message' => "Créez d'abord une boutique"], 403);
        }

        $slug = Str::slug($validated['name']);
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i++;
        }

        $product = $shop->products()->create([
            ...$validated,
            'slug'   => $slug,
            'stock'  => $validated['stock'] ?? 0,
            'status' => 'publié',  // publié directement pour les vendeurs
        ]);

        return response()->json([
            'message' => 'Produit créé avec succès',
            'product' => $product->load('category'),
        ], 201);
    }

    /**
     * Mettre à jour un produit (vendeur)
     */
    public function update(Request $request, Product $product)
    {
        $shop = $request->user()->shop;
        if (! $shop || $product->shop_id !== $shop->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'integer|min:0',
            'status' => 'sometimes|in:brouillon,publié',
            'image' => 'nullable|string',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Produit mis à jour',
            'product' => $product->fresh()->load('category'),
        ]);
    }

    /**
     * Supprimer un produit (vendeur)
     */
    public function destroy(Request $request, Product $product)
    {
        $shop = $request->user()->shop;
        if (! $shop || $product->shop_id !== $shop->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }

    /**
     * Upload image produit
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $path = $request->file('image')->store('products', 'public');
        $url = asset('storage/'.$path);

        return response()->json(['url' => $url]);
    }
}
