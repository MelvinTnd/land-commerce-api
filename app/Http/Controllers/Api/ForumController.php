<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    // GET /api/forum/topics - Liste des topics
    public function index(Request $request)
    {
        $query = ForumTopic::withCount('replies')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        if ($request->filled('sort') && $request->sort === 'votes') {
            $query->orderBy('votes', 'desc');
        }

        $topics = $query->paginate(10);

        return response()->json($topics);
    }

    // GET /api/forum/topics/{id} - Détail d'un topic + ses réponses
    public function show($id)
    {
        $topic = ForumTopic::with(['user', 'replies.user'])
            ->withCount('replies')
            ->findOrFail($id);

        return response()->json($topic);
    }

    // POST /api/forum/topics - Créer un topic (auth requis)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'required|string',
            'tag'         => 'nullable|string|max:50',
            'image'       => 'nullable|url',
        ]);

        $user = $request->user();

        $topic = ForumTopic::create([
            'user_id'     => $user->id,
            'auteur'      => $user->name,
            'titre'       => $validated['titre'],
            'description' => $validated['description'],
            'tag'         => $validated['tag'] ?? null,
            'image'       => $validated['image'] ?? null,
            'votes'       => 0,
            'commentaires'=> 0,
        ]);

        return response()->json($topic, 201);
    }

    // POST /api/forum/topics/{id}/replies - Répondre à un topic (auth requis)
    public function reply(Request $request, $id)
    {
        $topic = ForumTopic::findOrFail($id);

        $validated = $request->validate([
            'contenu' => 'required|string',
        ]);

        $user = $request->user();

        $reply = ForumReply::create([
            'forum_topic_id' => $topic->id,
            'user_id'        => $user->id,
            'auteur'         => $user->name,
            'contenu'        => $validated['contenu'],
        ]);

        // Mettre à jour le compteur de commentaires
        $topic->increment('commentaires');

        return response()->json($reply->load('user'), 201);
    }

    // POST /api/forum/topics/{id}/vote - Voter pour un topic (auth requis)
    public function vote(Request $request, $id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->increment('votes');

        return response()->json(['votes' => $topic->fresh()->votes]);
    }

    // GET /api/forum/tags - Tous les tags disponibles
    public function tags()
    {
        $tags = ForumTopic::select('tag')
            ->whereNotNull('tag')
            ->distinct()
            ->pluck('tag');

        return response()->json($tags);
    }
}
