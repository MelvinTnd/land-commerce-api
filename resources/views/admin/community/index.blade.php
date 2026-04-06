@extends('admin.layouts.app')

@section('title', 'Gestion Communauté')

@section('styles')
<style>
  /* ── COMMUNITY PAGE ── */
  .comm-grid { display:grid; grid-template-columns:minmax(0,2fr) minmax(0,1fr); gap:20px; }

  /* STAT CARDS */
  .comm-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
  .comm-stat-card {
    background:var(--adm-card); border:1px solid var(--adm-border);
    border-radius:var(--adm-radius); padding:18px 20px;
    animation:fadeInUp 0.3s ease both;
  }
  .comm-stat-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); margin-bottom:6px; }
  .comm-stat-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:26px; font-weight:800; }
  .comm-stat-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:20px; font-size:11px; font-weight:600; margin-left:8px; }
  .comm-stat-badge.up   { background:var(--adm-green-soft); color:var(--adm-green); }
  .comm-stat-badge.down { background:var(--adm-red-soft); color:var(--adm-red); }
  .comm-stat-badge.warn { background:var(--adm-red-soft); color:var(--adm-red); }
  .comm-stat-sub { font-size:11.5px; color:var(--adm-text3); margin-top:2px; }
  .comm-retention { background:var(--adm-green) !important; border-color:var(--adm-green) !important; }
  .comm-retention .comm-stat-label { color:rgba(255,255,255,0.75); }
  .comm-retention .comm-stat-val   { color:#fff; font-size:30px; }
  .comm-retention .comm-stat-sub   { color:rgba(255,255,255,0.8); }
  .comm-arrow { position:absolute; bottom:16px; right:16px; }
  .comm-arrow .material-symbols-outlined { font-size:32px; color:rgba(255,255,255,0.3); font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

  /* DISCUSSION ITEMS */
  .disc-item {
    display:flex; gap:14px; padding:18px 20px;
    border-bottom:1px solid var(--adm-border);
    transition:background 0.15s;
  }
  .disc-item:last-child { border-bottom:none; }
  .disc-item:hover { background:var(--adm-bg); }

  .disc-votes { display:flex; flex-direction:column; align-items:center; gap:2px; flex-shrink:0; width:36px; }
  .vote-btn { background:none; border:none; cursor:pointer; color:var(--adm-text3); padding:2px; font-size:16px; line-height:1; transition:color 0.15s; }
  .vote-btn:hover { color:var(--adm-green); }
  .vote-count { font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:700; color:var(--adm-text); }
  .vote-count.large { font-size:18px; color:var(--adm-green); }

  .tag-pill { display:inline-flex; align-items:center; padding:3px 9px; border-radius:4px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; margin-right:8px; }
  .tag-artisanat { background:#d4edda; color:#155724; }
  .tag-evenement  { background:#d1ecf1; color:#0c5460; }
  .tag-support    { background:var(--adm-red-soft); color:var(--adm-red); }
  .tag-default    { background:var(--adm-bg); color:var(--adm-text3); border:1px solid var(--adm-border); }

  .disc-title  { font-size:15px; font-weight:700; color:var(--adm-text); cursor:pointer; line-height:1.3; margin-bottom:4px; }
  .disc-title:hover { color:var(--adm-green); }
  .disc-excerpt { font-size:13px; color:var(--adm-text2); line-height:1.5; margin-bottom:8px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
  .disc-footer { display:flex; align-items:center; gap:14px; font-size:12px; color:var(--adm-text3); }
  .disc-footer-item { display:flex; align-items:center; gap:4px; }
  .disc-footer-item .material-symbols-outlined { font-size:14px; }
  .disc-signals { display:flex; align-items:center; gap:4px; color:var(--adm-red); font-weight:600; }
  .disc-traiter { padding:4px 12px; border-radius:6px; background:var(--adm-red-soft); color:var(--adm-red); border:1px solid #fed7d7; font-size:11.5px; font-weight:700; cursor:pointer; font-family:inherit; transition:all 0.15s; }
  .disc-traiter:hover { background:var(--adm-red); color:#fff; }

  .sort-tabs { display:flex; gap:6px; margin-left:auto; }
  .sort-tab { padding:5px 14px; border-radius:20px; font-size:12.5px; font-weight:600; border:1px solid var(--adm-border); background:var(--adm-card); color:var(--adm-text2); cursor:pointer; text-decoration:none; transition:all 0.15s; }
  .sort-tab.active { background:var(--adm-green); border-color:var(--adm-green); color:#fff; }

  /* MODERATION QUEUE */
  .mod-item { padding:14px 16px; border-bottom:1px solid var(--adm-border); }
  .mod-item:last-child { border-bottom:none; }
  .mod-user { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
  .mod-av { width:28px; height:28px; border-radius:50%; background:var(--adm-bg); border:1px solid var(--adm-border); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--adm-text2); flex-shrink:0; }
  .mod-username { font-size:13px; font-weight:700; }
  .mod-label { margin-left:auto; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; }
  .mod-label.injurieux { background:#fee2e2; color:#991b1b; }
  .mod-label.spam      { background:var(--adm-orange-soft); color:var(--adm-orange); }
  .mod-label.hors_sujet{ background:var(--adm-bg); color:var(--adm-text3); border:1px solid var(--adm-border); }
  .mod-quote { font-size:12.5px; color:var(--adm-text2); font-style:italic; margin-bottom:10px; line-height:1.5; }
  .mod-actions { display:flex; gap:8px; }
  .mod-btn-del { flex:1; padding:6px; border-radius:6px; border:none; background:var(--adm-red); color:#fff; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; transition:opacity 0.15s; }
  .mod-btn-del:hover { opacity:0.85; }
  .mod-btn-ign { flex:1; padding:6px; border-radius:6px; border:1px solid var(--adm-border); background:var(--adm-card); color:var(--adm-text2); font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background 0.15s; }
  .mod-btn-ign:hover { background:var(--adm-bg); }

  /* TOP CONTRIBUTORS */
  .contrib-item { display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid var(--adm-border); }
  .contrib-item:last-child { border-bottom:none; }
  .contrib-av { width:30px; height:30px; border-radius:50%; background:var(--adm-green-soft); color:var(--adm-green); font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .contrib-name { font-size:13px; font-weight:600; }
  .contrib-pts  { margin-left:auto; font-size:12.5px; font-weight:700; color:var(--adm-green); }

  /* Flash */
  .adm-flash { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:16px; }
  .adm-flash.success { background:var(--adm-green-soft); color:var(--adm-green); border:1px solid #b7dfc6; }

  @media(max-width:1000px) {
    .comm-grid { grid-template-columns:1fr; }
    .comm-stats { grid-template-columns:repeat(2,1fr); }
  }
</style>
@endsection

@section('content')

  @if(session('success'))
    <div class="adm-flash success">
      <span class="material-symbols-outlined" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">check_circle</span>
      {{ session('success') }}
    </div>
  @endif

  {{-- ── PAGE HEADER ── --}}
  <div class="adm-page-header">
    <h1>Gestion Communauté</h1>
    <form method="GET" action="{{ route('admin.community') }}" style="flex:1;max-width:360px;margin:0 16px">
      <div class="adm-search">
        <span class="material-symbols-outlined adm-search-icon">search</span>
        <input type="text" name="search" placeholder="Rechercher des discussions..."
               value="{{ request('search') }}"
               style="width:100%;background:var(--adm-bg);border:1px solid var(--adm-border);border-radius:8px;padding:8px 12px 8px 36px;font-size:13px;font-family:inherit;outline:none;">
      </div>
    </form>
  </div>

  {{-- ── STATS ── --}}
  <div class="comm-stats">
    <div class="comm-stat-card">
      <div class="comm-stat-label">Membres Actifs</div>
      <div class="comm-stat-val">
        {{ number_format($stats['membres']) }}
        <span class="comm-stat-badge up">+12%</span>
      </div>
      <div class="comm-stat-sub">Utilisateurs inscrits</div>
    </div>
    <div class="comm-stat-card">
      <div class="comm-stat-label">Discussions / Total</div>
      <div class="comm-stat-val">
        {{ number_format($stats['discussions']) }}
        <span class="comm-stat-badge down">-3%</span>
      </div>
      <div class="comm-stat-sub">Sujets du forum</div>
    </div>
    <div class="comm-stat-card">
      <div class="comm-stat-label">Signalements en attente</div>
      <div class="comm-stat-val" style="{{ $stats['signalements'] > 0 ? 'color:var(--adm-red)' : '' }}">
        {{ $stats['signalements'] }}
        @if($stats['signalements'] > 0)
          <span class="comm-stat-badge warn">Urgents</span>
        @endif
      </div>
      <div class="comm-stat-sub">À modérer</div>
    </div>
    <div class="comm-stat-card comm-retention" style="position:relative">
      <div class="comm-stat-label">Taux de Rétention</div>
      <div class="comm-stat-val">94.2%</div>
      <div class="comm-stat-sub">Niveau record ce mois-ci</div>
      <div class="comm-arrow">
        <span class="material-symbols-outlined">trending_up</span>
      </div>
    </div>
  </div>

  {{-- ── MAIN GRID ── --}}
  <div class="comm-grid">

    {{-- LEFT : Discussions --}}
    <div class="adm-card">
      <div class="adm-card-header">
        <h2>Discussions Actives</h2>
        <div class="sort-tabs">
          <a href="{{ route('admin.community', ['sort'=>'popular']) }}"
             class="sort-tab {{ $sort==='popular'?'active':'' }}">Populaires</a>
          <a href="{{ route('admin.community', ['sort'=>'recent']) }}"
             class="sort-tab {{ $sort==='recent'?'active':'' }}">Récentes</a>
        </div>
      </div>

      @forelse($discussions as $disc)
        @php
          $tagMap = [
            'artisanat'   => 'tag-artisanat',
            'evenement'   => 'tag-evenement',
            'support'     => 'tag-support',
            'signalement' => 'tag-support',
          ];
          $tagClass = $tagMap[strtolower($disc->tag ?? '')] ?? 'tag-default';
          $isSignaled = $disc->commentaires >= 3 || ($disc->tag === 'signalement');
        @endphp
        <div class="disc-item">
          {{-- Votes --}}
          <div class="disc-votes">
            <button class="vote-btn">▲</button>
            <span class="vote-count {{ $disc->votes >= 500 ? 'large' : '' }}">
              {{ $disc->votes >= 1000 ? number_format($disc->votes/1000,1).'k' : $disc->votes }}
            </span>
            <button class="vote-btn">▼</button>
          </div>

          {{-- Content --}}
          <div style="flex:1;min-width:0">
            <div style="margin-bottom:4px">
              @if($disc->tag)
                <span class="tag-pill {{ $tagClass }}">{{ strtoupper($disc->tag) }}</span>
              @endif
              <span style="font-size:11.5px;color:var(--adm-text3)">
                Posté par {{ $disc->auteur }} •
                {{ $disc->created_at->diffForHumans() }}
              </span>
            </div>
            <div class="disc-title">{{ $disc->titre }}</div>
            <div class="disc-excerpt">
              {{ \Illuminate\Support\Str::limit($disc->description ?? '', 120) }}
            </div>
            <div class="disc-footer">
              <span class="disc-footer-item">
                <span class="material-symbols-outlined">chat_bubble</span>
                {{ $disc->commentaires ?? 0 }} commentaires
              </span>
              @if($isSignaled)
                <span class="disc-signals">
                  <span class="material-symbols-outlined" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">flag</span>
                  3 signalements
                </span>
              @endif
              <div style="margin-left:auto;display:flex;gap:6px">
                @if($isSignaled)
                  <form method="POST" action="{{ route('admin.community.delete', $disc->id) }}"
                        onsubmit="return confirm('Supprimer cette discussion ?')">
                    @csrf @method('DELETE')
                    <button class="disc-traiter">Traiter</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.community.pin', $disc->id) }}">
                    @csrf @method('PATCH')
                    <button class="adm-icon-btn" title="{{ $disc->is_pinned ? 'Désépingler' : 'Épingler' }}">
                      <span class="material-symbols-outlined" style="font-size:18px">
                        {{ $disc->is_pinned ? 'bookmark_added' : 'bookmark_add' }}
                      </span>
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.community.delete', $disc->id) }}"
                        onsubmit="return confirm('Supprimer cette discussion ?')">
                    @csrf @method('DELETE')
                    <button class="adm-icon-btn" title="Supprimer" style="color:var(--adm-red)">
                      <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div style="padding:40px;text-align:center;color:var(--adm-text3)">
          <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;opacity:0.4">forum</span>
          Aucune discussion pour le moment.
        </div>
      @endforelse

      {{-- Pagination --}}
      @if($discussions->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--adm-border)">
          {{ $discussions->links() }}
        </div>
      @endif
    </div>

    {{-- RIGHT : Modération + Top Contributeurs --}}
    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Queue de Modération --}}
      <div class="adm-card">
        <div class="adm-card-header">
          <div>
            <span class="material-symbols-outlined"
                  style="font-size:18px;color:var(--adm-red);vertical-align:middle;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">
              notifications_active
            </span>
            <h2 style="display:inline;margin-left:6px">Queue de Modération</h2>
          </div>
        </div>

        @forelse($reported as $rep)
          @php
            $labelMap = ['injurieux'=>'injurieux','spam'=>'spam','signalement'=>'injurieux'];
            $labelClass = $labelMap[strtolower($rep->tag ?? '')] ?? 'hors_sujet';
            $labelText  = ucfirst($rep->tag ?? 'Hors-sujet');
          @endphp
          <div class="mod-item">
            <div class="mod-user">
              <div class="mod-av">{{ strtoupper(substr($rep->auteur ?? 'U', 0, 2)) }}</div>
              <span class="mod-username">{{ $rep->auteur }}</span>
              <span class="mod-label {{ $labelClass }}">{{ $labelText }}</span>
            </div>
            <div class="mod-quote">
              "{{ \Illuminate\Support\Str::limit($rep->description ?? $rep->titre, 100) }}"
            </div>
            <div class="mod-actions">
              <form method="POST" action="{{ route('admin.community.delete', $rep->id) }}"
                    onsubmit="return confirm('Supprimer définitivement ?')" style="flex:1">
                @csrf @method('DELETE')
                <button class="mod-btn-del" style="width:100%">Supprimer</button>
              </form>
              <form method="POST" action="{{ route('admin.community.ignore', $rep->id) }}" style="flex:1">
                @csrf @method('PATCH')
                <button class="mod-btn-ign" style="width:100%">Ignorer</button>
              </form>
            </div>
          </div>
        @empty
          <div style="padding:24px;text-align:center;color:var(--adm-text3);font-size:13px">
            <span class="material-symbols-outlined"
                  style="font-size:32px;display:block;margin-bottom:6px;color:var(--adm-green);opacity:0.5;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">
              verified
            </span>
            Aucun signalement en attente 🎉
          </div>
        @endforelse
      </div>

      {{-- Top Contributeurs --}}
      <div class="adm-card">
        <div class="adm-card-header">
          <h2>Meilleurs contributeurs</h2>
        </div>
        @forelse($topContributors as $contrib)
          <div class="contrib-item">
            <div class="contrib-av">{{ strtoupper(substr($contrib->auteur ?? 'U', 0, 2)) }}</div>
            <div>
              <div class="contrib-name">{{ $contrib->auteur }}</div>
              <div style="font-size:11px;color:var(--adm-text3)">{{ $contrib->total_posts }} posts</div>
            </div>
            <span class="contrib-pts">{{ number_format($contrib->total_votes) }} pts</span>
          </div>
        @empty
          <div style="padding:16px;text-align:center;color:var(--adm-text3);font-size:13px">Aucun contributeur.</div>
        @endforelse
      </div>

    </div>
  </div>{{-- /comm-grid --}}

@endsection
