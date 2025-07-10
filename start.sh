#!/bin/bash

# Script de démarrage rapide BRACONGO Stages
# Usage: ./start.sh

set -e

echo "🍺 BRACONGO Stages - Démarrage rapide"
echo "======================================"

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez l'installer avant de continuer."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Veuillez l'installer avant de continuer."
    exit 1
fi

# Créer le fichier .env s'il n'existe pas
if [ ! -f ".env" ]; then
    echo "📋 Création du fichier .env..."
    cp .env.example .env
fi

# Démarrer tous les services
echo "🚀 Démarrage des services Docker..."
docker-compose up -d

# Attendre que MySQL soit prêt
echo "⏳ Attente de la base de données..."
sleep 10

# Installation des dépendances et initialisation
echo "📦 Installation des dépendances..."
docker-compose exec -T app composer install

echo "🔑 Génération de la clé d'application..."
docker-compose exec -T app php artisan key:generate

echo "🗄️ Migration de la base de données..."
docker-compose exec -T app php artisan migrate --force

echo "🌱 Chargement des données de test..."
docker-compose exec -T app php artisan db:seed --force

echo "🔗 Création du lien storage..."
docker-compose exec -T app php artisan storage:link

echo "📦 Installation des dépendances NPM..."
docker-compose exec -T app npm install

echo "🎨 Compilation des assets..."
docker-compose exec -T app npm run build

echo ""
echo "✅ Installation terminée !"
echo ""
echo "🌐 Accès aux services :"
echo "   Application : http://localhost:8000"
echo "   Administration : http://localhost:8000/admin"
echo "   Mailpit : http://localhost:8025"
echo ""
echo "📧 Pour créer un utilisateur admin :"
echo "   make shell"
echo "   php artisan make:filament-user"
echo ""
echo "⚡ Pour démarrer les workers de queue :"
echo "   make queue-work"
echo ""
echo "🍺 Bon développement avec BRACONGO Stages !"