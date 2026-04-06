<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    /* ─────────────────────────────────────────
     | INDEX — liste de tous les articles
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $query = Article::latest();

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        $articles = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Article::count(),
            'publies'     => Article::where('statut', 'publié')->count(),
            'brouillons'  => Article::where('statut', 'brouillon')->count(),
            'en_attente'  => Article::where('statut', 'en_attente')->count(),
        ];

        $categories = Article::distinct()->pluck('categorie')->filter()->sort()->values();

        return view('admin.blog.index', compact('articles', 'stats', 'categories'));
    }

    /* ─────────────────────────────────────────
     | CREATE — formulaire
     ───────────────────────────────────────── */
    public function create()
    {
        $categories = Article::distinct()->pluck('categorie')->filter()->sort()->values();
        return view('admin.blog.form', compact('categories'));
    }

    /* ─────────────────────────────────────────
     | STORE — sauvegarder le nouvel article
     | → Liaison frontend : statut 'publié' =
     |   visible sur /api/articles
     ───────────────────────────────────────── */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'categorie'   => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content'     => 'required|string',
            'image'       => 'nullable|url|max:500',
            'auteur'      => 'nullable|string|max:100',
            'statut'      => 'required|in:publié,brouillon,en_attente',
            'featured'    => 'boolean',
            'read_time'   => 'nullable|integer|min:1',
            'tags'        => 'nullable|string',
        ]);

        $validated['auteur'] = $validated['auteur'] ?? 'Heritage Admin';
        $validated['featured'] = $request->boolean('featured');

        // Générer le slug unique
        $slug = Str::slug($validated['titre']);
        $original = $slug;
        $i = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        $validated['slug'] = $slug;

        Article::create($validated);

        return redirect()->route('admin.blog')
            ->with('success', "Article « {$validated['titre']} » créé et " .
                ($validated['statut'] === 'publié' ? '🟢 publié sur le frontend !' : 'sauvegardé en ' . $validated['statut']));
    }

    /* ─────────────────────────────────────────
     | EDIT — formulaire de modification
     ───────────────────────────────────────── */
    public function edit($id)
    {
        $article    = Article::findOrFail($id);
        $categories = Article::distinct()->pluck('categorie')->filter()->sort()->values();
        return view('admin.blog.form', compact('article', 'categories'));
    }

    /* ─────────────────────────────────────────
     | UPDATE — sauvegarder les modifications
     ───────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'categorie'   => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content'     => 'required|string',
            'image'       => 'nullable|max:500',
            'auteur'      => 'nullable|string|max:100',
            'statut'      => 'required|in:publié,brouillon,en_attente',
            'featured'    => 'boolean',
            'read_time'   => 'nullable|integer|min:1',
            'tags'        => 'nullable|string',
        ]);

        $validated['featured'] = $request->boolean('featured');

        $article->update($validated);

        return redirect()->route('admin.blog')
            ->with('success', "Article « {$article->titre} » mis à jour — statut : {$validated['statut']}");
    }

    /* ─────────────────────────────────────────
     | DESTROY — supprimer
     ───────────────────────────────────────── */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $titre   = $article->titre;
        $article->delete();

        return redirect()->route('admin.blog')
            ->with('success', "Article « {$titre} » supprimé.");
    }

    /* ─────────────────────────────────────────
     | TOGGLE STATUS — publier/dépublier rapide
     ───────────────────────────────────────── */
    public function toggleStatus($id)
    {
        $article = Article::findOrFail($id);
        $article->statut = $article->statut === 'publié' ? 'brouillon' : 'publié';
        $article->save();

        return back()->with('success',
            "« {$article->titre} » est maintenant " .
            ($article->statut === 'publié' ? '🟢 publié' : '⚫ en brouillon'));
    }
}
