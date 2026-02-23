<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe - BRACONGO Stages</title>
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
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 20px;
            border-radius: 50%;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }
        .security-info {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .security-info h3 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 16px;
        }
        .security-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #78350f;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #6b7280;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            color: #dc2626;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        .brand-colors {
            background: linear-gradient(45deg, #dc2626, #f59e0b, #10b981);
            height: 4px;
            width: 100%;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content, .header, .footer {
                padding: 20px;
            }
            .btn {
                padding: 12px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="brand-colors"></div>
    <div class="container">
        <!-- Header avec logo BRACONGO -->
        <div class="header">
            <h1>🍺 BRACONGO Stages</h1>
            <p>Réinitialisation de mot de passe</p>
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="greeting">
                Bonjour {{ $candidat->prenom }} {{ $candidat->nom }},
            </div>

            <div class="message">
                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte candidat sur la plateforme BRACONGO Stages.
            </div>

            <div class="message">
                Pour créer un nouveau mot de passe sécurisé, cliquez sur le bouton ci-dessous :
            </div>

            <!-- Bouton de réinitialisation -->
            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">
                    🔐 Réinitialiser mon mot de passe
                </a>
            </div>

            <!-- Informations de sécurité -->
            <div class="security-info">
                <h3>Informations importantes :</h3>
                <p>• Ce lien est valide pendant <strong>24 heures</strong></p>
                <p>• Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</p>
                <p>• Votre mot de passe actuel reste inchangé tant que vous n'en créez pas un nouveau</p>
                <p>• Une fois réinitialisé, vous devrez vous reconnecter sur tous vos appareils</p>
            </div>

            <div class="message">
                <strong>Lien de réinitialisation :</strong><br>
                <small style="color: #6b7280; word-break: break-all;">
                    {{ $resetUrl }}
                </small>
            </div>

            <div class="message">
                Si le bouton ne fonctionne pas, copiez et collez le lien ci-dessus dans votre navigateur.
            </div>

            <div class="message">
                <strong>Besoin d'aide ?</strong> Contactez-nous à 
                <a href="mailto:stages@bracongo.cg" style="color: #dc2626;">stages@bracongo.cg</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>BRACONGO</strong> - Brasseries du Congo</p>
            <p>Plateforme de Gestion des Stages</p>
            
            <div class="social-links">
                <a href="mailto:stages@bracongo.cg">Email</a>
                <a href="tel:+243">📞 Contact</a>
                <a href="https://bracongo.cg">🌐 Site Web</a>
            </div>

            <p style="margin-top: 20px; font-size: 11px; color: #9ca3af;">
                Cet email a été envoyé automatiquement depuis la plateforme BRACONGO Stages.<br>
                Merci de ne pas répondre directement à cet email.
            </p>
            
            <p style="font-size: 11px; color: #9ca3af; margin-top: 10px;">
                © {{ date('Y') }} BRACONGO - Tous droits réservés<br>
                "Ensemble, construisons l'avenir" 🍺
            </p>
        </div>
    </div>
    <div class="brand-colors"></div>
</body>
</html>