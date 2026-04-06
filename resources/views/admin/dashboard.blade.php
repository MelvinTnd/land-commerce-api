@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-page-header">
        <div>
            <h1>Tableau de bord Global</h1>
            <p>Bienvenue, voici l'état de votre écosystème aujourd'hui.</p>
        </div>
        <div class="adm-header-actions">
            <a href="{{ route('admin.export.pdf') }}" class="adm-btn adm-btn-outline">
                <span class="material-symbols-outlined">picture_as_pdf</span>
                Exporter PDF
            </a>
            <button class="adm-btn adm-btn-primary">
                <span class="material-symbols-outlined">campaign</span>
                Nouvelle Campagne
            </button>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats-grid">

        {{-- Commissions --}}
        <div class="adm-stat-card featured">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon green">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="adm-stat-badge up">
                    <span class="material-symbols-outlined">trending_up</span>+12.5%
                </span>
            </div>
            <div class="adm-stat-label">Volume des Commissions</div>
            <div class="adm-stat-value large">
                {{ number_format($stats['commissions'], 0, ',', '.') }}
                <span class="adm-stat-unit">FCFA</span>
            </div>
            <div class="adm-mini-chart">
                @php $bars = [35, 50, 40, 60, 55, 75, 90]; @endphp
                @foreach($bars as $idx => $h)
                    <div class="adm-mini-bar @if($idx === count($bars)-1) active @endif"
                         style="height:{{ $h }}%"></div>
                @endforeach
            </div>
        </div>

        {{-- Utilisateurs --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon gold">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>
            <div class="adm-stat-label">Utilisateurs Actifs</div>
            <div class="adm-stat-value">{{ number_format($stats['users'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <strong>{{ number_format($stats['new_users'], 0, ',', '.') }}</strong> nouveaux ce mois
            </div>
        </div>

        {{-- Produits --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon orange">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
            </div>
            <div class="adm-stat-label">Produits Listés</div>
            <div class="adm-stat-value">{{ number_format($stats['products'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <strong>{{ $stats['pending_products'] }}</strong> en attente de revue
            </div>
        </div>

    </div>

    {{-- ── DISCUSSIONS + BLOG ── --}}
    <div class="adm-grid-3-1">

        {{-- Discussions --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <h2>Discussions Communautaires Actives</h2>
                <a href="{{ route('admin.community') }}" class="adm-card-link">
                    Voir tout <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            @forelse($discussions as $disc)
                <div class="adm-discussion-item">
                    <div class="adm-disc-avatar">
                        {{ strtoupper(substr($disc->auteur ?? 'U', 0, 2)) }}
                    </div>
                    <div class="adm-disc-content">
                        <div class="adm-disc-title">{{ $disc->titre }}</div>
                        <div class="adm-disc-excerpt">
                            {{ \Illuminate\Support\Str::limit($disc->description ?? '', 80) }}
                        </div>
                        <div class="adm-disc-meta">
                            <span class="adm-disc-stat">
                                <span class="material-symbols-outlined">chat_bubble</span>
                                {{ $disc->commentaires ?? 0 }} réponses
                            </span>
                            @if($disc->votes)
                                <span class="adm-disc-stat">
                                    <span class="material-symbols-outlined">favorite</span>
                                    {{ $disc->votes }} likes
                                </span>
                            @endif
                            @if(isset($disc->tag) && $disc->tag)
                                <span class="adm-disc-badge-resolved">
                                    <span class="material-symbols-outlined">label</span>
                                    {{ $disc->tag }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="adm-disc-time">
                        {{ $disc->created_at ? strtoupper($disc->created_at->diffForHumans(null, true)) : '' }}
                    </span>
                </div>
            @empty
                <div style="padding:32px 20px;text-align:center;color:var(--adm-text3);font-size:13px;">
                    <span class="material-symbols-outlined"
                          style="font-size:36px;display:block;margin-bottom:8px;opacity:0.5">forum</span>
                    Aucune discussion pour le moment.
                </div>
            @endforelse
        </div>

        {{-- Articles de Blog --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <h2>Nouveaux Articles de Blog</h2>
            </div>

            @forelse($articles as $article)
                <div class="adm-blog-item">
                    @if($article->image)
                        <img src="{{ $article->image }}" alt="{{ $article->titre }}" class="adm-blog-thumbnail">
                    @else
                        @php
                            $isArtisan = str_contains(strtolower($article->categorie ?? ''), 'artisan');
                            $bg = $isArtisan
                                ? 'linear-gradient(135deg,#1B6B3A,#2E8B57)'
                                : 'linear-gradient(135deg,#0f1b2d,#1a3050)';
                            $ico = $isArtisan ? 'gallery_thumbnail' : 'phone_android';
                        @endphp
                        <div class="adm-blog-thumb-placeholder" style="background:{{ $bg }}">
                            <span class="material-symbols-outlined">{{ $ico }}</span>
                        </div>
                    @endif
                    <div class="adm-blog-info">
                        <span class="adm-blog-tag">{{ $article->categorie ?? 'Général' }}</span>
                        <div class="adm-blog-title">{{ $article->titre }}</div>
                        <div class="adm-blog-author">
                            Publié par {{ $article->auteur ?? 'l\'équipe' }}
                            @if($article->read_time)
                                • {{ $article->read_time }} min de lecture
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:20px;text-align:center;color:var(--adm-text3);font-size:13px;">
                    Aucun article récent.
                </div>
            @endforelse

            <a href="{{ route('admin.blog.create') }}" class="adm-blog-add">
                <span class="material-symbols-outlined">add_circle</span>
                Rédiger un nouvel article
            </a>
        </div>

    </div>

    {{-- ── TABLE VENDEURS ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div>
                <h2>Demandes de Vendeurs Récents</h2>
                <p>Vérifiez les documents pour approbation finale.</p>
            </div>
            <a href="{{ route('admin.sellers') }}" class="adm-card-link">
                Gérer les approbations
                <span class="material-symbols-outlined">open_in_new</span>
            </a>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Vendeur</th>
                        <th>Localisation</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingShops as $shop)
                        @php
                            $statusMap = [
                                'pending'  => ['cls' => 'adm-badge-pending',  'lbl' => 'En attente'],
                                'approved' => ['cls' => 'adm-badge-approved', 'lbl' => 'Approuvé'],
                                'rejected' => ['cls' => 'adm-badge-rejected', 'lbl' => 'Rejeté'],
                            ];
                            $st = $statusMap[$shop->status ?? 'pending'] ?? $statusMap['pending'];
                        @endphp
                        <tr>
                            <td>
                                <div class="adm-vendor-cell">
                                    <div class="adm-vendor-av">
                                        {{ strtoupper(substr($shop->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="adm-vendor-name">{{ $shop->name }}</div>
                                        <div class="adm-vendor-owner">{{ $shop->user->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--adm-text2);font-size:13px">
                                {{ $shop->location ?? '—' }}
                            </td>
                            <td style="color:var(--adm-text2);font-size:13px">
                                {{ $shop->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <span class="adm-badge {{ $st['cls'] }}">
                                    <span class="adm-status-dot {{ $shop->status ?? 'pending' }}"></span>
                                    {{ $st['lbl'] }}
                                </span>
                            </td>
                            <td>
                                <div class="adm-actions-cell">
                                    <a href="{{ route('admin.sellers.show', $shop->id) }}"
                                       class="adm-icon-btn" title="Voir">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <button class="adm-icon-btn" title="Options">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:28px;color:var(--adm-text3);">
                                Aucune boutique enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
