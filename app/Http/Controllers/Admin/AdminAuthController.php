<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Afficher la page de connexion admin.
     */
    public function showLogin()
    {
        // Si déjà connecté comme admin → rediriger vers le dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Traiter la tentative de connexion.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => "L'adresse e-mail est obligatoire.",
            'email.email'       => "Veuillez saisir une adresse e-mail valide.",
            'password.required' => "Le mot de passe est obligatoire.",
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Vérifier que l'utilisateur est bien un administrateur
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => "Accès refusé. Vous n'avez pas les droits administrateur."]);
            }

            // Régénérer la session pour éviter la fixation de session
            $request->session()->regenerate();

            return redirect()
                ->intended(route('admin.dashboard'))
                ->with('success', "Bienvenue, {$user->name} ! Connexion réussie.");
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Identifiants incorrects. Vérifiez votre e-mail et mot de passe.',
            ]);
    }

    /**
     * Déconnexion de l'administrateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
