<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rapport Commandes — BéninMarket</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }

  .pdf-header {
    background: #1B6B3A; color: #fff;
    padding: 24px 32px; display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 24px;
  }
  .pdf-logo { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
  .pdf-logo span { color: #D4920A; }
  .pdf-meta { text-align: right; }
  .pdf-meta h1 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
  .pdf-meta p  { font-size: 11px; opacity: 0.85; }

  .stats-row { display: flex; gap: 16px; padding: 0 32px; margin-bottom: 20px; }
  .stat-box { flex: 1; border: 1px solid #e2e8e2; border-radius: 8px; padding: 14px 16px; }
  .stat-box dt { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #9ba8a3; margin-bottom: 4px; }
  .stat-box dd { font-size: 20px; font-weight: 800; color: #1B6B3A; }

  .section { padding: 0 32px; }

  table { width: 100%; border-collapse: collapse; }
  thead th {
    background: #f4f6f4; padding: 9px 12px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px;
    color: #9ba8a3; text-align: left; border-bottom: 2px solid #e2e8e2;
  }
  tbody tr { border-bottom: 1px solid #e2e8e2; }
  tbody tr:last-child { border-bottom: none; }
  tbody td { padding: 10px 12px; vertical-align: middle; }
  tbody tr:nth-child(even) { background: #f9fbf9; }

  .order-id { font-weight: 700; color: #1B6B3A; font-family: monospace; }
  .amount   { font-weight: 700; }
  .status-pill {
    display: inline-block; padding: 2px 8px; border-radius: 20px;
    font-size: 10px; font-weight: 700;
  }
  .s-pending    { background: #fff8e6; color: #b45309; }
  .s-processing { background: #eff6ff; color: #1d4ed8; }
  .s-shipped    { background: #f0fdf4; color: #1B6B3A; }
  .s-delivered  { background: #e8f5ed; color: #1B6B3A; }
  .s-cancelled  { background: #fff5f5; color: #e53e3e; }

  .pdf-footer {
    margin-top: 32px; padding: 16px 32px;
    border-top: 1px solid #e2e8e2;
    display: flex; justify-content: space-between;
    font-size: 10px; color: #9ba8a3;
  }

  @media print {
    .no-print { display: none !important; }
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .pdf-header { -webkit-print-color-adjust: exact; }
  }

  /* Bouton impression */
  .print-bar { background: #1B6B3A; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; }
  .print-bar p { color: rgba(255,255,255,0.85); font-size: 13px; }
  .print-bar-btns { display: flex; gap: 10px; }
  .btn-print {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; transition: all 0.15s; font-family: inherit;
  }
  .btn-print-primary { background: #fff; color: #1B6B3A; }
  .btn-print-primary:hover { background: #e8f5ed; }
  .btn-print-outline { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.4); }
  .btn-print-outline:hover { background: rgba(255,255,255,0.1); }
</style>
</head>
<body>

<!-- Barre actions impression -->
<div class="print-bar no-print">
  <p>📄 Rapport prêt — Cliquez sur <strong>Imprimer / Enregistrer en PDF</strong> pour sauvegarder</p>
  <div class="print-bar-btns">
    <button class="btn-print btn-print-outline" onclick="window.history.back()">← Retour</button>
    <button class="btn-print btn-print-primary" onclick="window.print()">🖨️ Imprimer / PDF</button>
  </div>
</div>

<!-- En-tête PDF -->
<div class="pdf-header">
  <div class="pdf-logo">Bénin<span>Market</span></div>
  <div class="pdf-meta">
    <h1>Rapport des Commandes</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i') }} • BéninMarket Admin</p>
  </div>
</div>

<!-- Stats résumé -->
<div class="stats-row">
  <dl class="stat-box">
    <dt>Total commandes</dt>
    <dd>{{ number_format($stats['total']) }}</dd>
  </dl>
  <dl class="stat-box">
    <dt>Dans ce rapport</dt>
    <dd>{{ $orders->count() }}</dd>
  </dl>
  <dl class="stat-box">
    <dt>CA Livré</dt>
    <dd>{{ number_format($stats['revenue'], 0, ',', ' ') }} CFA</dd>
  </dl>
  <dl class="stat-box">
    <dt>Date rapport</dt>
    <dd style="font-size:14px">{{ now()->format('d M Y') }}</dd>
  </dl>
</div>

<!-- Table commandes -->
<div class="section">
  <table>
    <thead>
      <tr>
        <th>N° Commande</th>
        <th>Client</th>
        <th>Email</th>
        <th>Articles</th>
        <th>Montant</th>
        <th>Statut</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        @php
          $st = $order->status ?? 'pending';
          $stLabels = [
            'pending'=>'En attente','en_attente'=>'En attente',
            'processing'=>'En traitement','payee'=>'En traitement','en_livraison'=>'Expédiée',
            'shipped'=>'Expédiée','delivered'=>'Livrée','livree'=>'Livrée',
            'cancelled'=>'Annulée','annulee'=>'Annulée',
          ];
          $stClass = match(true) {
            in_array($st, ['pending','en_attente'])   => 's-pending',
            in_array($st, ['processing','payee'])      => 's-processing',
            in_array($st, ['shipped','en_livraison'])  => 's-shipped',
            in_array($st, ['delivered','livree'])      => 's-delivered',
            default                                     => 's-cancelled',
          };
        @endphp
        <tr>
          <td class="order-id">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
          <td><strong>{{ $order->user->name ?? 'Client inconnu' }}</strong></td>
          <td style="color:#9ba8a3">{{ $order->user->email ?? '—' }}</td>
          <td>{{ $order->items ? $order->items->count() : 0 }} art.</td>
          <td class="amount">{{ number_format($order->total ?? $order->total_amount ?? 0, 0, ',', ' ') }} CFA</td>
          <td><span class="status-pill {{ $stClass }}">{{ $stLabels[$st] ?? $st }}</span></td>
          <td>{{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:32px;color:#9ba8a3">
            Aucune commande dans ce rapport.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Pied de page -->
<div class="pdf-footer">
  <span>© {{ date('Y') }} BéninMarket — Marketplace artisanal du Bénin</span>
  <span>Confidentiel — Usage interne uniquement</span>
</div>

<script>
// Auto-print si ?autoprint=1
if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
  window.addEventListener('load', () => { setTimeout(() => window.print(), 800); });
}
</script>
</body>
</html>
