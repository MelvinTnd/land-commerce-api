<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminPromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::latest()->paginate(12);
        $categories = Category::orderBy('name')->get();
        $stats = [
            'active'  => Promotion::where('actif', true)->where('date_fin', '>=', now())->count(),
            'total'   => Promotion::count(),
            'expired' => Promotion::where('date_fin', '<', now())->count(),
        ];
        return view('admin.promotions.index', compact('promotions', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'reduction'   => 'required|numeric|min:1|max:100',
            'date_debut'  => 'nullable|date',
            'date_fin'    => 'required|date',
            'image'       => 'nullable|url',
            'categorie'   => 'nullable|string',
            'actif'       => 'boolean',
        ]);

        $data['actif'] = $request->boolean('actif', true);
        Promotion::create($data);

        return back()->with('success', "🎉 Promotion « {$data['titre']} » créée avec succès — visible sur le frontend.");
    }

    public function update(Request $request, $id)
    {
        $promo = Promotion::findOrFail($id);
        $data  = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'reduction'   => 'required|numeric|min:1|max:100',
            'date_fin'    => 'required|date',
            'image'       => 'nullable|url',
            'categorie'   => 'nullable|string',
            'actif'       => 'boolean',
        ]);
        $data['actif'] = $request->boolean('actif');
        $promo->update($data);

        return back()->with('success', "✅ Promotion « {$promo->titre} » mise à jour.");
    }

    public function toggle($id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->update(['actif' => !$promo->actif]);
        $msg = $promo->actif ? 'activée — visible sur /promotions' : 'désactivée';
        return back()->with('success', "Promotion « {$promo->titre} » {$msg}.");
    }

    public function destroy($id)
    {
        $promo = Promotion::findOrFail($id);
        $titre = $promo->titre;
        $promo->delete();
        return back()->with('success', "Promotion « {$titre} » supprimée.");
    }
}
