<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'admins'   => User::where('role', 'admin')->count(),
            'sellers'  => User::where('role', 'vendeur')->count(),
            'newMonth' => User::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', '❌ Impossible de supprimer un compte administrateur.');
        }

        $name = $user->name;

        // Supprimer boutique + produits si vendeur
        if ($user->shop) {
            $user->shop->products()->delete();
            $user->shop->delete();
        }

        $user->delete();

        return back()->with('success', "✅ Utilisateur « {$name} » supprimé avec succès.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Impossible de suspendre un administrateur.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $etat = $user->is_active ? 'réactivé ✅' : 'suspendu 🔒';

        return back()->with('success', "Utilisateur « {$user->name} » {$etat}.");
    }
}
