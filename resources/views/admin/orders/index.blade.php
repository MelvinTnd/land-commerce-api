@extends('admin.layouts.app')

@section('title', 'Gestion Commandes')

@section('styles')
<style>
  .orders-stats { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:12px; margin-bottom:24px; }
  .order-stat { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); padding:16px; display:flex; align-items:center; gap:12px; animation:fadeInUp 0.3s ease both; transition:box-shadow 0.15s; }
  .order-stat:hover { box-shadow:var(--adm-shadow-md); }
  .order-stat-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .order-stat-icon .material-symbols-outlined { font-size:20px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
  .order-stat-lbl { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--adm-text3); }
  .order-stat-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:20px; font-weight:800; margin-top:2px; line-height:1; }

  .status-tab-bar { display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
  .s-tab { padding:6px 14px; border-radius:20px; font-size:12.5px; font-weight:600; border:1px solid var(--adm-border); background:var(--adm-card); color:var(--adm-text2); cursor:pointer; text-decoration:none; transition:all 0.15s; white-space:nowrap; }
  .s-tab:hover  { border-color:var(--adm-green); color:var(--adm-green); }
  .s-tab.active { background:var(--adm-green); border-color:var(--adm-green); color:#fff; }
  .s-tab-badge  { display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; font-size:10px; font-weight:700; background:rgba(255,255,255,0.25); margin-left:4px; }
  .s-tab:not(.active) .s-tab-badge { background:var(--adm-bg); color:var(--adm-text3); }

  .order-amount { font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px; }

  .order-status-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; white-space:nowrap; }
  .order-status-pill .dot { width:6px; height:6px; border-radius:50%; }
  .status-pending    { background:#fff8e6; color:#b45309; } .status-pending .dot    { background:#d97706; }
  .status-processing { background:#eff6ff; color:#1d4ed8; } .status-processing .dot { background:#3b82f6; }
  .status-shipped    { background:#f0fdf4; color:var(--adm-green); } .status-shipped .dot   { background:var(--adm-green); }
  .status-delivered  { background:var(--adm-green-soft); color:var(--adm-green); } .status-delivered .dot  { background:var(--adm-green); }
  .status-cancelled  { background:var(--adm-red-soft); color:var(--adm-red); } .status-cancelled .dot  { background:var(--adm-red); }

  .quick-status-form { display:inline-flex; align-items:center; gap:6px; }
  .quick-status-select { font-size:12px; font-weight:600; border:1px solid var(--adm-border); border-radius:6px; padding:4px 8px; background:var(--adm-bg); color:var(--adm-text); font-family:inherit; outline:none; cursor:pointer; }
  .quick-status-btn { width:26px; height:26px; border-radius:6px; background:var(--adm-green); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0; transition:background 0.15s; }
  .quick-status-btn:hover { background:var(--adm-green-dark); }
  .quick-status-btn .material-symbols-outlined { font-size:14px; font-variation-settings:'FILL' 1,'wght' 500,'GRAD' 0,'opsz' 24; }

  .search-row { display:flex; gap:10px; margin-bottom:16px; }
  .search-row input { flex:1; max-width:300px; padding:8px 14px; border:1px solid var(--adm-border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; background:var(--adm-card); }
  .search-row input:focus { border-color:var(--adm-green); }

  @media(max-width:900px){ .orders-stats { grid-template-columns:repeat(2,1fr); } }
</style>
@endsection

@section('content')

  <div class="adm-page-header">
    <div>
      <h1>Gestion des Commandes</h1>
      <p>Suivez et gérez toutes les commandes passées sur la plateforme BéninMarket.</p>
    </div>
    <button class="adm-btn adm-btn-outline" onclick="exportTable()">
      <span class="material-symbols-outlined">download</span>
      Exporter CSV
    </button>
  </div>

  {{-- STATS --}}
  <div class="orders-stats">
    @php
      $statItems = [
        ['label'=>'Total','val'=>$stats['total'],   'icon'=>'receipt_long','bg'=>'background:#e8f5ed;color:var(--adm-green)'],
        ['label'=>'En attente','val'=>$stats['pending'],'icon'=>'hourglass_empty','bg'=>'background:#fff8e6;color:#b45309'],
        ['label'=>'En traitement','val'=>$stats['processing'],'icon'=>'local_shipping','bg'=>'background:#eff6ff;color:#1d4ed8'],
        ['label'=>'Livrées','val'=>$stats['delivered'], 'icon'=>'check_circle','bg'=>'background:var(--adm-green-soft);color:var(--adm-green)'],
        ['label'=>'CA Livré','val'=>number_format($stats['revenue'],0,',',' ').' CFA','icon'=>'payments','bg'=>'background:var(--adm-gold-soft);color:var(--adm-gold)'],
      ];
    @endphp
    @foreach($statItems as $i => $si)
      <div class="order-stat" style="animation-delay:{{ $i*0.06 }}s">
        <div class="order-stat-icon" style="{{ $si['bg'] }}">
          <span class="material-symbols-outlined">{{ $si['icon'] }}</span>
        </div>
        <div>
          <div class="order-stat-lbl">{{ $si['label'] }}</div>
          <div class="order-stat-val">{{ $si['val'] }}</div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- STATUS TABS --}}
  <div class="status-tab-bar">
    @foreach([''=>'Toutes','pending'=>'En attente','processing'=>'En traitement','shipped'=>'Expédiées','delivered'=>'Livrées','cancelled'=>'Annulées'] as $val => $label)
      <a href="{{ route('admin.orders', $val ? ['status'=>$val] : []) }}"
         class="s-tab {{ request('status')===$val ? 'active' : '' }}">
        {{ $label }}
        @if($val === 'pending' && $stats['pending'] > 0)
          <span class="s-tab-badge">{{ $stats['pending'] }}</span>
        @endif
      </a>
    @endforeach
  </div>

  {{-- SEARCH --}}
  <form method="GET" action="{{ route('admin.orders') }}" class="search-row">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="text" name="search" placeholder="N° commande, client..."
           value="{{ request('search') }}">
    <button class="adm-btn adm-btn-outline adm-btn-sm">
      <span class="material-symbols-outlined">search</span>
      Chercher
    </button>
  </form>

  {{-- TABLE --}}
  <div class="adm-card">
    <div class="adm-table-wrap">
      <table class="adm-table" id="orders-table">
        <thead>
          <tr>
            <th>N° Commande</th>
            <th>Client</th>
            <th>Articles</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Changer statut</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:13px">
                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
              </td>
              <td>
                <div style="font-size:13.5px;font-weight:600">{{ $order->user->name ?? 'Client supprimé' }}</div>
                <div style="font-size:11.5px;color:var(--adm-text3)">{{ $order->user->email ?? '' }}</div>
              </td>
              <td style="font-size:13px;color:var(--adm-text2)">
                {{ $order->items ? $order->items->count() : 0 }} article(s)
              </td>
              <td>
                <span class="order-amount">{{ number_format($order->total ?? 0, 0, ',', '.') }} CFA</span>
              </td>
              <td>
                @php
                  $st = $order->status ?? 'pending';
                  $stLabels = ['pending'=>'En attente','processing'=>'En traitement','shipped'=>'Expédiée','delivered'=>'Livrée','cancelled'=>'Annulée'];
                @endphp
                <span class="order-status-pill status-{{ $st }}">
                  <span class="dot"></span>
                  {{ $stLabels[$st] ?? $st }}
                </span>
              </td>
              <td style="font-size:12.5px;color:var(--adm-text3);white-space:nowrap">
                {{ $order->created_at->format('d M Y') }}
              </td>
              <td>
                <form method="POST"
                      action="{{ route('admin.orders.status', $order->id) }}"
                      class="quick-status-form">
                  @csrf @method('PATCH')
                  <select name="status" class="quick-status-select">
                    @foreach(['pending'=>'En attente','processing'=>'En traitement','shipped'=>'Expédiée','delivered'=>'Livrée','cancelled'=>'Annulée'] as $v => $l)
                      <option value="{{ $v }}" {{ $order->status===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="quick-status-btn" title="Appliquer">
                    <span class="material-symbols-outlined">check</span>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:48px;color:var(--adm-text3)">
                <span class="material-symbols-outlined" style="font-size:48px;display:block;margin-bottom:10px;opacity:0.3">receipt_long</span>
                Aucune commande trouvée.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($orders->hasPages())
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid var(--adm-border);font-size:12.5px;color:var(--adm-text3)">
        <span>{{ $orders->firstItem() }}–{{ $orders->lastItem() }} sur {{ number_format($orders->total()) }} commandes</span>
        <div style="display:flex;gap:4px">
          @if(!$orders->onFirstPage())
            <a href="{{ $orders->previousPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif
          @foreach(range(1, min($orders->lastPage(),5)) as $p)
            <a href="{{ $orders->url($p) }}"
               class="adm-page-btn {{ $orders->currentPage()===$p?'active':'' }}">{{ $p }}</a>
          @endforeach
          @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          @endif
        </div>
      </div>
    @endif
  </div>

  <style>
    .adm-page-btn{width:32px;height:32px;border-radius:8px;border:1px solid var(--adm-border);background:var(--adm-card);display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:600;cursor:pointer;color:var(--adm-text2);transition:all 0.15s;text-decoration:none}
    .adm-page-btn:hover{border-color:var(--adm-green);color:var(--adm-green)}
    .adm-page-btn.active{background:var(--adm-green);border-color:var(--adm-green);color:#fff}
    .adm-page-btn .material-symbols-outlined{font-size:16px}
  </style>

  <script>
  function exportTable() {
    const rows = document.querySelectorAll('#orders-table tr');
    let csv = [];
    rows.forEach(row => {
      const cells = [...row.querySelectorAll('th, td')].slice(0,6);
      csv.push(cells.map(c => '"' + c.innerText.trim().replace(/\n/g,' ') + '"').join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'commandes-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
  }
  </script>

@endsection
