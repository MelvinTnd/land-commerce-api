<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Vérifier que l'utilisateur connecté est un administrateur.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            Auth::logout();

            return redirect()
                ->route('admin.login')
                ->with('error', "Accès non autorisé. Veuillez vous connecter avec un compte administrateur.");
        }

        return $next($request);
    }
}
