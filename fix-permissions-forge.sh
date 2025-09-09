#!/bin/bash

# Script de réparation des permissions pour Laravel Forge
# À utiliser quand il y a des problèmes de permissions
set -e

echo "🔧 Réparation des permissions BRACONGO Stages..."

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

# 1. Permissions pour le dossier vendor
echo "🔧 Réparation des permissions vendor..."
if [ -d "vendor" ]; then
    echo "⚠️ Suppression du dossier vendor corrompu..."
    rm -rf vendor
fi

mkdir -p vendor
chmod -R 775 vendor
if [ "$USER" = "forge" ]; then
    chown -R forge:forge vendor
fi

# 2. Permissions pour storage et bootstrap/cache
echo "🔧 Réparation des permissions Laravel..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 3. Permissions pour node_modules
echo "🔧 Réparation des permissions Node.js..."
if [ -d "node_modules" ]; then
    chmod -R 755 node_modules
    if [ "$USER" = "forge" ]; then
        chown -R forge:forge node_modules
    fi
fi

# 4. Permissions générales
echo "🔧 Permissions générales..."
chmod 644 composer.json composer.lock package.json package-lock.json
chmod 755 artisan
chmod 644 .env*

# 5. Réinstallation Composer
echo "📦 Réinstallation Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 6. Permissions finales
echo "🔧 Permissions finales..."
chmod -R 755 vendor
if [ "$USER" = "forge" ]; then
    chown -R forge:forge vendor
fi

echo "✅ Permissions réparées avec succès !"
echo "🚀 Vous pouvez maintenant relancer le déploiement"
