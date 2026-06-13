@extends('admin.layouts.app')

@section('title', 'Global Catalog')

@section('styles')
<style>
  /* ── PRODUCTS PAGE ── */
  .adm-mini-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
    width: 100%;
  }
  .adm-mini-stat {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius);
    padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    transition: box-shadow 0.15s;
    animation: fadeInUp 0.3s ease both;
  }
  .adm-mini-stat:hover { box-shadow: var(--adm-shadow-md); }
  .adm-mini-stat-icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .adm-mini-stat-icon .material-symbols-outlined {
    font-size: 20px;
    font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
  }
  .adm-mini-stat-icon.green  { background:var(--adm-green-soft); color:var(--adm-green); }
  .adm-mini-stat-icon.orange { background:var(--adm-orange-soft); color:var(--adm-orange); }
  .adm-mini-stat-icon.red    { background:var(--adm-red-soft); color:var(--adm-red); }
  .adm-mini-stat-icon.gold   { background:var(--adm-gold-soft); color:var(--adm-gold); }
  .adm-mini-stat-label { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); margin-bottom:2px; }
  .adm-mini-stat-val   { font-family:'Plus Jakarta Sans',sans-serif; font-size:22px; font-weight:800; }

  /* Filter tabs */
  .adm-tabs { display:flex; align-items:center; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
  .adm-tab {
    padding:7px 16px; border-radius:20px;
    font-size:12.5px; font-weight:600;
    border:1px solid var(--adm-border);
    background:var(--adm-card); color:var(--adm-text2);
    cursor:pointer; transition:all 0.15s; text-decoration:none;
  }
  .adm-tab:hover { border-color:var(--adm-green); color:var(--adm-green); }
  .adm-tab.active { background:var(--adm-green); border-color:var(--adm-green); color:#fff; }
  .adm-tab-count { margin-left:auto; font-size:12px; color:var(--adm-text3); }

  /* Product cell */
  .prod-cell { display:flex; align-items:center; gap:12px; }
  .prod-thumb {
    width:44px; height:44px; border-radius:8px;
    object-fit:cover; background:var(--adm-bg);
    flex-shrink:0; border:1px solid var(--adm-border);
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
  }
  .prod-thumb img { width:100%; height:100%; object-fit:cover; }
  .prod-thumb .material-symbols-outlined { font-size:22px; color:var(--adm-text3); font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24; }
  .prod-name { font-size:13.5px; font-weight:700; color:var(--adm-text); }
  .prod-sku  { font-size:11px; color:var(--adm-text3); margin-top:2px; }

  .seller-cell { display:flex; align-items:center; gap:8px; }
  .seller-mini-av { width:26px; height:26px; border-radius:50%; overflow:hidden; background:var(--adm-green-soft); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--adm-green); flex-shrink:0; }
  .seller-mini-av img { width:100%; height:100%; object-fit:cover; }

  .price-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:13.5px; font-weight:700; color:var(--adm-green); }
  .price-unit { font-size:11px; color:var(--adm-text3); }

  .cat-tag { padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; background:var(--adm-bg); color:var(--adm-text2); border:1px solid var(--adm-border); white-space:nowrap; }

  .stock-cell { font-size:13px; font-weight:600; }
  .stock-low  { color:var(--adm-orange); }
  .stock-ok   { color:var(--adm-text); }

  /* Status badges */
  .status-approved    { background:var(--adm-green-soft); color:var(--adm-green); }
  .status-under_review{ background:var(--adm-orange-soft); color:var(--adm-orange); }
  .status-flagged     { background:var(--adm-red-soft); color:var(--adm-red); }
  .status-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:6px;
    font-size:11.5px; font-weight:700;
  }
  .status-badge .material-symbols-outlined { font-size:13px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

  /* Action buttons for products */
  .prod-actions { display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
  .act-btn {
    padding:4px 10px; border-radius:6px; font-size:11.5px; font-weight:600;
    border:1px solid var(--adm-border); background:var(--adm-card);
    color:var(--adm-text2); cursor:pointer; font-family:inherit; transition:all 0.15s;
  }
  .act-btn.approve { background:var(--adm-green-soft); color:var(--adm-green); border-color:var(--adm-green-soft); }
  .act-btn.reject  { background:var(--adm-red-soft); color:var(--adm-red); border-color:var(--adm-red-soft); }
  .act-btn.appeal  { background:var(--adm-orange-soft); color:var(--adm-orange); border-color:var(--adm-orange-soft); font-size:11px; }
  .act-btn:hover { filter:brightness(0.95); }

  @media(max-width:900px) {
    .adm-mini-stats { grid-template-columns:repeat(2,1fr); }
  }
</style>
@endsection

@section('content')

  {{-- ── PAGE HEADER ── --}}
  <div class="adm-page-header">
    <div>
      <h1>Catalogue Produits</h1>
      <p>Modérez et gérez tous les produits listés sur CauriMarket.</p>
    </div>
    <div class="adm-header-actions">
      <button class="adm-btn adm-btn-outline" onclick="toggleFiltersPanel()">
        <span class="material-symbols-outlined">tune</span>
        Filtres avancés
      </button>
      <button class="adm-btn adm-btn-primary" onclick="openNewListingModal()">
        <span class="material-symbols-outlined">add</span>
        Nouveau produit
      </button>
    </div>
  </div>

  {{-- ── PANNEAU FILTRES AVANCÉS ── --}}
  <div id="filters-panel" style="display:none;background:var(--adm-card);border:1px solid var(--adm-border);border-radius:12px;padding:20px;margin-bottom:20px;animation:fadeInUp 0.2s ease">
    <form method="GET" action="{{ route('admin.products') }}">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:16px">
        <div>
          <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Statut</label>
          <select name="status" style="width:100%;padding:8px 12px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--adm-bg)">
            <option value="">Tous</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvés</option>
            <option value="pending"  {{ request('status')=='pending'?'selected':'' }}>En attente</option>
            <option value="flagged"  {{ request('status')=='flagged'?'selected':'' }}>Signalés</option>
          </select>
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Prix min (CFA)</label>
          <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" style="width:100%;padding:8px 12px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--adm-bg)">
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Prix max (CFA)</label>
          <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="999999" style="width:100%;padding:8px 12px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--adm-bg)">
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Recherche</label>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du produit..." style="width:100%;padding:8px 12px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--adm-bg)">
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('admin.products') }}" class="adm-btn adm-btn-outline adm-btn-sm">Réinitialiser</a>
        <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
          <span class="material-symbols-outlined" style="font-size:15px">search</span>
          Appliquer
        </button>
      </div>
    </form>
  </div>

  {{-- ── MODALE NOUVEAU PRODUIT ── --}}
  <div id="new-listing-overlay"
       style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9000;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.2s"
       onclick="if(event.target===this)closeNewListingModal()">
    <div class="adm-modal-box"
         style="background:#fff;border-radius:16px;padding:32px;max-width:520px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,0.2);transform:scale(0.95);transition:transform 0.2s;max-height:90vh;overflow-y:auto">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
          <h2 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:17px;font-weight:700">Ajouter un produit</h2>
          <p style="font-size:13px;color:var(--adm-text3);margin-top:2px">Créer une nouvelle fiche produit dans le catalogue</p>
        </div>
        <button onclick="closeNewListingModal()" style="background:none;border:none;cursor:pointer;padding:4px;color:var(--adm-text3)">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      <div style="background:var(--adm-green-soft);border-radius:10px;padding:14px 16px;margin-bottom:16px;font-size:13px;color:var(--adm-green);display:flex;gap:10px;align-items:flex-start">
        <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">info</span>
        <span>Les vendeurs peuvent ajouter leurs propres produits depuis leur compte. Cette fonctionnalité d'ajout direct par l'admin sera disponible dans la prochaine version.</span>
      </div>
      <div style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Nom du produit *</label>
          <input type="text" placeholder="Ex: Masque Gèlèdè en bois" style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='var(--adm-green)'" onblur="this.style.borderColor='var(--adm-border)'">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Prix (CFA)</label>
            <input type="number" placeholder="5000" style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='var(--adm-green)'" onblur="this.style.borderColor='var(--adm-border)'">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Stock</label>
            <input type="number" placeholder="10" style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='var(--adm-green)'" onblur="this.style.borderColor='var(--adm-border)'">
          </div>
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--adm-text3);margin-bottom:6px">Description</label>
          <textarea rows="3" placeholder="Description du produit..." style="width:100%;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:vertical" onfocus="this.style.borderColor='var(--adm-green)'" onblur="this.style.borderColor='var(--adm-border)'"></textarea>
        </div>
      </div>
      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px">
        <button onclick="closeNewListingModal()" class="adm-btn adm-btn-outline adm-btn-sm">Annuler</button>
        <button onclick="showToast('Fonctionnalité en cours de développement.','info');closeNewListingModal()" class="adm-btn adm-btn-primary adm-btn-sm">
          <span class="material-symbols-outlined" style="font-size:15px">save</span>
          Enregistrer
        </button>
      </div>
    </div>
  </div>

  <script>
  function toggleFiltersPanel() {
    const p = document.getElementById('filters-panel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
  }
  function openNewListingModal() {
    const o = document.getElementById('new-listing-overlay');
    o.style.opacity='1'; o.style.visibility='visible';
    o.querySelector('.adm-modal-box').style.transform='scale(1)';
  }
  function closeNewListingModal() {
    const o = document.getElementById('new-listing-overlay');
    o.style.opacity='0'; o.style.visibility='hidden';
    o.querySelector('.adm-modal-box').style.transform='scale(0.95)';
  }
  </script>

  {{-- ── MINI STAT CARDS ── --}}
  <div class="adm-mini-stats">
    <div class="adm-mini-stat">
      <div class="adm-mini-stat-icon green">
        <span class="material-symbols-outlined">inventory_2</span>
      </div>
      <div>
        <div class="adm-mini-stat-label">Total Items</div>
        <div class="adm-mini-stat-val">{{ number_format($miniStats['total']) }}</div>
      </div>
    </div>
    <div class="adm-mini-stat">
      <div class="adm-mini-stat-icon orange">
        <span class="material-symbols-outlined">rate_review</span>
      </div>
      <div>
        <div class="adm-mini-stat-label">Under Review</div>
        <div class="adm-mini-stat-val">{{ number_format($miniStats['under_review']) }}</div>
      </div>
    </div>
    <div class="adm-mini-stat">
      <div class="adm-mini-stat-icon red">
        <span class="material-symbols-outlined">flag</span>
      </div>
      <div>
        <div class="adm-mini-stat-label">Flagged</div>
        <div class="adm-mini-stat-val">{{ number_format($miniStats['flagged']) }}</div>
      </div>
    </div>
    <div class="adm-mini-stat">
      <div class="adm-mini-stat-icon gold">
        <span class="material-symbols-outlined">trending_up</span>
      </div>
      <div>
        <div class="adm-mini-stat-label">Top Category</div>
        <div class="adm-mini-stat-val" style="font-size:16px">{{ $miniStats['top_category'] ?? '—' }}</div>
      </div>
    </div>
  </div>

  {{-- ── TABS + COUNT ── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div class="adm-tabs">
      @php $activeTab = request('tab', 'all'); @endphp
      <a href="{{ route('admin.products', ['tab'=>'all']) }}"
         class="adm-tab {{ $activeTab==='all' ? 'active' : '' }}">
        All Products
      </a>
      <a href="{{ route('admin.products', ['tab'=>'pending']) }}"
         class="adm-tab {{ $activeTab==='pending' ? 'active' : '' }}">
        Pending Review
      </a>
      <a href="{{ route('admin.products', ['tab'=>'flagged']) }}"
         class="adm-tab {{ $activeTab==='flagged' ? 'active' : '' }}">
        Flagged Items
      </a>
    </div>
    <span style="font-size:12.5px;color:var(--adm-text3);">
      Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
      of {{ number_format($products->total()) }} products
    </span>
  </div>

  {{-- ── TABLE ── --}}
  <div class="adm-card">
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            @php
              $pStatus = $product->status ?? 'approved';
              $statusMap = [
                'approved'     => ['class'=>'status-approved',     'icon'=>'check_circle', 'label'=>'Approved'],
                'pending'      => ['class'=>'status-under_review', 'icon'=>'pending',      'label'=>'Under Review'],
                'under_review' => ['class'=>'status-under_review', 'icon'=>'pending',      'label'=>'Under Review'],
                'flagged'      => ['class'=>'status-flagged',      'icon'=>'flag',         'label'=>'Flagged'],
              ];
              $st = $statusMap[$pStatus] ?? $statusMap['approved'];
              $stock = $product->stock ?? $product->quantity ?? 0;
              $isLowStock = $stock > 0 && $stock <= 5;
            @endphp
            <tr>
              <td>
                <div class="prod-cell">
                  <div class="prod-thumb">
                    @if($product->image)
                      <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    @else
                      <span class="material-symbols-outlined">image</span>
                    @endif
                  </div>
                  <div>
                    <div class="prod-name">{{ $product->name }}</div>
                    <div class="prod-sku">SKU: BEN-{{ strtoupper(substr($product->name, 0, 2)) }}-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="seller-cell">
                  <div class="seller-mini-av">
                    @if($product->shop && $product->shop->logo)
                      <img src="{{ $product->shop->logo }}" alt="">
                    @else
                      {{ strtoupper(substr($product->shop->name ?? 'S', 0, 1)) }}
                    @endif
                  </div>
                  <span style="font-size:13px;font-weight:500;color:var(--adm-text2)">
                    {{ $product->shop->name ?? '—' }}
                  </span>
                </div>
              </td>
              <td>
                <div class="price-val">{{ number_format($product->price, 0, ',', '.') }}</div>
                <div class="price-unit">CFA</div>
              </td>
              <td>
                <span class="cat-tag">{{ $product->category->name ?? 'Divers' }}</span>
              </td>
              <td>
                <span class="stock-cell {{ $isLowStock ? 'stock-low' : 'stock-ok' }}">
                  @if($isLowStock)
                    <span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">warning</span>
                  @else
                    <span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle;color:var(--adm-green);font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">circle</span>
                  @endif
                  {{ $stock }} {{ $stock <= 1 ? 'unit' : 'units' }}
                </span>
              </td>
              <td>
                <span class="status-badge {{ $st['class'] }}">
                  <span class="material-symbols-outlined">{{ $st['icon'] }}</span>
                  {{ $st['label'] }}
                </span>
              </td>
              <td>
                <div class="prod-actions">
                  @if($pStatus === 'pending' || $pStatus === 'under_review')
                    <form method="POST"
                          action="{{ route('admin.products.approve', $product->id) }}">
                      @csrf
                      <button class="act-btn approve" type="submit">Approve</button>
                    </form>
                    <form method="POST"
                          action="{{ route('admin.products.reject', $product->id) }}">
                      @csrf
                      <button class="act-btn reject" type="submit">Reject</button>
                    </form>
                  @elseif($pStatus === 'flagged')
                    <form method="POST"
                          action="{{ route('admin.products.approve', $product->id) }}">
                      @csrf
                      <button class="act-btn approve" type="submit">Review Appeal</button>
                    </form>
                  @elseif($pStatus === 'publié' || $pStatus === 'approved')
                    <form method="POST"
                          action="{{ route('admin.products.flag', $product->id) }}">
                      @csrf
                      <button class="act-btn appeal" type="submit">Signaler</button>
                    </form>
                  @endif
                  <button class="adm-icon-btn" title="Message vendeur">
                    <span class="material-symbols-outlined">mail</span>
                  </button>
                  <form method="POST"
                        action="{{ route('admin.products.destroy', $product->id) }}"
                        onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                    @csrf @method('DELETE')
                    <button class="adm-icon-btn" title="Supprimer"
                            style="color:var(--adm-red)">
                      <span class="material-symbols-outlined">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:40px;color:var(--adm-text3)">
                <span class="material-symbols-outlined" style="font-size:40px;display:block;margin-bottom:8px;opacity:0.4">inventory_2</span>
                Aucun produit trouvé.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
      <div class="adm-pagination">
        <span>
          Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
          of {{ number_format($products->total()) }} products
        </span>
        <div class="adm-page-btns">
          @if($products->onFirstPage())
            <span class="adm-page-btn" style="opacity:0.4;cursor:not-allowed">
              <span class="material-symbols-outlined">chevron_left</span>
            </span>
          @else
            <a href="{{ $products->previousPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif

          @foreach(range(1, min($products->lastPage(), 5)) as $p)
            <a href="{{ $products->url($p) }}"
               class="adm-page-btn {{ $products->currentPage() === $p ? 'active' : '' }}">
              {{ $p }}
            </a>
          @endforeach

          @if($products->lastPage() > 5)
            <span class="adm-page-btn" style="border:none;background:none">…</span>
            <a href="{{ $products->url($products->lastPage()) }}" class="adm-page-btn">
              {{ $products->lastPage() }}
            </a>
          @endif

          @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          @endif
        </div>
      </div>
    @endif
  </div>

@endsection
