<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRACONGO Stages - Plateforme de Candidature</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            min-height: 100vh;
            color: white;
        }
        
        .header {
            background: rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }
        
        .hero {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .buttons {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            background: white;
            color: #f97316;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .btn.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }
        
        .feature {
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .feature h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .status {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .buttons { flex-direction: column; align-items: center; }
            .btn { width: 100%; max-width: 300px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">🏭 BRACONGO</div>
        </div>
    </header>
    
    <main class="hero">
        <div class="container">
            <h1>Plateforme de Stages</h1>
            <p>Postulez pour un stage chez BRACONGO - Brasseries du Congo</p>
            
            <div class="buttons">
                <a href="/candidature" class="btn">📝 Nouvelle Candidature</a>
                <a href="/suivi" class="btn secondary">🔍 Suivi de Candidature</a>
                <a href="/admin" class="btn secondary">👤 Administration</a>
                <a href="/test" class="btn secondary">🔧 Test Technique</a>
            </div>
            
            <div class="features">
                <div class="feature">
                    <h3>🎯 Candidature Simplifiée</h3>
                    <p>Formulaire intuitif en plusieurs étapes pour soumettre votre candidature facilement</p>
                </div>
                <div class="feature">
                    <h3>📊 Suivi en Temps Réel</h3>
                    <p>Suivez l'évolution de votre dossier avec un code de suivi personnalisé</p>
                </div>
                <div class="feature">
                    <h3>⚡ Traitement Rapide</h3>
                    <p>Notifications automatiques à chaque étape de validation de votre candidature</p>
                </div>
            </div>
            
            <div class="status">
                ✅ Application BRACONGO Stages déployée avec succès<br>
                🚀 Interface candidat opérationnelle<br>
                🔧 Configuration Docker avec extension PHP intl installée
            </div>
        </div>
    </main>
</body>
</html> 