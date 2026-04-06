@extends('admin.layouts.app')

@section('title', $page)

@section('content')
    <div class="adm-page-header">
        <div>
            <h1>{{ $page }}</h1>
            <p>Cette section est en cours de développement.</p>
        </div>
    </div>

    <div class="adm-card" style="padding:60px 40px;text-align:center;">
        <span class="material-symbols-outlined" style="font-size:64px;color:var(--adm-green-soft);font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48;display:block;margin-bottom:16px;">
            {{ $icon }}
        </span>
        <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:20px;font-weight:700;margin-bottom:8px;color:var(--adm-text);">
            {{ $page }} — Bientôt disponible
        </h2>
        <p style="color:var(--adm-text3);font-size:14px;max-width:400px;margin:0 auto 24px;">
            Cette page est en cours de développement. Envoyez la maquette de cette section pour que je l'implémente.
        </p>
        <a href="{{ route('admin.dashboard') }}" class="adm-btn adm-btn-primary">
            <span class="material-symbols-outlined">arrow_back</span>
            Retour au Dashboard
        </a>
    </div>
@endsection
