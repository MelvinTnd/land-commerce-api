@extends('admin.layouts.app')

@section('title', 'Gestion des Vendeurs')

@section('styles')
<style>
  /* ── SELLERS PAGE ── */
  .adm-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .adm-filter-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 20px;
    font-size: 12.5px; font-weight: 600;
    border: 1px solid var(--adm-border);
    background: var(--adm-card); color: var(--adm-text2);
    cursor: pointer; transition: all 0.15s; font-family: inherit;
  }
  .adm-filter-btn:hover { border-color: var(--adm-green); color: var(--adm-green); }
  .adm-filter-btn.active { background: var(--adm-green); border-color: var(--adm-green); color: #fff; }
  .adm-filter-btn .material-symbols-outlined { font-size: 15px; }

  .adm-sort-wrap { margin-left: auto; display: flex; align-items: center; gap: 8px; }
  .adm-sort-wrap label { font-size: 12px; color: var(--adm-text3); font-weight: 500; }
  .adm-select {
    padding: 7px 28px 7px 12px; border-radius: 8px;
    border: 1px solid var(--adm-border); background: var(--adm-card);
    font-size: 12.5px; font-weight: 600; color: var(--adm-text);
    cursor: pointer; outline: none; font-family: inherit;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%239ba8a3' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center;
  }

  .seller-identity { display: flex; align-items: center; gap: 12px; }
  .seller-thumb {
    width: 44px; height: 44px; border-radius: 10px;
    object-fit: cover; background: var(--adm-green-soft);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; color: var(--adm-green);
    flex-shrink: 0; overflow: hidden;
  }
  .seller-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .seller-name { font-size: 13.5px; font-weight: 700; color: var(--adm-text); }
  .seller-id   { font-size: 11px; color: var(--adm-text3); font-weight: 500; margin-top:2px; }

  .origin-city     { font-size: 13px; font-weight: 600; color: var(--adm-text); }
  .origin-district { font-size: 11.5px; color: var(--adm-text3); margin-top: 2px; }

  .spec-badge {
    padding: 4px 12px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600;
    background: var(--adm-bg); color: var(--adm-text2);
    border: 1px solid var(--adm-border);
    white-space: nowrap;
  }

  .compliance-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 700; letter-spacing: 0.3px;
  }
  .compliance-badge .ico {
    width: 20px; height: 20px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
  }
  .compliance-badge.verified { color: var(--adm-green); }
  .compliance-badge.verified .ico { background: var(--adm-green-soft); color: var(--adm-green); }
  .compliance-badge.pending  { color: var(--adm-orange); }
  .compliance-badge.pending  .ico { background: var(--adm-orange-soft); color: var(--adm-orange); }
  .compliance-badge.rejected { color: var(--adm-red); }
  .compliance-badge.rejected .ico { background: var(--adm-red-soft); color: var(--adm-red); }
  .compliance-badge .material-symbols-outlined { font-size: 13px; font-variation-settings:'FILL' 1,'wght' 500,'GRAD' 0,'opsz' 24; }

  .adm-pagination {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-top: 1px solid var(--adm-border);
    font-size: 12.5px; color: var(--adm-text3);
  }
  .adm-page-btns { display: flex; align-items: center; gap: 4px; }
  .adm-page-btn {
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid var(--adm-border); background: var(--adm-card);
    display: flex; align-items: center; justify-content: center;
    font-size: 12.5px; font-weight: 600; cursor: pointer;
    color: var(--adm-text2); transition: all 0.15s; text-decoration: none;
  }
  .adm-page-btn:hover { border-color: var(--adm-green); color: var(--adm-green); }
  .adm-page-btn.active { background: var(--adm-green); border-color: var(--adm-green); color: #fff; }
  .adm-page-btn .material-symbols-outlined { font-size: 16px; }
</style>
@endsection

@section('content')

  {{-- ── PAGE HEADER ── --}}
  <div class="adm-page-header">
    <div>
      <h1>Gestion des Vendeurs</h1>
      <p>Gérez et vérifiez les artisans et boutiques sur la plateforme BéninMarket.</p>
    </div>
    <div class="adm-header-actions">
      <button class="adm-btn adm-btn-outline" onclick="exportSellersCSV()">
        <span class="material-symbols-outlined">download</span>
        Exporter
      </button>
      <button class="adm-btn adm-btn-primary" onclick="openSellerAddModal()">
        <span class="material-symbols-outlined">person_add</span>
        Ajouter un vendeur
      </button>
    </div>
  </div>

  {{-- MODALE AJOUT VENDEUR --}}
  <div id="seller-add-overlay"
       style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9000;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.2s"
       onclick="if(event.target===this)closeSellerAddModal()">
    <div class="adm-modal-box"
         style="background:#fff;border-radius:16px;padding:32px;max-width:520px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:scale(0.95);transition:transform 0.2s;max-height:90vh;overflow-y:auto">
      <form action="{{ route('admin.sellers.store') }}" method="POST">
        @csrf
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
          <div>
            <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:17px;font-weight:700">Nouveau Vendeur</h2>
            <p style="font-size:13px;color:var(--adm-text3);margin-top:2px">Créer un compte et une boutique</p>
          </div>
          <button type="button" onclick="closeSellerAddModal()" style="background:none;border:none;cursor:pointer;padding:4px;color:var(--adm-text3)">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div style="grid-column:span 2">
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Nom du propriétaire</label>
            <input name="name" type="text" placeholder="Ex: Jean Koffi" required style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Email *</label>
            <input name="email" type="email" placeholder="koffi@example.com" required style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Mot de passe</label>
            <input name="password" type="password" placeholder="••••••••" required style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
          </div>
          <div style="grid-column:span 2;margin-top:4px;padding-top:14px;border-top:1px solid var(--adm-border)">
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Nom de la boutique</label>
            <input name="shop_name" type="text" placeholder="Ex: Tissage d'Or" required style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
          </div>
          <div style="grid-column:span 2">
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Localisation (Ville)</label>
            <input name="location" type="text" placeholder="Ex: Cotonou, Akpakpa" required style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;">
          </div>
          <div style="grid-column:span 2">
            <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Description (optionnel)</label>
            <textarea name="description" rows="2" placeholder="Brève présentation de la boutique..." style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:none;"></textarea>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px">
          <button type="button" onclick="closeSellerAddModal()" class="adm-btn adm-btn-outline adm-btn-sm">Annuler</button>
          <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
            <span class="material-symbols-outlined" style="font-size:15px">store</span>
            Créer le vendeur
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  function openSellerAddModal() {
    const o = document.getElementById('seller-add-overlay');
    o.style.opacity = '1'; o.style.visibility = 'visible';
    o.querySelector('.adm-modal-box').style.transform = 'scale(1)';
  }
  function closeSellerAddModal() {
    const o = document.getElementById('seller-add-overlay');
    o.style.opacity = '0'; o.style.visibility = 'hidden';
    o.querySelector('.adm-modal-box').style.transform = 'scale(0.95)';
  }
  function exportSellersCSV() {
    const table = document.querySelector('.adm-table');
    if (!table) return;
    const rows = table.querySelectorAll('tr');
    const lines = [];
    rows.forEach(row => {
      const cells = [...row.querySelectorAll('th,td')].slice(0,4);
      lines.push(cells.map(c => '"' + (c.innerText||c.textContent).trim().replace(/\s+/g,' ').replace(/"/g,'""') + '"').join(','));
    });
    const blob = new Blob(['\uFEFF' + lines.join('\n')], {type:'text/csv;charset=utf-8;'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = 'vendeurs-blackmaket-' + new Date().toISOString().split('T')[0] + '.csv'; a.click();
    showToast('Export CSV téléchargé !', 'success');
  }
  </script>

  {{-- ── FILTERS ── --}}
  <div class="adm-filter-bar">
    <button class="adm-filter-btn" style="background:none;border:none;padding:7px 2px;color:var(--adm-text2)">
      <span class="material-symbols-outlined" style="font-size:16px;">filter_list</span> Filters
    </button>
    @php
      $filters = [
        'all'     => ['label' => 'All Sellers',      'count' => $shops->total()],
        'approved'=> ['label' => 'Verified Only',    'count' => null],
        'pending' => ['label' => 'Pending Approval', 'count' => null],
        'artisan' => ['label' => 'Artisans',         'count' => null],
        'retail'  => ['label' => 'Retail Shops',     'count' => null],
      ];
      $activeFilter = request('filter', 'all');
    @endphp
    @foreach($filters as $key => $f)
      <a href="{{ route('admin.sellers', ['filter' => $key]) }}"
         class="adm-filter-btn {{ $activeFilter === $key ? 'active' : '' }}">
        {{ $f['label'] }}
      </a>
    @endforeach

    <div class="adm-sort-wrap">
      <label>Sort by:</label>
      <select class="adm-select" onchange="window.location='{{ route('admin.sellers') }}?sort='+this.value">
        <option value="recent">Recently Joined</option>
        <option value="name">Name A–Z</option>
        <option value="status">Status</option>
      </select>
    </div>
  </div>

  {{-- ── TABLE ── --}}
  <div class="adm-card">
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Seller Identity</th>
            <th>Origin &amp; Hub</th>
            <th>Specialization</th>
            <th>Compliance Status</th>
            <th>Administrative Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($shops as $shop)
            @php
              $status = $shop->status ?? 'pending';
              $compMap = [
                'approved' => ['class' => 'verified', 'icon' => 'check_circle', 'label' => 'VERIFIED'],
                'pending'  => ['class' => 'pending',  'icon' => 'pending',      'label' => 'PENDING'],
                'rejected' => ['class' => 'rejected', 'icon' => 'cancel',       'label' => 'REJECTED'],
              ];
              $comp = $compMap[$status] ?? $compMap['pending'];
              $idx  = $loop->index;
              $colors = ['#8B5E3C','#1B6B3A','#D4920A','#2E4A7C','#8B3A3A'];
              $bg = $colors[$idx % count($colors)];
            @endphp
            <tr>
              <td>
                <div class="seller-identity">
                  <div class="seller-thumb" style="background:{{ $bg }}20;color:{{ $bg }}">
                    @if($shop->logo)
                      <img src="{{ $shop->logo }}" alt="{{ $shop->name }}">
                    @else
                      {{ strtoupper(substr($shop->name, 0, 1)) }}
                    @endif
                  </div>
                  <div>
                    <div class="seller-name">{{ $shop->name }}</div>
                    <div class="seller-id">ID: SL-BN-{{ str_pad($shop->id, 7, '2024-0', STR_PAD_LEFT) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="origin-city">{{ $shop->location ?? '—' }}</div>
                <div class="origin-district">{{ $shop->city ?? 'Bénin' }}</div>
              </td>
              <td>
                <span class="spec-badge">{{ $shop->category ?? 'Artisanat' }}</span>
              </td>
              <td>
                <span class="compliance-badge {{ $comp['class'] }}">
                  <span class="ico">
                    <span class="material-symbols-outlined">{{ $comp['icon'] }}</span>
                  </span>
                  {{ $comp['label'] }}
                </span>
              </td>
              <td>
                <div class="adm-actions-cell">
                  <a href="{{ route('admin.sellers.show', $shop->id) }}"
                     class="adm-icon-btn" title="Voir détails">
                    <span class="material-symbols-outlined">visibility</span>
                  </a>
                  <button class="adm-icon-btn" title="Modifier">
                    <span class="material-symbols-outlined">edit</span>
                  </button>
                  @if($status !== 'approved')
                    <form method="POST" action="{{ route('admin.sellers.approve', $shop->id) }}" style="display:inline">
                      @csrf
                      <button class="adm-icon-btn" title="Approuver"
                              style="color:var(--adm-green)">
                        <span class="material-symbols-outlined">check_circle</span>
                      </button>
                    </form>
                  @else
                    <button class="adm-icon-btn" title="Suspendre" style="color:var(--adm-red)">
                      <span class="material-symbols-outlined">block</span>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center;padding:40px;color:var(--adm-text3)">
                <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;opacity:0.4">storefront</span>
                Aucune boutique enregistrée.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="adm-pagination">
      <span>
        Showing {{ $shops->firstItem() }} to {{ $shops->lastItem() }} of
        {{ number_format($shops->total()) }} registered sellers
      </span>
      <div class="adm-page-btns">
        @if($shops->onFirstPage())
          <span class="adm-page-btn" style="opacity:0.4;cursor:not-allowed">
            <span class="material-symbols-outlined">chevron_left</span>
          </span>
        @else
          <a href="{{ $shops->previousPageUrl() }}" class="adm-page-btn">
            <span class="material-symbols-outlined">chevron_left</span>
          </a>
        @endif

        @foreach(range(1, min($shops->lastPage(), 5)) as $p)
          <a href="{{ $shops->url($p) }}"
             class="adm-page-btn {{ $shops->currentPage() === $p ? 'active' : '' }}">
            {{ $p }}
          </a>
        @endforeach

        @if($shops->hasMorePages())
          <a href="{{ $shops->nextPageUrl() }}" class="adm-page-btn">
            <span class="material-symbols-outlined">chevron_right</span>
          </a>
        @endif
      </div>
    </div>
  </div>

@endsection
