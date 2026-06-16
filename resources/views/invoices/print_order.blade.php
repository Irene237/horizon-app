<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Commande d'Impression #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; line-height: 1.5; }
        .header { margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; color: #1e3a8a; }
        .title { font-size: 18px; font-weight: bold; margin-top: 20px; text-transform: uppercase; border-bottom: 2px solid #1e3a8a; padding-bottom: 5px; }
        .details-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .details-table th { background-color: #f3f4f6; }
        .total-box { margin-top: 20px; text-align: right; font-size: 16px; font-weight: bold; }
        .footer { margin-top: 5px; text-align: center; font-size: 11px; color: #777; position: absolute; bottom: 0; width: 100%; }
    </style>
</head>
<body>

    <table style="width: 100%;" class="header">
        <tr>
            <td>
                <div class="company-name">HORIZON NUMÉRIQUE</div>
                <p>Yaoundé, Cameroun<br>Email: contact@horizon.cm</p>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <strong>Date :</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}<br>
                <strong>Statut :</strong> {{ strtoupper($order->status) }}
            </td>
        </tr>
    </table>

    <div class="title">
        {{ $order->status === 'quote' ? "Devis d'Impression Numérique" : "Bon de Commande d'Impression" }} #{{ $order->id }}
    </div>

    <div style="margin-top: 20px;">
        <strong>Client :</strong> {{ $order->customer ? $order->customer->name : 'Client Anonyme' }}<br>
        <strong>Téléphone :</strong> {{ $order->customer ? $order->customer->phone : 'N/A' }}<br>
        <strong>Adresse :</strong> {{ $order->customer ? $order->customer->address : 'N/A' }}
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Type de Support</th>
                <th>Dimensions (L x H)</th>
                <th>Quantité</th>
                <th>Tarif de Base</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ ucfirst($order->support_type) }}</td>
                <td>{{ $order->width && $order->height ? $order->width . ' cm x ' . $order->height . ' cm' : 'Format Standard' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ number_format($order->unit_price, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        MONTANT TOTAL : {{ number_format($order->total_price, 0, ',', ' ') }} FCFA
    </div>

    <div class="footer">
        <p>Horizon Numérique - Document généré automatiquement par le système de gestion.</p>
    </div>

</body>
</html>