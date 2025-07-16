#!/bin/bash

# Script de déploiement BRACONGO Stages pour Laravel Forge
# Utilisé automatiquement par Forge lors des déploiements

set -e

echo "🚀 Déploiement BRACONGO Stages en production..."

# Variables d'environnement (définies dans Forge)
FORGE_SITE_PATH=${FORGE_SITE_PATH:-/home/forge/bracongo-stages.com}
FORGE_SITE_USER=${FORGE_SITE_USER:-forge}

cd $FORGE_SITE_PATH

# 1. Git pull et mise à jour du code
echo "📥 Récupération du code source..."
git pull origin main

# 2. Installation/mise à jour des dépendances Composer
echo "📦 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 3. Installation/mise à jour des dépendances Node.js
echo "📦 Installation des dépendances Node.js..."
npm ci --production

# 4. Compilation des assets frontend
echo "🎨 Compilation des assets frontend..."
npm run build

# 5. Mise en cache de la configuration
echo "⚡ Optimisations Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5.1 Test de la configuration mail
echo "📧 Test de la configuration mail..."
if php artisan tinker --execute="try { Mail::raw('Test de configuration SMTP', function(\$message) { \$message->to('test@bracongo.cd')->subject('Test SMTP - ' . date('Y-m-d H:i:s')); }); echo '✅ Configuration mail OK'; } catch (Exception \$e) { echo '❌ Erreur mail: ' . \$e->getMessage(); }" 2>/dev/null; then
    echo "✅ Configuration mail validée"
else
    echo "⚠️ Configuration mail à vérifier"
fi

# 6. Exécution des migrations de base de données
echo "🗄️ Mise à jour de la base de données..."
php artisan migrate --force --no-interaction

# 7. Création du lien symbolique pour le storage
echo "🔗 Configuration du stockage..."
php artisan storage:link

# 8. Rechargement de PHP-FPM et services
echo "🔄 Rechargement des services..."
sudo -S service php8.2-fpm reload

# 9. Nettoyage des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 10. Optimisations finales
echo "⚡ Optimisations finales..."
php artisan optimize

# 11. Vérification de l'état de l'application
echo "✅ Vérification de l'application..."
php artisan about

# 12. Test de connectivité base de données
echo "📊 Test de connectivité base de données..."
php artisan tinker --execute="echo 'DB OK: ' . \DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);"

# 13. Configuration des permissions finales
echo "🔧 Configuration des permissions..."
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 14. Notification de fin de déploiement
echo "🎉 Déploiement BRACONGO Stages terminé avec succès !"
echo "🍺 Application accessible sur: https://bracongo-stages.com"
echo "⚙️ Admin Panel: https://bracongo-stages.com/admin"

# 15. Envoi d'une notification (optionnel)
if [ ! -z "$SLACK_WEBHOOK_URL" ]; then
    echo "📲 Envoi notification Slack..."
    curl -X POST -H 'Content-type: application/json' \
        --data '{"text":"🍺 BRACONGO Stages déployé avec succès en production !"}' \
        $SLACK_WEBHOOK_URL
fi

# 16. Backup automatique post-déploiement (recommandé)
if command -v mysqldump &> /dev/null; then
    echo "💾 Sauvegarde automatique post-déploiement..."
    BACKUP_DIR="/home/forge/backups/bracongo-stages"
    BACKUP_FILE="bracongo_stages_$(date +%Y%m%d_%H%M%S).sql"
    
    mkdir -p $BACKUP_DIR
    mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_DIR/$BACKUP_FILE
    
    # Garder seulement les 7 dernières sauvegardes
    find $BACKUP_DIR -name "*.sql" -type f -mtime +7 -delete
fi

echo "✅ Script de déploiement Forge terminé avec succès !"
echo ""
echo "🔗 Liens utiles :"
echo "   • Site principal: https://bracongo-stages.com"
echo "   • Admin Panel: https://bracongo-stages.com/admin"
echo "   • Mailpit (si activé): https://mail.bracongo-stages.com"
echo ""
echo "📧 Comptes par défaut :"
echo "   • Admin: admin@bracongo.com / BracongoAdmin2024!"
echo "   • DG: dg@bracongo.com / BracongoDG2024!"
echo ""
echo "🍺 BRACONGO Stages est prêt pour les candidatures !" 