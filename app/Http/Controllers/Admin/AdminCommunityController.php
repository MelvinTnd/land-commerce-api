<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class AdminCommunityController extends Controller
{
    /* ─────────────────────────────────────────
     | INDEX — vue communauté + modération
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'recent');

        $query = ForumTopic::latest();
        if ($sort === 'popular') {
            $query = ForumTopic::orderByDesc('votes')->orderByDesc('commentaires');
        }

        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        $discussions = $query->paginate(10)->withQueryString();

        // Discussions signalées (avec tag "signalement" ou is_reported)
        $reported = ForumTopic::where('tag', 'signalement')
            ->orWhere('is_reported', true)
            ->latest()
            ->take(5)
            ->get();

        // Top contributeurs (par votes)
        $topContributors = ForumTopic::select('auteur')
            ->selectRaw('SUM(votes) as total_votes, COUNT(*) as total_posts')
            ->groupBy('auteur')
            ->orderByDesc('total_votes')
            ->take(5)
            ->get();

        $stats = [
            'membres'      => \App\Models\User::count(),
            'discussions'  => ForumTopic::count(),
            'signalements' => ForumTopic::where('tag', 'signalement')->orWhere('is_reported', true)->count(),
        ];

        return view('admin.community.index', compact(
            'discussions', 'reported', 'topContributors', 'stats', 'sort'
        ));
    }

    /* ─────────────────────────────────────────
     | DESTROY — supprimer une discussion
     | → Liaison frontend : supprimé de l'API
     ───────────────────────────────────────── */
    public function destroy($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $titre = $topic->titre;
        $topic->delete();

        return back()->with('success', "Discussion « {$titre} » supprimée du forum et du frontend.");
    }

    /* ─────────────────────────────────────────
     | IGNORE — ignorer un signalement
     ───────────────────────────────────────── */
    public function ignore($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update([
            'is_reported' => false,
            'tag'         => $topic->tag === 'signalement' ? null : $topic->tag,
        ]);

        return back()->with('success', "Signalement ignoré pour « {$topic->titre} ».");
    }

    /* ─────────────────────────────────────────
     | PIN — épingler une discussion
     ───────────────────────────────────────── */
    public function pin($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update(['is_pinned' => !($topic->is_pinned ?? false)]);

        return back()->with('success',
            ($topic->is_pinned ? 'Discussion épinglée' : 'Discussion désépinglée') . " : « {$topic->titre} »");
    }
}
