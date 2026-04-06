<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Liste tous les articles (ordre : featured d'abord, puis récents)
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // ── Liaison Frontend ──────────────────────────────
        // Seuls les articles avec statut='publié' sont visibles
        // sur le frontend Next.js. Publier depuis le dashboard
        // admin rend l'article visible ici instantanément.
        $query->where('statut', 'publié');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        // Articles featured en premier, puis les plus récents
        $articles = $query->orderByDesc('featured')->latest()->paginate(10);

        return response()->json($articles);
    }

    /**
     * Détail d'un article (par slug ou id)
     */
    public function show($slugOrId)
    {
        $article = Article::where('slug', $slugOrId)
            ->orWhere('id', is_numeric($slugOrId) ? $slugOrId : 0)
            ->firstOrFail();

        return response()->json($article);
    }

    /**
     * Créer un article (admin/vendeur)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'      => 'required|string|max:255',
            'categorie'  => 'nullable|string|max:100',
            'description'=> 'nullable|string',
            'content'    => 'nullable|string',
            'image'      => 'nullable|string',
            'featured'   => 'boolean',
            'read_time'  => 'integer|min:1',
            'tags'       => 'nullable|string',
        ]);

        $validated['auteur'] = $request->user()->name;

        $article = Article::create($validated);

        return response()->json([
            'message' => 'Article créé avec succès',
            'article' => $article,
        ], 201);
    }

    /**
     * Modifier un article
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'titre'      => 'sometimes|string|max:255',
            'categorie'  => 'nullable|string|max:100',
            'description'=> 'nullable|string',
            'content'    => 'nullable|string',
            'image'      => 'nullable|string',
            'featured'   => 'boolean',
            'read_time'  => 'integer|min:1',
            'tags'       => 'nullable|string',
        ]);

        $article->update($validated);

        return response()->json([
            'message' => 'Article mis à jour',
            'article' => $article->fresh(),
        ]);
    }

    /**
     * Supprimer un article
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json(['message' => 'Article supprimé']);
    }
}
