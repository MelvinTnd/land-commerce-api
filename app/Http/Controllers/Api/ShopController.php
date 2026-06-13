<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    /**
     * Liste des boutiques actives
     */
    public function index(Request $request)
    {
        $query = Shop::where('status', 'active')
            ->withCount('products');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            });
        }

        $query->latest();

        if ($request->filled('limit')) {
            $query->limit((int) $request->limit);
        }

        return response()->json($query->get());
    }

    /**
     * Détail d'une boutique (avec produits, utilisateur et stats)
     */
    public function show($slug)
    {
        $shop = Shop::with([
            'user:id,name,email',
            'products' => fn ($q) => $q->whereIn('status', ['publié', 'publie', 'active', 'mis_en_avant'])->latest(),
        ])
            ->where('slug', 'ILIKE', $slug)
            ->firstOrFail();

        return response()->json($shop);
    }

    /**
     * Création d'une nouvelle boutique
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'whatsapp'    => 'nullable|string|max:30',
            'instagram'   => 'nullable|string|max:100',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->user()->shop) {
            return response()->json(['message' => 'Vous avez déjà une boutique'], 409);
        }

        // Générer un slug unique
        $slug = Str::slug($request->name);
        $original = $slug;
        $counter  = 1;
        while (Shop::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }

        // Upload logo — les erreurs d'upload ne bloquent pas la création
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            try {
                if (config('cloudinary.cloud_name')) {
                    $logoUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                        $request->file('logo')->getRealPath(),
                        ['folder' => "caurimarket/shops/{$slug}/logo"]
                    )->getSecurePath();
                } else {
                    $path    = $request->file('logo')->store("shops/{$slug}", 'public');
                    $logoUrl = Storage::url($path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Logo upload failed for shop {$slug}: " . $e->getMessage());
                // On continue sans logo — la boutique sera créée quand même
            }
        }

        // Upload bannière — même logique de tolérance
        $bannerUrl = null;
        if ($request->hasFile('banner')) {
            try {
                if (config('cloudinary.cloud_name')) {
                    $bannerUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                        $request->file('banner')->getRealPath(),
                        ['folder' => "caurimarket/shops/{$slug}/banner"]
                    )->getSecurePath();
                } else {
                    $path      = $request->file('banner')->store("shops/{$slug}/banner", 'public');
                    $bannerUrl = Storage::url($path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Banner upload failed for shop {$slug}: " . $e->getMessage());
                // On continue sans bannière
            }
        }

        // Passer l'utilisateur en vendeur
        $request->user()->update(['role' => 'vendeur']);

        $shop = Shop::create([
            'user_id'     => $request->user()->id,
            'name'        => $request->name,
            'slug'        => $slug,
            'location'    => $request->location,
            'description' => $request->description,
            'category'    => $request->category,
            'whatsapp'    => $request->whatsapp,
            'instagram'   => $request->instagram,
            'logo'        => $logoUrl,
            'banner'      => $bannerUrl,
            'status'      => 'active',
        ]);

        return response()->json([
            'message' => 'Boutique créée avec succès',
            'shop'    => $shop,
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

        $rules = [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'sometimes|string|max:255',
            'category'    => 'nullable|string|max:100',
            'whatsapp'    => 'nullable|string|max:30',
            'instagram'   => 'nullable|string|max:100',
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
        } else {
            $rules['logo'] = 'nullable|string';
        }

        if ($request->hasFile('banner')) {
            $rules['banner'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096';
        } else {
            $rules['banner'] = 'nullable|string';
        }

        $request->validate($rules);

        $data = $request->only(['name', 'description', 'location', 'category', 'whatsapp', 'instagram']);

        // Upload logo si fichier envoyé
        if ($request->hasFile('logo')) {
            if (config('cloudinary.cloud_name')) {
                $data['logo'] = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                    $request->file('logo')->getRealPath(),
                    ['folder' => "blackmaket/shops/{$shop->slug}/logo"]
                )->getSecurePath();
            } else {
                if ($shop->logo && Str::startsWith($shop->logo, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $shop->logo));
                }
                $path         = $request->file('logo')->store("shops/{$shop->slug}", 'public');
                $data['logo'] = Storage::url($path);
            }
        } elseif ($request->filled('logo')) {
            $data['logo'] = $request->logo;
        }

        // Upload bannière si fichier envoyé
        if ($request->hasFile('banner')) {
            if (config('cloudinary.cloud_name')) {
                $data['banner'] = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
                    $request->file('banner')->getRealPath(),
                    ['folder' => "blackmaket/shops/{$shop->slug}/banner"]
                )->getSecurePath();
            } else {
                if ($shop->banner && Str::startsWith($shop->banner, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $shop->banner));
                }
                $path           = $request->file('banner')->store("shops/{$shop->slug}/banner", 'public');
                $data['banner'] = Storage::url($path);
            }
        } elseif ($request->filled('banner')) {
            $data['banner'] = $request->banner;
        }

        $shop->update($data);

        return response()->json([
            'message' => 'Boutique mise à jour',
            'shop'    => $shop->fresh(),
        ]);
    }
}
