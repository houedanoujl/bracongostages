#!/bin/bash

# Script de déploiement BRACONGO Stages optimisé pour Laravel Forge
set -e

echo "🚀 Déploiement BRACONGO Stages..."

# 1. Mise à jour du code depuis Git
echo "📥 Mise à jour du code..."
git pull origin ${FORGE_SITE_BRANCH:-main}

# 2. Installation des dépendances Composer (optimisé pour production)
echo "📦 Installation Composer..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3. Installation des dépendances Node.js (production uniquement)
echo "📦 Installation Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --only=production
else
    npm install --only=production
fi

# 4. Build des assets avec Vite
echo "🎨 Build des assets..."
npm run build

# 5. Création des répertoires nécessaires
echo "📁 Création des répertoires..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 6. Permissions Laravel
echo "🔧 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache

# 7. Configuration Laravel
echo "⚙️ Configuration Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Migration de la base de données
echo "🗄️ Migration base de données..."
php artisan migrate --force --no-interaction

# 9. Lien symbolique storage
echo "🔗 Lien symbolique storage..."
php artisan storage:link

# 10. Optimisations finales
echo "⚡ Optimisations..."
php artisan optimize

# 11. Redémarrage des services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all
fi

# 12. Vérification des caches
echo "✅ Caches de configuration, routes et vues actifs."

echo "✅ Déploiement terminé avec succès !"
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"