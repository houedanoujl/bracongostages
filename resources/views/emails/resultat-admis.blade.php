<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Félicitations - Admis au Test - BRACONGO</title>
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
        .success-box {
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .success-box h3 {
            margin: 0 0 10px 0;
            color: #166534;
            font-size: 22px;
        }
        .success-box p {
            margin: 0;
            color: #15803d;
            font-size: 16px;
        }
        .highlight {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
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
            <h1>🎉 Résultat du Test</h1>
            <p>Programme de stages BRACONGO</p>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $prenom }} {{ $nom }},
            </div>

            <div class="success-box">
                <h3>🏆 Félicitations !</h3>
                <p>Vous avez été admis(e) au test de niveau BRACONGO</p>
            </div>

            <div class="message">
                <p>Nous avons le plaisir de vous annoncer que vous avez réussi le test de niveau dans le cadre de notre programme de stages.</p>
                <p>Votre candidature passe maintenant à l'étape suivante du processus de sélection. Nos équipes vont procéder à votre affectation dans un service correspondant à votre profil.</p>
            </div>

            <div class="info-box">
                <h3>📋 Prochaines étapes</h3>
                <ul style="color: #4b5563; line-height: 1.8; padding-left: 20px;">
                    <li>Validation finale par la Direction des Ressources Humaines</li>
                    <li>Affectation à un service et désignation d'un tuteur</li>
                    <li>Confirmation des dates de stage par email</li>
                    <li>Session d'induction RH avant le début du stage</li>
                </ul>
            </div>

            <div class="highlight">
                <h3 style="margin-top: 0; color: #92400e;">💡 En attendant</h3>
                <p style="margin-bottom: 0; color: #92400e;">
                    Vous recevrez prochainement un email de confirmation avec les dates exactes de votre stage et les informations pratiques pour votre premier jour.
                </p>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/suivi') }}" class="cta-button">
                    📍 Suivre ma candidature
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>BRACONGO - Brasseries du Congo</strong></p>
            <p>Votre partenaire pour des stages enrichissants</p>
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
