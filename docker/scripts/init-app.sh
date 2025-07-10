#!/bin/bash

# Script d'initialisation automatique BRACONGO Stages
# Ce script s'exécute au démarrage du container pour configurer l'application

set -e

echo "🚀 Initialisation de BRACONGO Stages..."

# Attendre que MySQL soit prêt
echo "⏳ Attente de la base de données..."
php /var/www/docker/scripts/wait-for-db.php

# Installer les dépendances Composer si vendor n'existe pas
if [ ! -d "/var/www/vendor" ]; then
    echo "📦 Installation des dépendances Composer..."
    composer install --no-dev --optimize-autoloader
fi

# Générer la clé d'application si elle n'existe pas
if [ ! -f "/var/www/.env" ]; then
    echo "📋 Copie du fichier .env..."
    cp /var/www/.env.example /var/www/.env
fi

if grep -q "APP_KEY=$" /var/www/.env; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --no-interaction
fi

# Vérifier si les tables existent déjà
TABLE_COUNT=$(php artisan tinker --execute="echo \DB::connection()->getSchemaBuilder()->hasTable('users') ? '1' : '0';" 2>/dev/null | tail -1 || echo "0")

if [ "$TABLE_COUNT" = "0" ]; then
    echo "🗄️ Création des tables de base de données..."
    php artisan migrate --no-interaction --force
    
    echo "🌱 Insertion des données de démonstration..."
    php artisan db:seed --no-interaction --force
else
    echo "✅ Base de données déjà initialisée"
    
    # Exécuter les nouvelles migrations s'il y en a
    echo "🔄 Vérification des nouvelles migrations..."
    php artisan migrate --no-interaction --force
fi

# Créer le lien symbolique pour le storage
if [ ! -L "/var/www/public/storage" ]; then
    echo "🔗 Création du lien symbolique storage..."
    php artisan storage:link --no-interaction
fi

# Optimisations pour la production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimisations production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Fixer les permissions
echo "🔧 Configuration des permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ Initialisation terminée ! BRACONGO Stages est prêt."

# Démarrer PHP-FPM
echo "🚀 Démarrage de PHP-FPM..."
exec php-fpm 