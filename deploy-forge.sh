#!/bin/bash

# Script de déploiement optimisé BRACONGO Stages pour Laravel Forge
set -e

echo "🚀 Déploiement BRACONGO Stages en production..."

# Variables d'environnement
FORGE_SITE_PATH=${FORGE_SITE_PATH:-/home/forge/bracongostages.bigfive.dev}
FORGE_SITE_USER=${FORGE_SITE_USER:-forge}
FORGE_SITE_BRANCH=${FORGE_SITE_BRANCH:-main}

# cd $FORGE_SITE_PATH - Commenté car déjà dans le répertoire

# 1. Création des répertoires requis AVANT toute autre opération
echo "📁 Préparation des répertoires Laravel..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER bootstrap/cache storage
chmod -R 775 bootstrap/cache storage

echo "✅ Répertoires créés et permissions définies"

# 2. Git pull et mise à jour du code
echo "📥 Récupération du code source..."
git pull origin $FORGE_SITE_BRANCH

# 2. Création du fichier .env de production
echo "📋 Configuration de l'environnement de production..."
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

# 3. Création des répertoires requis AVANT composer install
echo "📁 Création des répertoires Laravel requis..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER bootstrap/cache storage
chmod -R 775 bootstrap/cache storage

# Vérification que les répertoires sont bien créés et accessibles
echo "🔍 Vérification des permissions des répertoires..."
if [ ! -w bootstrap/cache ]; then
    echo "❌ Erreur: bootstrap/cache n'est pas accessible en écriture"
    exit 1
fi
echo "✅ Tous les répertoires sont prêts"

# 4. Installation/mise à jour des dépendances Composer
echo "📦 Installation des dépendances Composer..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 5. Installation/mise à jour des dépendances Node.js
echo "📦 Installation des dépendances Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --only=production
else
    npm install --only=production
fi

# 6. Compilation des assets frontend
echo "🎨 Compilation des assets frontend..."
npm run build

# 7. Mise en cache de la configuration
echo "⚡ Optimisations Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6.1 Test de la configuration mail
echo "📧 Test de la configuration mail..."
if php artisan tinker --execute="try { Mail::raw('Test de configuration SMTP', function(\$message) { \$message->to('test@bracongo.cd')->subject('Test SMTP - ' . date('Y-m-d H:i:s')); }); echo '✅ Configuration mail OK'; } catch (Exception \$e) { echo '❌ Erreur mail: ' . \$e->getMessage(); }" 2>/dev/null; then
    echo "✅ Configuration mail validée"
else
    echo "⚠️ Configuration mail à vérifier"
fi

# 7. Exécution des migrations de base de données
echo "🗄️ Mise à jour de la base de données..."
php artisan migrate --force --no-interaction

# 8. Création du lien symbolique pour le storage
echo "🔗 Configuration du stockage..."
php artisan storage:link

# 9. Rechargement de PHP-FPM et services
echo "🔄 Rechargement des services..."
sudo -S service php8.2-fpm reload

# 10. Optimisations finales (les caches sont déjà créés à l'étape 7)
echo "⚡ Optimisations finales..."
php artisan optimize

# 12. Vérification de l'état de l'application
echo "✅ Vérification de l'application..."
php artisan about

# 13. Test de connectivité base de données
echo "📊 Test de connectivité base de données..."
php artisan tinker --execute="echo 'DB OK: ' . \DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);"

# 14. Configuration finale des permissions (sécurité)
echo "🔧 Vérification finale des permissions..."
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 15. Notification de fin de déploiement
echo "🎉 Déploiement BRACONGO Stages terminé avec succès !"
echo "🍺 Application accessible sur: https://bracongo.bigfive.dev"
echo "⚙️ Admin Panel: https://bracongo.bigfive.dev/admin"

# 16. Envoi d'une notification (optionnel)
if [ ! -z "$SLACK_WEBHOOK_URL" ]; then
    echo "📲 Envoi notification Slack..."
    curl -X POST -H 'Content-type: application/json' \
        --data '{"text":"🍺 BRACONGO Stages déployé avec succès en production !"}' \
        $SLACK_WEBHOOK_URL
fi

# 17. Backup automatique post-déploiement (recommandé)
if command -v mysqldump &> /dev/null; then
    echo "💾 Sauvegarde automatique post-déploiement..."
    BACKUP_DIR="/home/forge/backups/bracongo-bigfive-dev"
    BACKUP_FILE="bracongo_stages_$(date +%Y%m%d_%H%M%S).sql"
    
    mkdir -p $BACKUP_DIR
    mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_DIR/$BACKUP_FILE
    
    # Garder seulement les 7 dernières sauvegardes
    find $BACKUP_DIR -name "*.sql" -type f -mtime +7 -delete
fi

echo "✅ Script de déploiement Forge terminé avec succès !"
echo ""
echo "🔗 Liens utiles :"
echo "   • Site principal: https://bracongo.bigfive.dev"
echo "   • Admin Panel: https://bracongo.bigfive.dev/admin"
echo "   • Mailpit (si activé): https://mail.bracongo.bigfive.dev"
echo ""
echo "📧 Comptes par défaut :"
echo "   • Admin: admin@bracongo.com / BracongoAdmin2024!"
echo "   • DG: dg@bracongo.com / BracongoDG2024!"
echo ""
echo "🍺 BRACONGO Stages est prêt pour les candidatures !" 