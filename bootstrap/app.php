<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ── Admin role middleware alias ──
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // ── Gestion des utilisateurs non authentifiés ──
        // Pour les routes API (/api/*), retourner 401 JSON au lieu de rediriger vers le login HTML
        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->expectsJson() || $request->is('api/*')
                ? null  // null = pas de redirection, Laravel retournera une AuthenticationException -> 401 JSON
                : route('admin.login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Pour les routes API, toujours retourner du JSON en cas d'erreur d'authentification
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Non authentifié. Veuillez vous connecter.',
                    'error' => 'unauthenticated',
                ], 401);
            }
        });
    })->create();
