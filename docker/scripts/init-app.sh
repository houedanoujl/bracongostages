#!/bin/bash

# Script d'initialisation automatique BRACONGO Stages
# Ce script s'exécute au démarrage du container pour configurer l'application

set -e

echo "🚀 Initialisation de BRACONGO Stages..."

# Démarrer PHP-FPM en arrière-plan immédiatement
echo "🚀 Démarrage de PHP-FPM..."
php-fpm &
PHP_FPM_PID=$!

# Essayer de se connecter à MySQL de manière non-bloquante
echo "⏳ Tentative de connexion à la base de données (non-bloquante)..."
(
    # Installer les dépendances Composer si vendor n'existe pas (avant tout artisan)
    if [ ! -d "/var/www/vendor" ]; then
        echo "📦 Installation des dépendances Composer..."
        cd /var/www && composer install --no-interaction --prefer-dist
    fi

    if php /var/www/docker/scripts/wait-for-db.php; then
        echo "✅ Base de données disponible, poursuite de l'initialisation..."

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
        TABLE_COUNT=$(php artisan tinker --execute="echo \DB::connection()->getSchemaBuilder()->hasTable('users') ? '1' : '0';" 2>/dev/null | tail -1)
        TABLE_COUNT="${TABLE_COUNT:-0}"

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

        echo "✅ Initialisation base de données terminée !"
    else
        echo "⚠️ Base de données non disponible, l'application fonctionne en mode dégradé"
    fi
) &

# Fixer les permissions
echo "🔧 Configuration des permissions..."
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ PHP-FPM démarré ! L'application est accessible."

# Attendre que PHP-FPM se termine (garde le container en vie)
wait $PHP_FPM_PID 