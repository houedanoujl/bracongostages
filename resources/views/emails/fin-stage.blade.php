<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin de Stage - BRACONGO</title>
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
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .stage-details {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .stage-details h3 {
            margin: 0 0 15px 0;
            color: #0369a1;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #374151;
        }
        .detail-value {
            color: #6b7280;
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
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
        }
        .social-links a:hover {
            color: #f97316;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo-bracongo-white.png') }}" alt="BRACONGO" class="logo">
            <h1>🎓 Fin de Stage</h1>
            <p>Votre expérience chez BRACONGO</p>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $candidature->prenom }} {{ $candidature->nom }},
            </div>

            <div class="message">
                <p>Nous espérons que votre stage chez BRACONGO s'est bien déroulé et qu'il a répondu à vos attentes.</p>
                
                <p>Votre stage s'est terminé le <strong>{{ $candidature->date_fin_stage->format('d/m/Y') }}</strong>. Nous tenons à vous remercier pour votre contribution et votre engagement tout au long de cette période.</p>
            </div>

            <div class="stage-details">
                <h3>📋 Récapitulatif de votre stage</h3>
                <div class="detail-row">
                    <span class="detail-label">Code de suivi :</span>
                    <span class="detail-value">{{ $candidature->code_suivi }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date de début :</span>
                    <span class="detail-value">{{ $candidature->date_debut_stage->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date de fin :</span>
                    <span class="detail-value">{{ $candidature->date_fin_stage->format('d/m/Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Durée :</span>
                    <span class="detail-value">{{ $candidature->date_debut_stage->diffInDays($candidature->date_fin_stage) }} jours</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Établissement :</span>
                    <span class="detail-value">{{ $candidature->etablissement }}</span>
                </div>
            </div>

            <div class="highlight">
                <h3 style="margin-top: 0; color: #92400e;">💡 Partagez votre expérience</h3>
                <p style="margin-bottom: 0; color: #92400e;">
                    Votre retour est précieux pour nous ! En quelques minutes, vous pouvez nous aider à améliorer l'expérience des futurs stagiaires en évaluant votre stage.
                </p>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('candidature.evaluation', $candidature) }}" class="cta-button">
                    📝 Évaluer mon stage
                </a>
            </div>

            <div class="message">
                <p><strong>Pourquoi évaluer votre stage ?</strong></p>
                <ul style="color: #4b5563; line-height: 1.8;">
                    <li>Nous aider à améliorer l'accueil des futurs stagiaires</li>
                    <li>Partager vos suggestions d'amélioration</li>
                    <li>Contribuer à l'évolution de nos programmes de stage</li>
                    <li>Maintenir le contact avec BRACONGO pour de futures opportunités</li>
                </ul>
            </div>

            <div class="message">
                <p>L'évaluation ne prend que quelques minutes et vos réponses restent confidentielles. Merci de prendre le temps de partager votre expérience avec nous !</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>BRACONGO - Brasseries du Congo</strong></p>
            <p>Votre partenaire pour des stages enrichissants</p>
            
            <div class="social-links">
                <a href="https://www.bracongo.cg" target="_blank">🌐 Site web</a>
                <a href="mailto:stages@bracongo.cg">📧 Contact</a>
                <a href="tel:+242012345678">📞 Téléphone</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html> 