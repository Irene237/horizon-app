<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu d'Inscription #{{ $enrollment->id }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; line-height: 1.5; }
        .header { margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; color: #1e3a8a; }
        .title { font-size: 18px; font-weight: bold; margin-top: 20px; text-transform: uppercase; border-bottom: 2px solid #1e3a8a; padding-bottom: 5px; text-align: center; }
        .details-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .details-table th { background-color: #f3f4f6; }
        .summary-box { margin-top: 20px; text-align: right; font-size: 14px; }
        .highlight { font-size: 16px; font-weight: bold; color: #1e3a8a; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #777; position: absolute; bottom: 0; width: 100%; }
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
                <strong>Date :</strong> {{ \Carbon\Carbon::parse($enrollment->created_at)->format('d/m/Y') }}<br>
                <strong>Statut Paiement :</strong> {{ strtoupper($enrollment->payment_status) }}
            </td>
        </tr>
    </table>

    <div class="title">Reçu d'Inscription à la Formation</div>

    <div style="margin-top: 20px; background: #f9fafb; padding: 15px; border-radius: 5px;">
        <strong>Nom de l'Apprenant :</strong> {{ $enrollment->customer->name }}<br>
        <strong>Téléphone :</strong> {{ $enrollment->customer->phone }}<br>
        <strong>Email :</strong> {{ $enrollment->customer->email }}
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Intitulé de la Formation</th>
                <th>Formateur</th>
                <th>Durée</th>
                <th>Prix Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $enrollment->training->title }}</td>
                <td>{{ $enrollment->training->trainer_name }}</td>
                <td>{{ $enrollment->training->duration_hours }} heures</td>
                <td>{{ number_format($enrollment->training->price, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <p>Montant Versé : <strong>{{ number_format($enrollment->amount_paid, 0, ',', ' ') }} FCFA</strong></p>
        <p class="highlight">Reste à payer : {{ number_format($enrollment->training->price - $enrollment->amount_paid, 0, ',', ' ') }} FCFA</p>
    </div>

    <div class="footer">
        <p>Horizon Numérique - Document généré automatiquement - Cachet non requis pour validation en ligne.</p>
    </div>

</body>
</html>