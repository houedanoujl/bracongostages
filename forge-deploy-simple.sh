#!/bin/bash

# Script de déploiement simplifié pour Forge
cd /home/forge/bracongo.bigfive.dev

# Création des répertoires requis AVANT composer
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chmod -R 775 bootstrap/cache storage

git pull origin $FORGE_SITE_BRANCH
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Optimisations Laravel
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan optimize

echo "✅ Déploiement terminé"