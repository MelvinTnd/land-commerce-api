<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Heritage Modernist Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet">
    <style>
        /* Prevent Material Symbols FOUT — completely hide text/icon until the font is loaded */
        html:not(.mso-loaded) .material-symbols-outlined {
            visibility: hidden !important;
            opacity: 0 !important;
            display: inline-block;
            width: 1em; /* maintain space */
        }
    </style>

    <style>
        /* =============================================
           ADMIN DASHBOARD — HERITAGE MODERNIST
           ============================================= */
        :root {
            --adm-green:       #1B6B3A;
            --adm-green-dark:  #134d2b;
            --adm-green-mid:   #2E8B57;
            --adm-green-soft:  #e8f5ed;
            --adm-green-xsoft: #f0faf4;
            --adm-gold:        #D4920A;
            --adm-gold-soft:   #fff8e6;
            --adm-bg:          #f4f6f4;
            --adm-card:        #ffffff;
            --adm-border:      #e2e8e2;
            --adm-text:        #1a1a1a;
            --adm-text2:       #4a5568;
            --adm-text3:       #9ba8a3;
            --adm-red:         #e53e3e;
            --adm-red-soft:    #fff5f5;
            --adm-orange:      #d97706;
            --adm-orange-soft: #fffbeb;
            --adm-sidebar-w:   168px;
            --adm-header-h:    64px;
            --adm-radius:      12px;
            --adm-radius-sm:   8px;
            --adm-shadow:      0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);
            --adm-shadow-md:   0 4px 16px rgba(0,0,0,0.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--adm-bg);
            color: var(--adm-text);
            min-height: 100vh;
            display: flex;
        }

        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        /* ── SIDEBAR ── */
        .adm-sidebar {
            width: var(--adm-sidebar-w);
            background: var(--adm-card);
            border-right: 1px solid var(--adm-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .adm-sidebar::-webkit-scrollbar { width: 0; }

        .adm-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 16px 16px;
            border-bottom: 1px solid var(--adm-border);
        }
        .adm-logo-icon {
            width: 36px; height: 36px;
            background: var(--adm-green);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .adm-logo-icon .material-symbols-outlined { color: #fff; font-size: 20px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        .adm-logo-text strong { display:block; font-size:12px; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif; color:var(--adm-text); line-height:1.2; }
        .adm-logo-text small  { font-size:9px; color:var(--adm-text3); text-transform:uppercase; letter-spacing:0.8px; font-weight:500; }

        .adm-nav { flex:1; padding:12px 0; }
        .adm-nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            color: var(--adm-text2);
            font-size: 13px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: background 0.18s, color 0.18s;
        }
        .adm-nav-item:hover { background:var(--adm-green-xsoft); color:var(--adm-green); }
        .adm-nav-item.active { background:var(--adm-green-soft); color:var(--adm-green); border-left-color:var(--adm-green); font-weight:600; }
        .adm-nav-item .material-symbols-outlined { font-size:20px; font-variation-settings:'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 24; }
        .adm-nav-item.active .material-symbols-outlined { font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

        .adm-sidebar-footer {
            padding:12px 16px;
            border-top:1px solid var(--adm-border);
            display:flex; align-items:center; gap:10px;
        }
        .adm-user-avatar {
            width:34px; height:34px; border-radius:50%;
            background:var(--adm-green-soft);
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; color:var(--adm-green);
            flex-shrink:0;
        }
        .adm-user-info strong { display:block; font-size:12px; font-weight:600; }
        .adm-user-info small  { font-size:10px; color:var(--adm-text3); }
        .adm-logout-btn {
            margin-left:auto; background:none; border:none; cursor:pointer;
            padding:6px; border-radius:6px; display:flex; align-items:center;
            color:var(--adm-text3); transition:background 0.15s, color 0.15s;
            flex-shrink:0;
        }
        .adm-logout-btn:hover { background:var(--adm-red-soft); color:var(--adm-red); }
        .adm-logout-btn .material-symbols-outlined { font-size:18px; }

        /* ── MAIN ── */
        .adm-main { margin-left:var(--adm-sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

        /* ── HEADER ── */
        .adm-header {
            height: var(--adm-header-h);
            background: var(--adm-card);
            border-bottom: 1px solid var(--adm-border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 50;
            gap: 16px;
        }
        .adm-search { position:relative; flex:1; max-width:360px; }
        .adm-search input {
            width:100%; background:var(--adm-bg);
            border:1px solid var(--adm-border); border-radius:8px;
            padding:8px 12px 8px 36px; font-size:13px; color:var(--adm-text);
            outline:none; font-family:inherit;
            transition:border-color 0.18s;
        }
        .adm-search input::placeholder { color:var(--adm-text3); }
        .adm-search input:focus { border-color:var(--adm-green); }
        .adm-search-icon { position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:18px; color:var(--adm-text3); }
        .adm-header-right { display:flex; align-items:center; gap:16px; }
        .adm-notif-btn {
            position:relative; background:none; border:none; cursor:pointer;
            padding:6px; border-radius:8px; display:flex; align-items:center;
            transition:background 0.18s;
        }
        .adm-notif-btn:hover { background:var(--adm-bg); }
        .adm-notif-btn .material-symbols-outlined { font-size:22px; color:var(--adm-text2); }
        .adm-notif-dot { position:absolute; top:4px; right:4px; width:8px; height:8px; background:var(--adm-red); border-radius:50%; border:2px solid #fff; }
        .adm-header-user { font-size:13px; font-weight:600; color:var(--adm-text); }

        /* ── CONTENT ── */
        .adm-content { padding: 28px; flex: 1; }

        /* ── PAGE HEADER ── */
        .adm-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; gap:16px; }
        .adm-page-header h1 { font-family:'Plus Jakarta Sans',sans-serif; font-size:24px; font-weight:700; line-height:1.2; }
        .adm-page-header p { font-size:13.5px; color:var(--adm-text2); margin-top:3px; }
        .adm-header-actions { display:flex; align-items:center; gap:10px; flex-shrink:0; }

        /* ── BUTTONS ── */
        .adm-btn {
            display:inline-flex; align-items:center; gap:6px;
            padding:9px 18px; border-radius:8px;
            font-size:13px; font-weight:600; cursor:pointer;
            border:none; transition:all 0.18s; white-space:nowrap;
            font-family:inherit;
        }
        .adm-btn .material-symbols-outlined { font-size:16px; }
        .adm-btn-outline { background:var(--adm-card); border:1px solid var(--adm-border); color:var(--adm-text2); }
        .adm-btn-outline:hover { background:var(--adm-bg); border-color:var(--adm-green); color:var(--adm-green); }
        .adm-btn-primary { background:var(--adm-green); color:#fff; }
        .adm-btn-primary:hover { background:var(--adm-green-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(27,107,58,0.3); }
        .adm-btn-sm { padding:6px 12px; font-size:12px; }
        .adm-btn-danger { background:var(--adm-red-soft); color:var(--adm-red); border:1px solid #fed7d7; }

        /* ── STATS GRID ── */
        .adm-stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:24px; }
        .adm-stat-card {
            background:var(--adm-card); border:1px solid var(--adm-border);
            border-radius:var(--adm-radius); padding:20px;
            transition:box-shadow 0.18s;
            animation:fadeInUp 0.35s ease both;
        }
        .adm-stat-card.featured { grid-column:span 2; }
        .adm-stat-card:hover { box-shadow:var(--adm-shadow-md); }
        .adm-stat-card-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:12px; }
        .adm-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .adm-stat-icon .material-symbols-outlined { font-size:22px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        .adm-stat-icon.green  { background:var(--adm-green-soft); } .adm-stat-icon.green  .material-symbols-outlined { color:var(--adm-green); }
        .adm-stat-icon.gold   { background:var(--adm-gold-soft);  } .adm-stat-icon.gold   .material-symbols-outlined { color:var(--adm-gold); }
        .adm-stat-icon.orange { background:var(--adm-orange-soft);} .adm-stat-icon.orange .material-symbols-outlined { color:var(--adm-orange); }
        .adm-stat-icon.red    { background:var(--adm-red-soft);   } .adm-stat-icon.red    .material-symbols-outlined { color:var(--adm-red); }
        .adm-stat-badge { display:inline-flex; align-items:center; gap:3px; padding:3px 8px; border-radius:20px; font-size:11.5px; font-weight:600; }
        .adm-stat-badge .material-symbols-outlined { font-size:13px; }
        .adm-stat-badge.up   { background:var(--adm-green-soft); color:var(--adm-green); }
        .adm-stat-badge.down { background:var(--adm-red-soft);   color:var(--adm-red); }
        .adm-stat-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:var(--adm-text3); margin-bottom:6px; }
        .adm-stat-value { font-family:'Plus Jakarta Sans',sans-serif; font-size:28px; font-weight:800; line-height:1; }
        .adm-stat-value.large { font-size:32px; }
        .adm-stat-unit { font-size:0.42em; font-weight:500; margin-left:5px; color:var(--adm-text3); }
        .adm-stat-sub { font-size:12px; color:var(--adm-text3); margin-top:4px; }
        .adm-stat-sub strong { color:var(--adm-green); font-weight:600; }
        .adm-mini-chart { display:flex; align-items:flex-end; gap:5px; height:48px; margin-top:16px; }
        .adm-mini-bar { flex:1; background:var(--adm-green-soft); border-radius:4px 4px 0 0; }
        .adm-mini-bar.active { background:var(--adm-green); }

        /* ── GRID LAYOUTS ── */
        .adm-grid-3-1 { display:grid; grid-template-columns:3fr 2fr; gap:20px; margin-bottom:20px; }

        /* ── CARDS ── */
        .adm-card { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); overflow:hidden; animation:fadeInUp 0.35s ease both; }
        .adm-card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-bottom:1px solid var(--adm-border); }
        .adm-card-header h2 { font-family:'Plus Jakarta Sans',sans-serif; font-size:15px; font-weight:700; }
        .adm-card-header p { font-size:12px; color:var(--adm-text3); margin-top:2px; }
        .adm-card-link { font-size:12.5px; color:var(--adm-green); font-weight:600; display:flex; align-items:center; gap:2px; cursor:pointer; white-space:nowrap; transition:gap 0.18s; }
        .adm-card-link:hover { gap:6px; }
        .adm-card-link .material-symbols-outlined { font-size:16px; }

        /* ── DISCUSSIONS ── */
        .adm-discussion-item {
            padding:16px 20px; border-bottom:1px solid var(--adm-border);
            display:flex; gap:14px; cursor:pointer;
            transition:background 0.15s; position:relative;
        }
        .adm-discussion-item:last-child { border-bottom:none; }
        .adm-discussion-item:hover { background:var(--adm-bg); }
        .adm-discussion-item.pinned { border-left:3px solid var(--adm-green); }
        .adm-disc-avatar { width:38px; height:38px; border-radius:50%; background:var(--adm-green-soft); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; color:var(--adm-green); flex-shrink:0; }
        .adm-disc-content { flex:1; min-width:0; }
        .adm-disc-title { font-size:13.5px; font-weight:600; margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .adm-disc-excerpt { font-size:12px; color:var(--adm-text3); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .adm-disc-meta { display:flex; align-items:center; gap:12px; margin-top:6px; }
        .adm-disc-stat { display:flex; align-items:center; gap:3px; font-size:11.5px; color:var(--adm-text3); }
        .adm-disc-stat .material-symbols-outlined { font-size:14px; }
        .adm-disc-time { font-size:11px; color:var(--adm-text3); position:absolute; top:16px; right:20px; }
        .adm-disc-badge-resolved { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:10.5px; font-weight:600; background:var(--adm-green-soft); color:var(--adm-green); }
        .adm-disc-badge-resolved .material-symbols-outlined { font-size:12px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        .adm-disc-avatars { display:flex; }
        .mini-av { width:20px; height:20px; border-radius:50%; background:var(--adm-green-soft); border:2px solid #fff; margin-left:-6px; font-size:9px; font-weight:700; color:var(--adm-green); display:flex; align-items:center; justify-content:center; }

        /* ── BLOG ARTICLES ── */
        .adm-blog-item { border-bottom:1px solid var(--adm-border); cursor:pointer; transition:background 0.15s; }
        .adm-blog-item:last-child { border-bottom:none; }
        .adm-blog-thumbnail { width:100%; height:120px; object-fit:cover; display:block; }
        .adm-blog-thumb-placeholder { width:100%; height:120px; display:flex; align-items:center; justify-content:center; }
        .adm-blog-thumb-placeholder .material-symbols-outlined { font-size:48px; color:rgba(255,255,255,0.35); font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        .adm-blog-info { padding:14px 20px; }
        .adm-blog-tag { display:inline-block; padding:2px 8px; background:var(--adm-green-soft); color:var(--adm-green); font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; border-radius:4px; margin-bottom:6px; }
        .adm-blog-title { font-size:13.5px; font-weight:700; margin-bottom:4px; line-height:1.3; }
        .adm-blog-author { font-size:11.5px; color:var(--adm-text3); }
        .adm-blog-add { display:flex; align-items:center; gap:8px; padding:14px 20px; font-size:13px; font-weight:600; color:var(--adm-green); cursor:pointer; background:none; border:none; width:100%; font-family:inherit; transition:background 0.15s; }
        .adm-blog-add:hover { background:var(--adm-green-xsoft); }
        .adm-blog-add .material-symbols-outlined { font-size:18px; }

        /* ── TABLE ── */
        .adm-table-wrap { overflow-x:auto; }
        .adm-table { width:100%; border-collapse:collapse; font-size:13px; }
        .adm-table thead th { padding:10px 16px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--adm-text3); text-align:left; background:var(--adm-bg); border-bottom:1px solid var(--adm-border); white-space:nowrap; }
        .adm-table tbody tr { border-bottom:1px solid var(--adm-border); transition:background 0.15s; }
        .adm-table tbody tr:last-child { border-bottom:none; }
        .adm-table tbody tr:hover { background:var(--adm-green-xsoft); }
        .adm-table tbody td { padding:14px 16px; vertical-align:middle; }
        .adm-vendor-cell { display:flex; align-items:center; gap:12px; }
        .adm-vendor-av { width:36px; height:36px; border-radius:8px; background:var(--adm-green-soft); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:var(--adm-green); flex-shrink:0; }
        .adm-vendor-name { font-weight:600; font-size:13.5px; }
        .adm-vendor-owner { font-size:11.5px; color:var(--adm-text3); }
        .adm-badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; white-space:nowrap; }
        .adm-badge-category { background:var(--adm-bg); color:var(--adm-text2); border:1px solid var(--adm-border); font-size:11px; }
        .adm-badge-pending  { background:var(--adm-orange-soft); color:var(--adm-orange); }
        .adm-badge-approved { background:var(--adm-green-soft);  color:var(--adm-green); }
        .adm-badge-rejected { background:var(--adm-red-soft);    color:var(--adm-red); }
        .adm-status-dot { width:6px; height:6px; border-radius:50%; display:inline-block; margin-right:5px; }
        .adm-status-dot.pending  { background:var(--adm-orange); }
        .adm-status-dot.approved { background:var(--adm-green); }
        .adm-status-dot.rejected { background:var(--adm-red); }
        .adm-icon-btn { width:30px; height:30px; border-radius:6px; border:none; background:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--adm-text3); transition:background 0.15s,color 0.15s; }
        .adm-icon-btn:hover { background:var(--adm-bg); color:var(--adm-text); }
        .adm-icon-btn .material-symbols-outlined { font-size:18px; }
        .adm-actions-cell { display:flex; align-items:center; gap:6px; }

        /* ── FOOTER ── */
        .adm-footer { margin-top:28px; padding:16px 0; border-top:1px solid var(--adm-border); display:flex; align-items:center; justify-content:space-between; font-size:12px; color:var(--adm-text3); }
        .adm-footer-links { display:flex; gap:20px; }
        .adm-footer-links a { color:var(--adm-text3); transition:color 0.15s; }
        .adm-footer-links a:hover { color:var(--adm-green); }
        .adm-version { color:var(--adm-green); font-weight:600; }

        /* ── RESPONSIVE ── */
        @media(max-width:1100px) {
            .adm-grid-3-1 { grid-template-columns:1fr; }
            .adm-stat-card.featured { grid-column:span 1; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(12px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .adm-stat-card:nth-child(1) { animation-delay:0.05s; }
        .adm-stat-card:nth-child(2) { animation-delay:0.10s; }
        .adm-stat-card:nth-child(3) { animation-delay:0.15s; }

        /* ── PAGE-SPECIFIC EXTRAS ── */
    </style>

    @yield('styles')
</head>
<body>

    <!-- ══ SIDEBAR ══ -->
    <aside class="adm-sidebar">
        <div class="adm-logo">
            <div class="adm-logo-icon">
                <span class="material-symbols-outlined">storefront</span>
            </div>
            <div class="adm-logo-text">
                <strong>Heritage Modernist</strong>
                <small>Marketplace Admin</small>
            </div>
        </div>

        <nav class="adm-nav">
            @php
                $currentRoute = Route::currentRouteName();
            @endphp
            @foreach([
                ['route'=>'admin.dashboard',   'label'=>'Dashboard',    'icon'=>'dashboard'],
                ['route'=>'admin.sellers',      'label'=>'Vendeurs',     'icon'=>'storefront'],
                ['route'=>'admin.products',     'label'=>'Produits',     'icon'=>'inventory_2'],
                ['route'=>'admin.orders',       'label'=>'Commandes',    'icon'=>'receipt_long'],
                ['route'=>'admin.promotions',   'label'=>'Promotions',   'icon'=>'local_offer'],
                ['route'=>'admin.users',        'label'=>'Utilisateurs', 'icon'=>'group'],
                ['route'=>'admin.blog',         'label'=>'Blog/News',    'icon'=>'article'],
                ['route'=>'admin.community',    'label'=>'Communauté',   'icon'=>'forum'],
                ['route'=>'admin.settings',     'label'=>'Paramètres',   'icon'=>'settings'],
            ] as $item)
                <a href="{{ route($item['route']) }}"
                   class="adm-nav-item {{ $currentRoute === $item['route'] ? 'active' : '' }}">
                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="adm-sidebar-footer">
            @php
                $authUser    = auth()->user();
                $authInitials = collect(explode(' ', $authUser->name ?? 'Admin'))
                                ->map(fn($w) => strtoupper(substr($w,0,1)))
                                ->take(2)->implode('');
            @endphp
            <div class="adm-user-avatar">{{ $authInitials }}</div>
            <div class="adm-user-info">
                <strong>{{ $authUser->name ?? 'Administrateur' }}</strong>
                <small>Super Admin</small>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="adm-logout-btn" title="Déconnexion">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ══ MAIN ══ -->
    <div class="adm-main">

        <!-- Header -->
        <header class="adm-header">
            <div class="adm-search">
                <span class="material-symbols-outlined adm-search-icon">search</span>
                <input type="text" placeholder="Rechercher des transactions, vendeurs ou articles...">
            </div>
            <div class="adm-header-right">
                <button class="adm-notif-btn" title="Notifications">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="adm-notif-dot"></span>
                </button>
                <span class="adm-header-user">{{ auth()->user()->name ?? 'Administrateur' }}</span>
            </div>
        </header>

        <!-- Content -->
        <main class="adm-content">

            {{-- ── Global Flash Messages ── --}}
            @if(session('success'))
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;background:var(--adm-green-soft);color:var(--adm-green);border:1px solid #b7dfc6;animation:fadeInUp 0.25s ease">
                    <span class="material-symbols-outlined" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;background:var(--adm-red-soft);color:var(--adm-red);border:1px solid #fed7d7;animation:fadeInUp 0.25s ease">
                    <span class="material-symbols-outlined" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">error</span>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

            <!-- Footer -->
            <footer class="adm-footer">
                <span>© 2024 Heritage Modernist Marketplace. Tous droits réservés.</span>
                <div class="adm-footer-links">
                    <a href="#">Support Technique</a>
                    <a href="#">Politique de Confidentialité</a>
                    <span class="adm-version">v2.5.0-stable</span>
                </div>
            </footer>
        </main>
    </div>

    <!-- ══ GLOBAL TOAST SYSTEM ══ -->
    <div id="adm-toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none"></div>

    <!-- ══ GLOBAL CONFIRM DELETE MODAL ══ -->
    <div id="adm-confirm-overlay"
         style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9000;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.2s">
        <div id="adm-confirm-box"
             style="background:#fff;border-radius:14px;padding:28px;max-width:400px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:scale(0.95);transition:transform 0.2s">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:20px">
                <div style="width:42px;height:42px;border-radius:10px;background:#fff5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span class="material-symbols-outlined" style="font-size:22px;color:#e53e3e;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">delete_forever</span>
                </div>
                <div>
                    <div style="font-weight:700;font-size:15px;margin-bottom:4px" id="adm-confirm-title">Confirmer la suppression</div>
                    <div style="font-size:13px;color:#4a5568" id="adm-confirm-msg">Cette action est irréversible.</div>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px">
                <button onclick="closeConfirm()" class="adm-btn adm-btn-outline adm-btn-sm">Annuler</button>
                <button id="adm-confirm-btn"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:#e53e3e;color:#fff;font-family:inherit;transition:background 0.15s"
                        onmouseenter="this.style.background='#c53030'"
                        onmouseleave="this.style.background='#e53e3e'">
                    <span class="material-symbols-outlined" style="font-size:15px">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <style>
        .adm-toast {
            display:flex; align-items:center; gap:12px;
            padding:13px 18px; border-radius:10px;
            font-size:13.5px; font-weight:600;
            box-shadow:0 8px 24px rgba(0,0,0,0.14);
            pointer-events:all; cursor:pointer;
            animation:toastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
            max-width:380px; min-width:280px;
        }
        .adm-toast.success { background:#1B6B3A; color:#fff; }
        .adm-toast.error   { background:#e53e3e; color:#fff; }
        .adm-toast.info    { background:#2563eb; color:#fff; }
        .adm-toast .material-symbols-outlined { font-size:20px; flex-shrink:0; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
        .adm-toast-out { animation:toastOut 0.3s ease forwards; }
        @keyframes toastIn  { from { opacity:0; transform:translateX(100%) scale(0.9); } to { opacity:1; transform:translateX(0) scale(1); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(120%); } }

        /* Loading spinner on form submit */
        .adm-btn-loading { position:relative; color:transparent !important; pointer-events:none; }
        .adm-btn-loading::after { content:''; position:absolute; inset:0; margin:auto; width:16px; height:16px; border:2px solid rgba(255,255,255,0.35); border-top-color:#fff; border-radius:50%; animation:spin 0.6s linear infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }
    </style>

    <script>
    /* ─── TOAST ─────────────────────────────── */
    function showToast(message, type = 'success', duration = 4500) {
        const icons = { success:'check_circle', error:'error', info:'info' };
        const c = document.getElementById('adm-toast-container');
        const div = document.createElement('div');
        div.className = `adm-toast ${type}`;
        div.innerHTML = `<span class="material-symbols-outlined">${icons[type]||'check_circle'}</span><span>${message}</span>`;
        c.appendChild(div);
        div.addEventListener('click', () => removeToast(div));
        setTimeout(() => removeToast(div), duration);
    }
    function removeToast(el) {
        el.classList.add('adm-toast-out');
        el.addEventListener('animationend', () => el.remove());
    }

    /* ─── AUTO-SHOW FLASH AS TOAST ───────────── */
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () =>
            showToast('{{ addslashes(session("success")) }}', 'success'));
    @endif
    @if(session('error'))
        document.addEventListener('DOMContentLoaded', () =>
            showToast('{{ addslashes(session("error")) }}', 'error'));
    @endif

    /* ─── CONFIRM DELETE MODAL ──────────────── */
    let _pendingForm = null;
    function confirmAction(formEl, title, msg) {
        _pendingForm = formEl;
        document.getElementById('adm-confirm-title').textContent = title || 'Confirmer la suppression';
        document.getElementById('adm-confirm-msg').textContent   = msg   || 'Cette action est irréversible.';
        const overlay = document.getElementById('adm-confirm-overlay');
        overlay.style.opacity     = '1';
        overlay.style.visibility  = 'visible';
        document.getElementById('adm-confirm-box').style.transform = 'scale(1)';
    }
    function closeConfirm() {
        const overlay = document.getElementById('adm-confirm-overlay');
        overlay.style.opacity    = '0';
        overlay.style.visibility = 'hidden';
        document.getElementById('adm-confirm-box').style.transform = 'scale(0.95)';
        _pendingForm = null;
    }
    document.getElementById('adm-confirm-btn').addEventListener('click', () => {
        if (_pendingForm) {
            const btn = document.getElementById('adm-confirm-btn');
            btn.classList.add('adm-btn-loading');
            _pendingForm.submit();
        }
        closeConfirm();
    });
    document.getElementById('adm-confirm-overlay').addEventListener('click', e => {
        if (e.target === document.getElementById('adm-confirm-overlay')) closeConfirm();
    });

    /* ─── FORM SUBMIT LOADING FEEDBACK ─────── */
    document.addEventListener('submit', e => {
        const btn = e.target.querySelector('[type=submit]');
        if (btn && !btn.closest('[data-no-loading]')) {
            btn.classList.add('adm-btn-loading');
        }
    });

    /* ─── ESCAPE KEY ────────────────────────── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeConfirm();
    });

    /* ─── MATERIAL SYMBOLS FOUT FIX ─────────── */
    document.fonts.ready.then(() => {
        document.documentElement.classList.add('mso-loaded');
    });
    // fallback: show after 1.5s even if font fails
    setTimeout(() => document.documentElement.classList.add('mso-loaded'), 1500);
    </script>

</body>
</html>
