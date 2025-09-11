#!/bin/bash

echo "🧹 Nettoyage complet des conflits Témoignage/Temoignage..."

# Supprimer tous les fichiers et dossiers avec accents
echo "Suppression des fichiers avec accents..."
find . -name "*Témoignage*" -type f -delete 2>/dev/null || true
find . -name "*Témoignage*" -type d -exec rm -rf {} + 2>/dev/null || true

# Supprimer le cache de l'autoloader
echo "Nettoyage du cache autoloader..."
rm -rf vendor/composer/autoload_*.php 2>/dev/null || true

# Régénérer l'autoloader
echo "Régénération de l'autoloader..."
composer dump-autoload --optimize --no-dev

# Nettoyer les caches Laravel
echo "Nettoyage des caches Laravel..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true  
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "✅ Nettoyage terminé !"