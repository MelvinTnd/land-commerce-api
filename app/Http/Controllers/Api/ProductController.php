<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private const PUBLIC_STATUSES = ['publié', 'publie', 'active', 'mis_en_avant'];

    private function cloudinaryConfigured(): bool
    {
        return (bool) config('cloudinary.cloud_url');
    }

    private function storeProductImage(UploadedFile $image, string $folder = 'blackmaket/products'): string
    {
        if ($this->cloudinaryConfigured()) {
            try {
                return \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                    $image->getRealPath(),
                    [
                        'folder' => $folder,
                        'resource_type' => 'image',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ]
                )->getSecurePath();
            } catch (\Throwable $e) {
                Log::warning('Cloudinary upload failed, falling back to local storage.', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $path = $image->store('products', 'public');
        return '/storage/' . ltrim($path, '/');
    }

    public function index(Request $request)
    {
        $query = Product::with(['shop:id,name,slug,location', 'category:id,name,slug,icon'])
            ->whereIn('status', self::PUBLIC_STATUSES);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('shop')) {
            $query->whereHas('shop', fn ($q) => $q->where('slug', $request->shop));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('shop', fn ($sq) => $sq->where('location', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('prix_max')) {
            $query->where('price', '<=', $request->prix_max);
        }

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

    public function show($slugOrId)
    {
        $product = Product::with(['shop', 'category'])
            ->where(function ($query) use ($slugOrId) {
                $query->where('slug', $slugOrId);
                if (is_numeric($slugOrId)) {
                    $query->orWhere('id', $slugOrId);
                }
            })
            ->whereIn('status', self::PUBLIC_STATUSES)
            ->firstOrFail();

        return response()->json($product);
    }

    public function vendorIndex(Request $request)
    {
        $shop = $request->user()->shop;
        if (! $shop) {
            return response()->json(['data' => [], 'message' => 'Boutique non trouvee'], 200);
        }

        return response()->json(
            $shop->products()->with('category')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'integer|min:0',
            'image' => 'nullable',
        ]);

        $shop = $request->user()->shop;
        if (! $shop) {
            return response()->json(['message' => "Creez d'abord une boutique"], 403);
        }

        $slug = Str::slug($validated['name']);
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $data = $validated;
        if ($request->hasFile('image')) {
            $data['image'] = $this->storeProductImage($request->file('image'));
        }

        $product = $shop->products()->create([
            ...$data,
            'slug' => $slug,
            'stock' => $validated['stock'] ?? 0,
            'status' => 'publié',
        ]);

        return response()->json([
            'message' => 'Produit cree avec succes',
            'product' => $product->load('category'),
        ], 201);
    }

    public function update(Request $request, Product $product)
    {
        $shop = $request->user()->shop;
        if (! $shop || $product->shop_id !== $shop->id) {
            return response()->json(['message' => 'Non autorise'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'integer|min:0',
            'status' => 'sometimes|in:brouillon,publié',
            'image' => 'nullable',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            if ($product->image && Str::contains($product->image, 'storage/products')) {
                $oldPath = str_replace(Storage::url(''), '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            $data['image'] = $this->storeProductImage($request->file('image'));
        }

        $product->update($data);

        return response()->json([
            'message' => 'Produit mis a jour',
            'product' => $product->fresh()->load('category'),
        ]);
    }

    public function destroy(Request $request, Product $product)
    {
        $shop = $request->user()->shop;
        if (! $shop || $product->shop_id !== $shop->id) {
            return response()->json(['message' => 'Non autorise'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Produit supprime']);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            return response()->json([
                'url' => $this->storeProductImage($request->file('image')),
            ]);
        } catch (\Exception $e) {
            Log::error('Upload image error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur upload: ' . $e->getMessage(),
                'cloudinary_configured' => $this->cloudinaryConfigured(),
            ], 500);
        }
    }
}
