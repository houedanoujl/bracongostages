#!/bin/bash

# Script de déploiement BRACONGO Stages pour Laravel Forge (Version Corrigée)
set -e

echo "🚀 Déploiement BRACONGO Stages..."

# 1. Récupération du code depuis Git
echo "📥 Mise à jour du code..."
git pull origin ${FORGE_SITE_BRANCH:-main}

# 2. Copie du fichier .env de production
echo "📋 Configuration de l'environnement..."
if [ -f ".env.production" ]; then
    cp .env.production .env
    echo "✅ Fichier .env.production copié"
else
    echo "⚠️ Fichier .env.production manquant, utilisation du .env existant"
fi

# 3. Installation Composer (optimisé production)
echo "📦 Installation Composer..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 4. Création des répertoires Laravel requis
echo "📁 Création des répertoires..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 5. Permissions Laravel
echo "🔧 Permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 6. Installation Node.js (production uniquement)
echo "📦 Installation Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --only=production --no-audit
else
    npm install --only=production --no-audit
fi

# 7. Build des assets
echo "🎨 Build des assets..."
npm run build

# 8. Artisan commands
echo "⚙️ Configuration Laravel..."
php artisan key:generate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Migration base de données
echo "🗄️ Migration BDD..."
php artisan migrate --force --no-interaction

# 10. Lien symbolique storage
echo "🔗 Lien symbolique..."
php artisan storage:link

# 11. Optimisations
echo "⚡ Optimisations..."
php artisan optimize

# 12. Redémarrage services
echo "🔄 Redémarrage..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 13. Vérifications finales
echo "✅ Vérifications..."
php artisan about --only=environment

echo "🎉 Déploiement terminé !"
echo "🌐 https://bracongostages.bigfive.dev"
echo "⚙️ https://bracongostages.bigfive.dev/admin"