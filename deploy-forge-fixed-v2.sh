#!/bin/bash

# Script de déploiement BRACONGO Stages pour Laravel Forge (Version Corrigée v2)
# Résout le problème de bootstrap/cache manquant lors du composer install
set -e

echo "🚀 Déploiement BRACONGO Stages (Version Corrigée)..."

# Variables d'environnement
FORGE_SITE_PATH=${FORGE_SITE_PATH:-/home/forge/bracongostages.bigfive.dev}
FORGE_SITE_USER=${FORGE_SITE_USER:-forge}
FORGE_SITE_BRANCH=${FORGE_SITE_BRANCH:-main}

# 1. Mise à jour du code depuis Git
echo "📥 Mise à jour du code..."
git pull origin $FORGE_SITE_BRANCH

# 2. CRÉATION DES RÉPERTOIRES LARAVEL AVANT TOUTE AUTRE OPÉRATION
echo "📁 Création des répertoires Laravel requis..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 3. CONFIGURATION DES PERMISSIONS AVANT COMPOSER
echo "🔧 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# Vérification que bootstrap/cache est accessible en écriture
if [ ! -w bootstrap/cache ]; then
    echo "❌ Erreur: bootstrap/cache n'est pas accessible en écriture"
    exit 1
fi
echo "✅ Répertoires Laravel créés et permissions configurées"

# 4. Configuration de l'environnement
echo "📋 Configuration de l'environnement..."
if [ ! -f .env ]; then
    cat > .env << 'EOL'
APP_NAME="BRACONGO Stages"
APP_ENV=production
APP_KEY=base64:+DiT/dEhYPOyDTCYA3gPRrRoH4ts/a0uoxhRhO48zGs=
APP_DEBUG=false
APP_URL=https://bracongostages.bigfive.dev

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forge
DB_USERNAME=forge
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bracongostages.bigfive.dev"
MAIL_FROM_NAME="${APP_NAME}"

FILAMENT_FILESYSTEM_DISK=public
EOL
    echo "✅ Fichier .env créé"
else
    echo "✅ Fichier .env existe déjà"
fi

# 5. Installation des dépendances Composer (AVEC les répertoires créés)
echo "📦 Installation des dépendances Composer..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 6. Installation des dépendances Node.js (production uniquement)
echo "📦 Installation des dépendances Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --only=production --no-audit
else
    npm install --only=production --no-audit
fi

# 7. Build des assets avec Vite
echo "🎨 Build des assets..."
npm run build

# 8. Configuration Laravel (cache)
echo "⚙️ Configuration Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Migration de la base de données
echo "🗄️ Migration base de données..."
php artisan migrate --force --no-interaction

# 10. Lien symbolique storage
echo "🔗 Lien symbolique storage..."
php artisan storage:link

# 11. Optimisations finales
echo "⚡ Optimisations..."
php artisan optimize

# 12. Redémarrage des services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 13. Nettoyage des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan view:clear

# 14. Vérifications finales
echo "✅ Vérifications finales..."
php artisan about --only=environment

# 15. Configuration finale des permissions (sécurité)
echo "🔧 Vérification finale des permissions..."
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "🎉 Déploiement terminé avec succès !"
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"
echo ""
echo "📧 Comptes par défaut :"
echo "   • Admin: admin@bracongo.com / BracongoAdmin2024!"
echo "   • DG: dg@bracongo.com / BracongoDG2024!"
echo ""
echo "🍺 BRACONGO Stages est prêt pour les candidatures !"
