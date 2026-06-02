@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    {{-- ── PAGE HEADER ── --}}
    <div class="adm-page-header">
        <div>
            <h1>Tableau de bord</h1>
            <p>Bienvenue sur BéninMarket Admin — voici l'état de la plateforme aujourd'hui.</p>
        </div>
        <div class="adm-header-actions">
            <a href="{{ route('admin.export.pdf') }}" class="adm-btn adm-btn-outline">
                <span class="material-symbols-outlined">picture_as_pdf</span>
                Exporter PDF
            </a>
            <a href="{{ route('admin.sellers') }}" class="adm-btn adm-btn-primary">
                <span class="material-symbols-outlined">storefront</span>
                Gérer vendeurs
            </a>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="adm-stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">

        {{-- Commissions --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon green">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="adm-stat-badge up">
                    <span class="material-symbols-outlined">trending_up</span>+12.5%
                </span>
            </div>
            <div class="adm-stat-label">Commission totale</div>
            <div class="adm-stat-value">
                {{ number_format($stats['commissions'], 0, ',', '.') }}
                <span class="adm-stat-unit">FCFA</span>
            </div>
            <div class="adm-stat-sub">5% sur les ventes payées</div>
        </div>

        {{-- Utilisateurs --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon gold">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>
            <div class="adm-stat-label">Utilisateurs actifs</div>
            <div class="adm-stat-value">{{ number_format($stats['users'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <strong>{{ $stats['new_users'] }}</strong> nouveaux ce mois
            </div>
        </div>

        {{-- Boutiques --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon green">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
            </div>
            <div class="adm-stat-label">Boutiques</div>
            <div class="adm-stat-value">{{ number_format($stats['shops_total'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <strong>{{ $stats['shops_active'] }}</strong> boutiques actives
            </div>
        </div>

        {{-- Produits --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon orange">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
            </div>
            <div class="adm-stat-label">Produits listés</div>
            <div class="adm-stat-value">{{ number_format($stats['products'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">
                <strong>{{ $stats['pending_products'] }}</strong> en attente de revue
            </div>
        </div>

        {{-- Commandes --}}
        <div class="adm-stat-card">
            <div class="adm-stat-card-top">
                <div class="adm-stat-icon orange">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                @if($stats['orders_pending'] > 0)
                    <span class="adm-stat-badge" style="background:#fff8e6;color:#d97706">
                        {{ $stats['orders_pending'] }} en attente
                    </span>
                @endif
            </div>
            <div class="adm-stat-label">Commandes</div>
            <div class="adm-stat-value">{{ number_format($stats['orders_total'], 0, ',', '.') }}</div>
            <div class="adm-stat-sub">Total sur la plateforme</div>
        </div>

    </div>

    {{-- ── MINI CHART + ACTIONS RAPIDES ── --}}
    <div class="adm-grid-3-1" style="margin-bottom:20px">

        {{-- Graphe simplifié --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h2>Activité de la semaine</h2>
                    <p>Volume des transactions sur 7 jours</p>
                </div>
                <div style="display:flex;gap:6px">
                    @foreach(['7J','30J','1A'] as $p)
                        <button style="padding:5px 12px;border-radius:20px;border:none;font-size:11px;font-weight:700;cursor:pointer;
                            background:{{ $p==='7J'?'var(--adm-green)':'var(--adm-bg)' }};
                            color:{{ $p==='7J'?'#fff':'var(--adm-text3)' }}">{{ $p }}</button>
                    @endforeach
                </div>
            </div>
            <div style="padding:20px">
                <div class="adm-mini-chart" style="height:80px;gap:8px">
                    @php $bars = [30,55,40,70,60,85,100]; @endphp
                    @foreach($bars as $idx => $h)
                        <div class="adm-mini-bar @if($idx===count($bars)-1) active @endif"
                             style="height:{{ $h }}%;border-radius:6px;transition:height 0.3s"></div>
                    @endforeach
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:10px;font-size:11px;color:var(--adm-text3);font-weight:600">
                    @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $j)
                        <span>{{ $j }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="adm-card">
            <div class="adm-card-header">
                <h2>Actions rapides</h2>
            </div>
            <div style="padding:12px;display:flex;flex-direction:column;gap:6px">
                @foreach([
                    ['route'=>'admin.sellers',   'icon'=>'storefront',  'label'=>'Gérer les vendeurs',    'color'=>'var(--adm-green)'],
                    ['route'=>'admin.products',  'icon'=>'inventory_2', 'label'=>'Modérer les produits',  'color'=>'var(--adm-orange)'],
                    ['route'=>'admin.orders',    'icon'=>'receipt_long','label'=>'Voir les commandes',     'color'=>'#7C3AED'],
                    ['route'=>'admin.promotions','icon'=>'local_offer', 'label'=>'Gérer les promotions',  'color'=>'var(--adm-gold)'],
                    ['route'=>'admin.users',     'icon'=>'group',       'label'=>'Gérer les utilisateurs','color'=>'var(--adm-text2)'],
                ] as $action)
                    <a href="{{ route($action['route']) }}"
                       style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;transition:background 0.15s;color:var(--adm-text)"
                       onmouseover="this.style.background='var(--adm-bg)'" onmouseout="this.style.background='transparent'">
                        <div style="width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;
                            background:{{ $action['color'] }}18;flex-shrink:0">
                            <span class="material-symbols-outlined" style="font-size:18px;color:{{ $action['color'] }};
                                font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">{{ $action['icon'] }}</span>
                        </div>
                        <span style="font-size:13px;font-weight:600">{{ $action['label'] }}</span>
                        <span class="material-symbols-outlined" style="font-size:16px;color:var(--adm-text3);margin-left:auto">arrow_forward_ios</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── VENDEURS RÉCENTS ── --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div>
                <h2>Boutiques enregistrées</h2>
                <p>Les dernières boutiques créées sur BéninMarket.</p>
            </div>
            <a href="{{ route('admin.sellers') }}" class="adm-card-link">
                Voir tout <span class="material-symbols-outlined">open_in_new</span>
            </a>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Boutique</th>
                        <th>Vendeur</th>
                        <th>Localisation</th>
                        <th>Date d'inscription</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingShops as $shop)
                        @php
                            $statusMap = [
                                'pending'  => ['cls' => 'adm-badge-pending',  'lbl' => 'En attente'],
                                'active'   => ['cls' => 'adm-badge-approved', 'lbl' => 'Actif'],
                                'approved' => ['cls' => 'adm-badge-approved', 'lbl' => 'Approuvé'],
                                'rejected' => ['cls' => 'adm-badge-rejected', 'lbl' => 'Rejeté'],
                                'inactive' => ['cls' => 'adm-badge-rejected', 'lbl' => 'Inactif'],
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
                                        <div class="adm-vendor-owner">{{ $shop->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--adm-text2);font-size:13px">
                                {{ $shop->user->name ?? '—' }}
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:32px;color:var(--adm-text3);">
                                <span class="material-symbols-outlined" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4">store_mall_directory</span>
                                Aucune boutique enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── COMMANDES RÉCENTES ── --}}
    @if(isset($recentOrders) && $recentOrders->count() > 0)
    <div class="adm-card" style="margin-top:20px">
        <div class="adm-card-header">
            <div>
                <h2>Commandes récentes</h2>
                <p>Les 5 dernières commandes passées sur la plateforme.</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="adm-card-link">
                Voir tout <span class="material-symbols-outlined">open_in_new</span>
            </a>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Articles</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    @php
                        $oMap = [
                            'en_attente' => ['cls'=>'adm-badge-pending',  'lbl'=>'En attente'],
                            'payee'      => ['cls'=>'adm-badge-approved', 'lbl'=>'Payée'],
                            'en_livraison'=> ['cls'=>'adm-badge-approved', 'lbl'=>'En livraison'],
                            'livree'     => ['cls'=>'adm-badge-approved', 'lbl'=>'Livrée'],
                            'annulee'    => ['cls'=>'adm-badge-rejected', 'lbl'=>'Annulée'],
                        ];
                        $os = $oMap[$order->status ?? 'en_attente'] ?? $oMap['en_attente'];
                        @endphp
                        <tr>
                            <td style="font-weight:700;font-size:12px;color:var(--adm-text3)">#{{ $order->id }}</td>
                            <td style="font-weight:600;font-size:13px">{{ $order->user->name ?? '—' }}</td>
                            <td style="color:var(--adm-text2);font-size:13px">{{ $order->items->count() }} article(s)</td>
                            <td style="font-weight:700;font-size:13px;color:var(--adm-green)">
                                {{ number_format($order->total_amount ?? $order->items->sum(fn($i)=>$i->unit_price*$i->quantity), 0, ',', '.') }} FCFA
                            </td>
                            <td>
                                <span class="adm-badge {{ $os['cls'] }}">{{ $os['lbl'] }}</span>
                            </td>
                            <td style="color:var(--adm-text3);font-size:12px">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection
