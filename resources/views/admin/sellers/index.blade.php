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
      <h1>Seller Management</h1>
      <p>Oversee and verify artisans and commercial shops across the Republic of Benin.<br>
         Maintain marketplace integrity through proactive vetting.</p>
    </div>
    <div class="adm-header-actions">
      <button class="adm-btn adm-btn-outline">
        <span class="material-symbols-outlined">download</span>
        Export Registry
      </button>
      <button class="adm-btn adm-btn-primary">
        <span class="material-symbols-outlined">add</span>
        Invite New Seller
      </button>
    </div>
  </div>

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
