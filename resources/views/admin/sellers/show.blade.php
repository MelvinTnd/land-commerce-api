@extends('admin.layouts.app')

@section('title', 'Fiche Vendeur')

@section('styles')
<style>
  .seller-profile { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,2fr); gap:20px; }
  .profile-card { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); overflow:hidden; }

  .profile-banner {
    height:100px;
    background: linear-gradient(135deg,var(--adm-green) 0%,var(--adm-green-mid) 100%);
    position:relative;
  }
  .profile-avatar {
    width:72px; height:72px; border-radius:14px;
    background:#fff; border:3px solid #fff;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; font-weight:800; color:var(--adm-green);
    position:absolute; bottom:-30px; left:20px;
    box-shadow:0 4px 14px rgba(0,0,0,0.15);
    overflow:hidden;
  }
  .profile-avatar img { width:100%; height:100%; object-fit:cover; }
  .profile-body { padding:44px 20px 20px; }
  .profile-name { font-family:'Plus Jakarta Sans',sans-serif; font-size:18px; font-weight:800; }
  .profile-sub  { font-size:12.5px; color:var(--adm-text3); margin-top:3px; }

  .profile-meta { display:flex; flex-direction:column; gap:10px; margin-top:16px; }
  .profile-meta-row { display:flex; align-items:center; gap:8px; font-size:13px; }
  .profile-meta-row .material-symbols-outlined { font-size:16px; color:var(--adm-text3); width:20px; flex-shrink:0; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
  .profile-meta-label { color:var(--adm-text3); font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; display:block; }

  .status-chip {
    display:inline-flex; align-items:center; gap:6px;
    padding:5px 12px; border-radius:8px;
    font-size:12.5px; font-weight:700;
    margin-top:14px;
  }
  .status-chip.pending  { background:var(--adm-orange-soft); color:var(--adm-orange); }
  .status-chip.approved { background:var(--adm-green-soft); color:var(--adm-green); }
  .status-chip.rejected { background:var(--adm-red-soft); color:var(--adm-red); }
  .status-chip .material-symbols-outlined { font-size:14px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

  .profile-actions { display:flex; gap:8px; margin-top:18px; flex-wrap:wrap; }
  .profile-actions form { display:inline; }

  .prod-mini-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(140px,1fr));
    gap:12px; padding:20px;
  }
  .prod-mini-card {
    border:1px solid var(--adm-border); border-radius:8px;
    overflow:hidden; transition:box-shadow 0.15s;
  }
  .prod-mini-card:hover { box-shadow:var(--adm-shadow-md); }
  .prod-mini-img { height:90px; background:var(--adm-bg); display:flex; align-items:center; justify-content:center; overflow:hidden; }
  .prod-mini-img img { width:100%; height:100%; object-fit:cover; }
  .prod-mini-img .material-symbols-outlined { font-size:32px; color:var(--adm-text3); opacity:0.4; font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24; }
  .prod-mini-body { padding:8px 10px; }
  .prod-mini-name { font-size:12px; font-weight:600; color:var(--adm-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .prod-mini-price { font-size:12px; font-weight:700; color:var(--adm-green); margin-top:2px; }

  .stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding:16px 20px; border-bottom:1px solid var(--adm-border); }
  .stat-row-item { text-align:center; }
  .stat-row-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:22px; font-weight:800; }
  .stat-row-lbl { font-size:11px; color:var(--adm-text3); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-top:2px; }

  @media(max-width:900px) {
    .seller-profile { grid-template-columns:1fr; }
  }
</style>
@endsection

@section('content')

  {{-- Back button --}}
  <div style="margin-bottom:20px">
    <a href="{{ route('admin.sellers') }}" class="adm-btn adm-btn-outline adm-btn-sm">
      <span class="material-symbols-outlined">arrow_back</span>
      Retour aux vendeurs
    </a>
  </div>

  <div class="seller-profile">

    {{-- ── LEFT : Profile Card ── --}}
    <div style="display:flex;flex-direction:column;gap:16px">

      <div class="profile-card">
        <div class="profile-banner">
          <div class="profile-avatar">
            @if($shop->logo)
              <img src="{{ $shop->logo }}" alt="{{ $shop->name }}">
            @else
              {{ strtoupper(substr($shop->name, 0, 1)) }}
            @endif
          </div>
        </div>
        <div class="profile-body">
          <div class="profile-name">{{ $shop->name }}</div>
          <div class="profile-sub">ID: SL-BN-{{ str_pad($shop->id, 7, '2024-0', STR_PAD_LEFT) }}</div>

          @php
            $status = $shop->status ?? 'pending';
            $chipMap = [
              'approved' => ['class'=>'approved','icon'=>'check_circle','label'=>'Vendeur Vérifié'],
              'pending'  => ['class'=>'pending', 'icon'=>'pending',     'label'=>'En attente de vérification'],
              'rejected' => ['class'=>'rejected','icon'=>'cancel',      'label'=>'Rejeté'],
            ];
            $chip = $chipMap[$status] ?? $chipMap['pending'];
          @endphp
          <span class="status-chip {{ $chip['class'] }}">
            <span class="material-symbols-outlined">{{ $chip['icon'] }}</span>
            {{ $chip['label'] }}
          </span>

          <div class="profile-meta">
            <div class="profile-meta-row">
              <span class="material-symbols-outlined">location_on</span>
              <div>
                <span class="profile-meta-label">Localisation</span>
                {{ $shop->location ?? 'Non renseignée' }}{{ $shop->city ? ', '.$shop->city : '' }}
              </div>
            </div>
            <div class="profile-meta-row">
              <span class="material-symbols-outlined">person</span>
              <div>
                <span class="profile-meta-label">Propriétaire</span>
                {{ $shop->user->name ?? 'N/A' }}
              </div>
            </div>
            <div class="profile-meta-row">
              <span class="material-symbols-outlined">mail</span>
              <div>
                <span class="profile-meta-label">Email</span>
                {{ $shop->user->email ?? 'N/A' }}
              </div>
            </div>
            <div class="profile-meta-row">
              <span class="material-symbols-outlined">calendar_today</span>
              <div>
                <span class="profile-meta-label">Inscrit le</span>
                {{ $shop->created_at->format('d M Y') }}
              </div>
            </div>
            @if($shop->description)
              <div class="profile-meta-row" style="align-items:flex-start">
                <span class="material-symbols-outlined">info</span>
                <div>
                  <span class="profile-meta-label">Description</span>
                  <p style="font-size:12.5px;color:var(--adm-text2);line-height:1.5;margin-top:3px">
                    {{ \Illuminate\Support\Str::limit($shop->description, 150) }}
                  </p>
                </div>
              </div>
            @endif
          </div>

          {{-- Admin Actions --}}
          <div class="profile-actions">
            @if($status !== 'approved')
              <form method="POST" action="{{ route('admin.sellers.approve', $shop->id) }}">
                @csrf
                <button class="adm-btn adm-btn-primary adm-btn-sm">
                  <span class="material-symbols-outlined">check_circle</span>
                  Approuver
                </button>
              </form>
            @endif
            @if($status !== 'rejected')
              <form method="POST" action="{{ route('admin.sellers.reject', $shop->id) }}">
                @csrf
                <button class="adm-btn adm-btn-danger adm-btn-sm">
                  <span class="material-symbols-outlined">cancel</span>
                  Rejeter
                </button>
              </form>
            @endif
            <button class="adm-btn adm-btn-outline adm-btn-sm">
              <span class="material-symbols-outlined">mail</span>
              Contacter
            </button>
          </div>
        </div>
      </div>

      {{-- Stats rapides --}}
      <div class="profile-card">
        <div class="adm-card-header">
          <h2>Statistiques</h2>
        </div>
        <div class="stat-row">
          <div class="stat-row-item">
            <div class="stat-row-val">{{ $shop->products ? $shop->products->count() : 0 }}</div>
            <div class="stat-row-lbl">Produits</div>
          </div>
          <div class="stat-row-item">
            <div class="stat-row-val">
              {{ $shop->products ? number_format($shop->products->sum('stock')) : 0 }}
            </div>
            <div class="stat-row-lbl">Stock total</div>
          </div>
          <div class="stat-row-item">
            <div class="stat-row-val">
              {{ $shop->products ? number_format($shop->products->sum('price'), 0, ',', '.') : 0 }}
            </div>
            <div class="stat-row-lbl">Valeur CFA</div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── RIGHT : Products ── --}}
    <div class="adm-card">
      <div class="adm-card-header">
        <div>
          <h2>Catalogue de {{ $shop->name }}</h2>
          <p>{{ $shop->products ? $shop->products->count() : 0 }} produit(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.products') }}" class="adm-card-link">
          Voir tout
          <span class="material-symbols-outlined">chevron_right</span>
        </a>
      </div>

      @if($shop->products && $shop->products->count() > 0)
        <div class="prod-mini-grid">
          @foreach($shop->products->take(12) as $product)
            <div class="prod-mini-card">
              <div class="prod-mini-img">
                @if($product->image)
                  <img src="{{ $product->image }}" alt="{{ $product->name }}">
                @else
                  <span class="material-symbols-outlined">image</span>
                @endif
              </div>
              <div class="prod-mini-body">
                <div class="prod-mini-name" title="{{ $product->name }}">{{ $product->name }}</div>
                <div class="prod-mini-price">{{ number_format($product->price, 0, ',', '.') }} CFA</div>
              </div>
            </div>
          @endforeach
        </div>
        @if($shop->products->count() > 12)
          <div style="padding:12px 20px;border-top:1px solid var(--adm-border);text-align:center">
            <a href="{{ route('admin.products') }}" class="adm-card-link" style="justify-content:center">
              Voir {{ $shop->products->count() - 12 }} produits de plus
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          </div>
        @endif
      @else
        <div style="padding:40px;text-align:center;color:var(--adm-text3)">
          <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:8px;opacity:0.3;font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24">inventory_2</span>
          Ce vendeur n'a pas encore de produits.
        </div>
      @endif
    </div>

  </div>{{-- /seller-profile --}}

@endsection
