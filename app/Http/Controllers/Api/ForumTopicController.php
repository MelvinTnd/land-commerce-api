<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class ForumTopicController extends Controller
{
    /**
     * Liste des sujets triés par votes décroissants
     */
    public function index(Request $request)
    {
        $query = ForumTopic::query();

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        return response()->json(
            $query->orderByDesc('votes')->latest()->paginate(15)
        );
    }

    /**
     * Détail d'un sujet
     */
    public function show(ForumTopic $forumTopic)
    {
        return response()->json($forumTopic);
    }

    /**
     * Créer un nouveau sujet (authentifié)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'tag'         => 'nullable|string|max:50',
            'image'       => 'nullable|string',
        ]);

        $topic = ForumTopic::create([
            ...$validated,
            'auteur' => $request->user()->name,
            'votes'  => 0,
            'commentaires' => 0,
        ]);

        return response()->json([
            'message' => 'Sujet créé avec succès',
            'topic'   => $topic,
        ], 201);
    }

    /**
     * Voter pour un sujet (+1 / -1)
     */
    public function vote(Request $request, ForumTopic $forumTopic)
    {
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        if ($request->direction === 'up') {
            $forumTopic->increment('votes');
        } else {
            $forumTopic->decrement('votes');
        }

        return response()->json([
            'votes' => $forumTopic->fresh()->votes,
        ]);
    }
}
