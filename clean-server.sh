#!/bin/bash

echo "🧹 Nettoyage complet du serveur..."

# Se connecter au serveur et nettoyer
ssh forge@bracongostages.bigfive.dev << 'ENDSSH'
cd bracongostages.bigfive.dev

echo "📍 Dans le répertoire: $(pwd)"

echo "🔍 Fichiers avec accents trouvés:"
find . -name "*Témoignage*" -type f 2>/dev/null || echo "Aucun fichier trouvé"
find . -name "*Témoignage*" -type d 2>/dev/null || echo "Aucun dossier trouvé"

echo "🗑️ Suppression des fichiers avec accents..."
find . -name "*Témoignage*" -type f -delete 2>/dev/null || true
find . -name "*Témoignage*" -type d -exec rm -rf {} + 2>/dev/null || true

echo "🧹 Nettoyage du cache autoloader..."
rm -rf vendor/composer/autoload_*.php 2>/dev/null || true

echo "✅ Nettoyage terminé!"

ENDSSH

echo "🚀 Maintenant relancez votre déploiement!"