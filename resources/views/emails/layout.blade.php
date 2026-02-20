<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRACONGO - Programme de Stages</title>
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
        .content p {
            font-size: 16px;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 15px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo-bracongo-white.png') }}" alt="BRACONGO" class="logo">
            <h1>BRACONGO</h1>
            <p>Programme de stages BRACONGO</p>
        </div>

        <div class="content">
            {!! $contenu !!}
        </div>

        <div class="footer">
            <p><strong>BRACONGO - Brasseries du Congo</strong></p>
            <p>Avenue des Brasseries n&deg;7666, Kingabwa, Limete</p>
            <p>Kinshasa, R&eacute;publique D&eacute;mocratique du Congo</p>
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                Cet email a &eacute;t&eacute; envoy&eacute; automatiquement. Merci de ne pas y r&eacute;pondre directement.
            </p>
        </div>
    </div>
</body>
</html>
