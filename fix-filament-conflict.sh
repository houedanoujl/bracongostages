#!/bin/bash

# Script pour résoudre le conflit de noms de classes Filament
# Supprime les fichiers avec accents qui causent des conflits
set -e

echo "🔧 Résolution du conflit de classes Filament..."

# Navigation vers le bon répertoire
if [[ "$(pwd)" == "/home/forge" ]]; then
    if [ -d "bracongostages.bigfive.dev" ]; then
        echo "📁 Navigation vers bracongostages.bigfive.dev..."
        cd bracongostages.bigfive.dev
    else
        echo "❌ Répertoire bracongostages.bigfive.dev non trouvé"
        exit 1
    fi
fi

echo "📁 Répertoire de travail: $(pwd)"

# Vérifier que nous sommes dans un projet Laravel
if [ ! -f "artisan" ]; then
    echo "❌ artisan non trouvé, ce n'est pas un projet Laravel"
    exit 1
fi

# 1. Supprimer le dossier avec accents
echo "🗑️ Suppression des fichiers avec accents..."
if [ -d "app/Filament/Resources/TémoignageResource" ]; then
    echo "⚠️ Suppression du dossier TémoignageResource (avec accents)..."
    rm -rf "app/Filament/Resources/TémoignageResource"
    echo "✅ Dossier TémoignageResource supprimé"
else
    echo "ℹ️ Dossier TémoignageResource (avec accents) non trouvé"
fi

# 2. Vérifier que le dossier sans accents existe
if [ -d "app/Filament/Resources/TemoignageResource" ]; then
    echo "✅ Dossier TemoignageResource (sans accents) trouvé"
else
    echo "❌ Dossier TemoignageResource (sans accents) non trouvé"
    exit 1
fi

# 3. Nettoyer le cache Laravel
echo "🧹 Nettoyage du cache Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Optimiser l'autoloader
echo "⚡ Optimisation de l'autoloader..."
composer dump-autoload --optimize

# 5. Vérifier que tout fonctionne
echo "✅ Vérification..."
php artisan about --only=environment

echo ""
echo "🎉 Conflit de classes résolu !"
echo "✅ Les fichiers avec accents ont été supprimés"
echo "✅ Le cache Laravel a été nettoyé"
echo "✅ L'autoloader a été optimisé"
echo ""
echo "🚀 Vous pouvez maintenant relancer le déploiement"
