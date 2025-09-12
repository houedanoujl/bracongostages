#!/bin/bash

# 🍺 BRACONGO Stages - Script de Déploiement avec Reset Forcé
# Ce script effectue un reset complet de la base de données sans confirmation

set -e  # Arrêter en cas d'erreur

echo "🚀 Déploiement BRACONGO Stages (Reset Forcé)..."

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Configuration des variables d'environnement
print_status "Nettoyage du cache de configuration..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reset complet de la base de données
print_status "Reset COMPLET de la base de données (FORCE)..."
print_warning "⚠️  ATTENTION: Toutes les données existantes vont être supprimées !"

# Attendre 3 secondes pour laisser le temps de lire
sleep 3

# Exécuter le reset forcé
php artisan migrate:fresh --seed --force

print_success "Reset de la base de données terminé !"

# Publication des assets Filament
print_status "Publication des assets Filament..."
php artisan filament:assets 2>/dev/null || print_warning "Commande filament:assets non disponible"
php artisan vendor:publish --provider="Filament\FilamentServiceProvider" --force 2>/dev/null || true

# Compilation des assets
print_status "Compilation des assets..."
npm run build 2>/dev/null || (print_warning "Build failed, trying dev mode..." && npm run dev)

# Permissions
print_status "Configuration des permissions..."
chmod -R 775 storage/ bootstrap/cache/
chmod -R 755 public/

# Propriétaire selon l'environnement
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage/ bootstrap/cache/ public/ 2>/dev/null || true
else
    chown -R www-data:www-data storage/ bootstrap/cache/ public/ 2>/dev/null || true
fi

# Lien symbolique
print_status "Création du lien de stockage..."
php artisan storage:link

# Optimisation Laravel
print_status "Optimisation Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_success "🎉 Déploiement avec reset forcé terminé !"
print_warning "Toutes les données ont été réinitialisées avec les données de test"

echo ""
echo "🌐 Accès admin : https://$(hostname -f || echo 'votre-domaine.com')/admin"
echo "👤 Email : admin@bracongo.cg"
echo "🔑 Mot de passe : AdminBracongo2024!"
echo "🧪 Code test : BRC-TEST123"