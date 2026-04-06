@extends('admin.layouts.app')

@section('title', 'Blog & Actualités')

@section('styles')
<style>
  /* ── BLOG PAGE ── */
  .blog-stat-cards { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin-bottom:24px; }
  .blog-stat-card {
    background:var(--adm-card); border:1px solid var(--adm-border);
    border-radius:var(--adm-radius); padding:20px 24px;
    display:flex; align-items:flex-start; justify-content:space-between;
    animation:fadeInUp 0.3s ease both;
  }
  .blog-stat-card:hover { box-shadow:var(--adm-shadow-md); }
  .blog-stat-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); margin-bottom:8px; }
  .blog-stat-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:32px; font-weight:800; line-height:1; }
  .blog-stat-sub { font-size:12px; color:var(--adm-green); font-weight:600; margin-top:4px; }
  .blog-stat-icon { width:40px; height:40px; border-radius:10px; background:var(--adm-green-soft); display:flex; align-items:center; justify-content:center; }
  .blog-stat-icon .material-symbols-outlined { font-size:22px; color:var(--adm-green); font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

  .filter-bar { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
  .adm-select-sm { padding:8px 28px 8px 12px; border-radius:8px; border:1px solid var(--adm-border); background:var(--adm-card); font-size:13px; font-weight:500; color:var(--adm-text); cursor:pointer; outline:none; font-family:inherit; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%239ba8a3' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
  .adm-date-input { padding:8px 12px; border-radius:8px; border:1px solid var(--adm-border); background:var(--adm-card); font-size:13px; color:var(--adm-text); font-family:inherit; outline:none; }

  .article-thumb { width:52px; height:52px; border-radius:8px; object-fit:cover; background:var(--adm-bg); border:1px solid var(--adm-border); flex-shrink:0; overflow:hidden; display:flex; align-items:center; justify-content:center; }
  .article-thumb img { width:100%; height:100%; object-fit:cover; }
  .article-thumb .material-symbols-outlined { font-size:24px; color:var(--adm-text3); opacity:0.6; font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24; }
  .article-title { font-size:13.5px; font-weight:700; color:var(--adm-text); max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .article-views { font-size:11.5px; color:var(--adm-text3); display:flex; align-items:center; gap:3px; margin-top:2px; }
  .article-views .material-symbols-outlined { font-size:14px; }

  .author-cell { display:flex; align-items:center; gap:8px; }
  .author-av { width:28px; height:28px; border-radius:50%; background:var(--adm-green-soft); color:var(--adm-green); font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

  /* Status pill */
  .pill { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; }
  .pill-dot { width:7px; height:7px; border-radius:50%; display:inline-block; }
  .pill.publié      { background:var(--adm-green-soft); color:var(--adm-green); }
  .pill.publié .pill-dot      { background:var(--adm-green); }
  .pill.brouillon   { background:#f3f4f6; color:#6b7280; }
  .pill.brouillon .pill-dot   { background:#9ca3af; }
  .pill.en_attente  { background:var(--adm-gold-soft); color:var(--adm-gold); }
  .pill.en_attente .pill-dot  { background:var(--adm-gold); }

  /* Flash message */
  .adm-flash { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:16px; animation:fadeInUp 0.25s ease; }
  .adm-flash.success { background:var(--adm-green-soft); color:var(--adm-green); border:1px solid #b7dfc6; }
  .adm-flash.error   { background:var(--adm-red-soft); color:var(--adm-red); }
  .adm-flash .material-symbols-outlined { font-size:18px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
</style>
@endsection

@section('content')

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="adm-flash success">
      <span class="material-symbols-outlined">check_circle</span>
      {{ session('success') }}
    </div>
  @endif

  {{-- ── PAGE HEADER ── --}}
  <div class="adm-page-header">
    <div>
      <h1>Blog &amp; Actualités</h1>
      <p>Gérez le contenu éditorial de la plateforme Heritage.<br>
         Rédigez, éditez et suivez la performance de vos publications.</p>
    </div>
    <a href="{{ route('admin.blog.create') }}" class="adm-btn adm-btn-primary">
      <span class="material-symbols-outlined">add</span>
      Nouvel Article
    </a>
  </div>

  {{-- ── STAT CARDS ── --}}
  <div class="blog-stat-cards">
    <div class="blog-stat-card">
      <div>
        <div class="blog-stat-label">Articles Publiés</div>
        <div class="blog-stat-val">{{ number_format($stats['publies']) }}</div>
        <div class="blog-stat-sub">+{{ $stats['en_attente'] }} en attente</div>
      </div>
      <div class="blog-stat-icon"><span class="material-symbols-outlined">check_circle</span></div>
    </div>
    <div class="blog-stat-card">
      <div>
        <div class="blog-stat-label">Total Articles</div>
        <div class="blog-stat-val">{{ number_format($stats['total']) }}</div>
        <div class="blog-stat-sub">{{ $stats['brouillons'] }} brouillons</div>
      </div>
      <div class="blog-stat-icon"><span class="material-symbols-outlined">article</span></div>
    </div>
    <div class="blog-stat-card">
      <div>
        <div class="blog-stat-label">Temps de Lecture Moy.</div>
        <div class="blog-stat-val" style="font-size:24px">{{ $stats['total'] > 0 ? '4m 12s' : '—' }}</div>
        <div class="blog-stat-sub">Moyenne stable</div>
      </div>
      <div class="blog-stat-icon"><span class="material-symbols-outlined">timer</span></div>
    </div>
  </div>

  {{-- ── FILTERS ── --}}
  <form method="GET" action="{{ route('admin.blog') }}" class="filter-bar">
    <select name="categorie" class="adm-select-sm" onchange="this.form.submit()">
      <option value="">Toutes les catégories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ request('categorie') == $cat ? 'selected' : '' }}>
          {{ $cat }}
        </option>
      @endforeach
    </select>

    <select name="statut" class="adm-select-sm" onchange="this.form.submit()">
      <option value="">Tous les statuts</option>
      <option value="publié"     {{ request('statut')=='publié'     ? 'selected' : '' }}>Publié</option>
      <option value="brouillon"  {{ request('statut')=='brouillon'  ? 'selected' : '' }}>Brouillon</option>
      <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected' : '' }}>En attente</option>
    </select>

    <input type="date" name="date" class="adm-date-input"
           value="{{ request('date') }}" onchange="this.form.submit()">

    <div style="margin-left:auto;">
      <button type="button" class="adm-btn adm-btn-outline adm-btn-sm">
        <span class="material-symbols-outlined">download</span>
        Exporter
      </button>
    </div>
  </form>

  {{-- ── TABLE ── --}}
  <div class="adm-card">
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Article</th>
            <th>Statut</th>
            <th>Auteur</th>
            <th>Catégorie</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($articles as $article)
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:12px;">
                  <div class="article-thumb">
                    @if($article->image)
                      <img src="{{ $article->image }}" alt="{{ $article->titre }}">
                    @else
                      <span class="material-symbols-outlined">image</span>
                    @endif
                  </div>
                  <div>
                    <div class="article-title">{{ $article->titre }}</div>
                    <div class="article-views">
                      <span class="material-symbols-outlined">visibility</span>
                      {{ $article->views ?? '—' }} vues
                    </div>
                  </div>
                </div>
              </td>
              <td>
                @php
                  $s = $article->statut ?? 'brouillon';
                  $labels = ['publié'=>'Publié','brouillon'=>'Brouillon','en_attente'=>'En attente'];
                @endphp
                <form method="POST"
                      action="{{ route('admin.blog.toggle', $article->id) }}"
                      style="display:inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="pill {{ str_replace(' ','_',$s) }}"
                          title="Cliquer pour changer le statut">
                    <span class="pill-dot"></span>
                    {{ $labels[$s] ?? $s }}
                  </button>
                </form>
              </td>
              <td>
                <div class="author-cell">
                  <div class="author-av">{{ strtoupper(substr($article->auteur ?? 'A', 0, 2)) }}</div>
                  <span style="font-size:13px;font-weight:500">{{ $article->auteur ?? '—' }}</span>
                </div>
              </td>
              <td>
                <span style="font-size:12.5px;color:var(--adm-text2);font-weight:500">
                  {{ $article->categorie ?? '—' }}
                </span>
              </td>
              <td style="font-size:12.5px;color:var(--adm-text2);white-space:nowrap">
                {{ $article->created_at->format('d M Y') }}
              </td>
              <td>
                <div class="adm-actions-cell">
                  <a href="{{ route('admin.blog.edit', $article->id) }}"
                     class="adm-icon-btn" title="Modifier">
                    <span class="material-symbols-outlined">edit</span>
                  </a>
                  <form method="POST"
                        action="{{ route('admin.blog.destroy', $article->id) }}"
                        onsubmit="return false"
                        data-confirm-title="Supprimer cet article ?"
                        data-confirm-msg="« {{ addslashes($article->titre) }} » sera définitivement supprimé.">
                    @csrf @method('DELETE')
                    <button class="adm-icon-btn" title="Supprimer"
                            style="color:var(--adm-red)"
                            onclick="confirmAction(this.closest('form'), this.closest('form').dataset.confirmTitle, this.closest('form').dataset.confirmMsg)">
                      <span class="material-symbols-outlined">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;padding:40px;color:var(--adm-text3)">
                <span class="material-symbols-outlined"
                      style="font-size:40px;display:block;margin-bottom:8px;opacity:0.4">article</span>
                Aucun article trouvé.
                <br><a href="{{ route('admin.blog.create') }}"
                       style="color:var(--adm-green);font-weight:600">Créer le premier article →</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($articles->hasPages())
      <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-top:1px solid var(--adm-border);font-size:12.5px;color:var(--adm-text3)">
        <span>Affichage de {{ $articles->firstItem() }} sur {{ number_format($articles->total()) }} articles</span>
        <div class="adm-page-btns">
          @if(!$articles->onFirstPage())
            <a href="{{ $articles->previousPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif
          @foreach(range(1, min($articles->lastPage(), 5)) as $p)
            <a href="{{ $articles->url($p) }}"
               class="adm-page-btn {{ $articles->currentPage()===$p?'active':'' }}">{{ $p }}</a>
          @endforeach
          @if($articles->lastPage() > 5)
            <span class="adm-page-btn" style="border:none;background:none">…</span>
            <a href="{{ $articles->url($articles->lastPage()) }}" class="adm-page-btn">{{ $articles->lastPage() }}</a>
          @endif
          @if($articles->hasMorePages())
            <a href="{{ $articles->nextPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          @endif
        </div>
      </div>
    @endif
  </div>

  <style>
    .adm-page-btns { display:flex;align-items:center;gap:4px; }
    .adm-page-btn { width:32px;height:32px;border-radius:8px;border:1px solid var(--adm-border);background:var(--adm-card);display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:600;cursor:pointer;color:var(--adm-text2);transition:all 0.15s;text-decoration:none; }
    .adm-page-btn:hover { border-color:var(--adm-green);color:var(--adm-green); }
    .adm-page-btn.active { background:var(--adm-green);border-color:var(--adm-green);color:#fff; }
    .adm-page-btn .material-symbols-outlined { font-size:16px; }
  </style>

@endsection
