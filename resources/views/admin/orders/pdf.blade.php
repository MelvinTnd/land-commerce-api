<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Commandes - CauriMarket</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1B6B3A; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { color: #1B6B3A; font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 18px; color: #1B6B3A; }
        .title p { margin: 5px 0 0; font-size: 11px; color: #666; }
        
        .stats-box { display: flex; gap: 20px; margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
        .stat-item { flex: 1; }
        .stat-label { font-size: 9px; text-transform: uppercase; color: #888; font-weight: bold; margin-bottom: 2px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #000; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; }
        th { background: #1B6B3A; color: white; text-align: left; padding: 10px; text-transform: uppercase; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #fcfcfc; }
        
        .status { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .status-payee { background: #e8f5ed; color: #1B6B3A; }
        .status-en_attente { background: #fff8e6; color: #b45309; }
        
        .footer { position: fixed; bottom: 20px; left: 20px; right: 20px; font-size: 9px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="{{ request('autoprint') ? 'window.print()' : '' }}">

    <div class="no-print" style="background:#fff3cd; padding:10px; border-radius:8px; margin-bottom:20px; border:1px solid #ffeeba; font-size:13px; display:flex; justify-content:space-between; align-items:center;">
        <span>Prêt pour l'impression ou l'export PDF via votre navigateur.</span>
        <button onclick="window.print()" style="background:#1B6B3A; color:white; border:none; padding:6px 15px; border-radius:5px; cursor:pointer; font-weight:bold;">Imprimer maintenant</button>
    </div>

    <div class="header">
        <div class="logo">CauriMarket</div>
        <div class="title">
            <h1>Rapport des Commandes</h1>
            <p>Généré le {{ date('d/m/Y à H:i') }}</p>
            @if(request('status'))
                <p>Filtre : <strong>{{ strtoupper(request('status')) }}</strong></p>
            @endif
        </div>
    </div>

    <div class="stats-box">
        <div class="stat-item">
            <div class="stat-label">Total Commandes</div>
            <div class="stat-value">{{ $orders->count() }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Volume de ventes (Sélection)</div>
            <div class="stat-value">{{ number_format($orders->sum('total_amount'), 0, ',', ' ') }} CFA</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Statut global</div>
            <div class="stat-value">Actif</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Client</th>
                <th>Articles</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                <td>
                    {{ $order->user->name ?? 'N/A' }}<br>
                    <small style="color:#888">{{ $order->user->email ?? '' }}</small>
                </td>
                <td>{{ $order->items->count() }}</td>
                <td>{{ number_format($order->total_amount, 0, ',', ' ') }} CFA</td>
                <td>
                    <span class="status status-{{ $order->status }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        CauriMarket Plateau - Document officiel généré via le Panel Administration. Page 1 sur 1.
    </div>

</body>
</html>
