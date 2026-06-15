<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $order->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; }
        .invoice-box { max-width: 800px; margin: auto; padding: 10px; }
        .header { width: 100%; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #4e73df; }
        .company-details, .client-details { width: 50%; float: left; font-size: 13px; }
        .client-details { text-align: right; }
        .clear { clear: both; }
        .invoice-details { margin-top: 20px; text-align: right; font-size: 13px; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; margin-top: 20px; }
        table th { background: #f8f9fa; color: #555; padding: 10px; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
        table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total-section { float: right; width: 40%; margin-top: 20px; font-size: 14px; }
        .total-table td { padding: 5px 10px; border: none; }
        .grand-total { font-weight: bold; color: #4e73df; font-size: 16px; border-top: 2px solid #4e73df !important; }
        .footer { text-align: center; margin-top: 50px; font-size: 11px; color: #999; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-details">
                <div class="logo">HORIZON</div>
                <p>Yaoundé, Cameroun<br>Contact: support@horizon.com</p>
            </div>
            <div class="client-details">
                <h3>Facturé à :</h3>
                <p>
                    {{ $order->customer ? $order->customer->name : 'Client Comptant (Anonyme)' }}<br>
                    {{ $order->customer && $order->customer->phone ? $order->customer->phone : '' }}
                </p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="invoice-details">
            <strong>Numéro de facture :</strong> #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}<br>
            <strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
            <strong>Mode de Paiement :</strong> {{ strtoupper(str_replace('_', ' ', $order->payment_mode)) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix Unitaire</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Sous-total:</td>
                    <td style="text-align: right;">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td>Remise:</td>
                    <td style="text-align: right; color: red;">-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>Total Net:</td>
                    <td style="text-align: right;">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>

        <div class="footer">
            Merci pour votre confiance ! — Logiciel Horizon POS
        </div>
    </div>
</body>
</html>