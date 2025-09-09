#!/bin/bash

# Script de déploiement BRACONGO Stages - Version Simple et Robuste
# Évite les problèmes de permissions en utilisant --no-scripts
set -e

echo "🚀 Déploiement BRACONGO Stages (Version Simple)..."

# 1. Mise à jour du code
echo "📥 Mise à jour du code..."
if [ -d ".git" ]; then
    echo "✅ Repository Git détecté, mise à jour..."
    git pull origin ${FORGE_SITE_BRANCH:-main}
else
    echo "⚠️ Aucun repository Git détecté dans ce répertoire"
    echo "ℹ️ Vérifiez que le déploiement s'exécute dans le bon répertoire"
    echo "ℹ️ Ou que le repository a été correctement cloné sur le serveur"
    # Continue le déploiement même sans Git (pour les déploiements manuels)
fi

# 2. Création des répertoires Laravel
echo "📁 Création des répertoires..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 3. Permissions
echo "🔧 Permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 4. Installation Composer SANS scripts (évite les erreurs)
echo "📦 Installation Composer (sans scripts)..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 5. Exécution manuelle des scripts Laravel après installation
echo "⚙️ Configuration Laravel..."
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Installation Node.js
echo "📦 Installation Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --only=production --no-audit
else
    npm install --only=production --no-audit
fi

# 7. Build des assets
echo "🎨 Build des assets..."
npm run build

# 8. Migration
echo "🗄️ Migration base de données..."
php artisan migrate --force --no-interaction

# 9. Lien symbolique
echo "🔗 Lien symbolique storage..."
php artisan storage:link

# 10. Optimisations
echo "⚡ Optimisations..."
php artisan optimize

# 11. Redémarrage services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

echo "✅ Déploiement terminé avec succès !"
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"
