#!/bin/bash

echo "🚀 Démarrage du serveur BRACONGO Stages..."

# Arrêter tous les serveurs existants
echo "🛑 Arrêt des serveurs existants..."
pkill -f "php artisan serve" 2>/dev/null
pkill -f ":8000" 2>/dev/null
sleep 2

# Vérifier que le port est libre
echo "🔍 Vérification du port 8000..."
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Port 8000 encore occupé, forcage de l'arrêt..."
    sudo fuser -k 8000/tcp 2>/dev/null || true
    sleep 2
fi

# Nettoyer les caches Laravel
echo "🧹 Nettoyage des caches..."
php artisan cache:clear >/dev/null 2>&1
php artisan config:clear >/dev/null 2>&1
php artisan view:clear >/dev/null 2>&1

# Démarrer le serveur
echo "🔥 Démarrage du serveur sur http://127.0.0.1:8000..."
php artisan serve --host=127.0.0.1 --port=8000 &

# Attendre que le serveur démarre
sleep 3

# Tester que le serveur fonctionne
if curl -s http://127.0.0.1:8000 >/dev/null 2>&1; then
    echo "✅ Serveur démarré avec succès !"
    echo ""
    echo "📄 URLs disponibles :"
    echo "   🏠 Accueil : http://127.0.0.1:8000"
    echo "   📝 Candidature : http://127.0.0.1:8000/candidature"
    echo "   🔍 Suivi : http://127.0.0.1:8000/suivi"
    echo "   ⚙️  Admin : http://127.0.0.1:8000/admin"
    echo ""
    echo "💡 Pour arrêter le serveur : pkill -f 'php artisan serve'"
else
    echo "❌ Erreur lors du démarrage du serveur"
    exit 1
fi 