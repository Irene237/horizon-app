<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Formation</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #222; text-align: center; padding: 40px; background-color: #fff; }
        .border-outer { border: 5px double #1e3a8a; padding: 20px; border-radius: 10px; }
        .border-inner { border: 2px solid #b45309; padding: 40px; border-radius: 5px; }
        .logo { font-size: 28px; font-weight: bold; color: #1e3a8a; letter-spacing: 2px; }
        .subtitle { font-size: 14px; text-transform: uppercase; letter-spacing: 4px; color: #666; margin-top: 5px; }
        .main-title { font-size: 36px; font-weight: bold; color: #b45309; margin: 40px 0; text-transform: uppercase; font-style: italic; }
        .certified-text { font-size: 18px; margin: 20px auto; width: 80%; line-height: 1.8; }
        .student-name { font-size: 26px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #ddd; display: inline-block; padding-bottom: 5px; margin: 10px 0; }
        .meta-info { margin-top: 40px; font-size: 16px; color: #444; }
        .signatures { margin-top: 60px; width: 100%; }
        .signature-block { width: 45%; display: inline-block; vertical-align: top; font-size: 14px; }
        .signature-title { font-weight: bold; color: #1e3a8a; margin-bottom: 50px; }
    </style>
</head>
<body>

    <div class="border-outer">
        <div class="border-inner">
            
            <div class="logo">HORIZON NUMÉRIQUE</div>
            <div class="subtitle">Centre de Formation Professionnelle</div>

            <div class="main-title">Attestation de Réussite</div>

            <div class="certified-text">
                Il est certifié par la présente que l'apprenant(e) :<br>
                <div class="student-name">{{ $enrollment->customer->name }}</div><br>
                a suivi avec assiduité et validé avec succès la formation intitulée :<br>
                <strong>« {{ $enrollment->training->title }} »</strong><br>
                dispensée par <em>{{ $enrollment->training->trainer_name }}</em>.
            </div>

            <div class="meta-info">
                <strong>Durée totale :</strong> {{ $enrollment->training->duration_hours }} Heures de cours théoriques et pratiques.<br>
                <strong>Taux d'assiduité final :</strong> {{ $attendanceRate }}%
            </div>

            <table class="signatures" style="width: 100%;">
                <tr>
                    <td style="width: 50%; text-align: left; padding-left: 40px;">
                        <div class="signature-title">Le Formateur</div>
                        <div>{{ $enrollment->training->trainer_name }}</div>
                    </td>
                    <td style="width: 50%; text-align: right; padding-right: 40px;">
                        <div class="signature-title">La Direction générale</div>
                        <div>Fait à Yaoundé, le {{ date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>