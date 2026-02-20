<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation des Dates de Stage - BRACONGO</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .message {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #4b5563;
        }
        .info-box {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #0369a1;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0f2fe;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: bold;
            color: #374151;
        }
        .detail-value {
            color: #6b7280;
        }
        .highlight {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo-bracongo-white.png') }}" alt="BRACONGO" class="logo">
            <h1>📅 Confirmation de Stage</h1>
            <p>Programme de stages BRACONGO</p>
        </div>

        <div class="content">
            <div class="greeting">
                Madame / Monsieur {{ $nom }},
            </div>

            <div class="message">
                <p>Nous vous confirmons que votre stage au sein de Bracongo se déroulera selon les modalités suivantes :</p>
            </div>

            <div class="info-box">
                <h3>📋 Détails de votre stage</h3>
                <div class="detail-row">
                    <span class="detail-label">Date de début :</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($date_debut)->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date de fin :</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($date_fin)->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Direction / Service d'affectation :</span>
                    <span class="detail-value">{{ $direction_service }}</span>
                </div>
            </div>

            <div class="message">
                <p>Nous vous prions de vous présenter le premier jour à {{ $heure_presentation }} auprès de la Direction des Ressources Humaines pour les formalités d'accueil.</p>

                <p>Nous vous souhaitons pleine réussite dans cette expérience professionnelle.</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>BRACONGO - Brasseries du Congo</strong></p>
            <p>Avenue des Brasseries n°7666, Kingabwa, Limete</p>
            <p>Kinshasa, République Démocratique du Congo</p>
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
