<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // GET /api/articles - Liste paginée
    public function index(Request $request)
    {
        $query = Article::orderBy('created_at', 'desc');

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('featured')) {
            $query->where('featured', true);
        }

        $articles = $query->paginate(9);

        return response()->json($articles);
    }

    // GET /api/articles/featured - Articles mis en avant
    public function featured()
    {
        $articles = Article::where('featured', true)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return response()->json($articles);
    }

    // GET /api/articles/{id} - Détail d'un article
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article);
    }

    // GET /api/articles/categories - Toutes les catégories
    public function categories()
    {
        $categories = Article::select('categorie')
            ->whereNotNull('categorie')
            ->distinct()
            ->pluck('categorie');

        return response()->json($categories);
    }
}
