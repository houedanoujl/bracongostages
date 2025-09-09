#!/bin/bash

# Script de déploiement automatique pour Laravel Forge
# Gère automatiquement la navigation vers le bon répertoire
set -e

echo "🚀 Déploiement Automatique BRACONGO Stages pour Forge..."

# Fonction pour afficher les erreurs
error_exit() {
    echo "❌ ERREUR: $1" >&2
    exit 1
}

# 1. Navigation automatique vers le répertoire du projet
echo "🔍 Navigation vers le répertoire du projet..."
echo "📁 Répertoire de départ: $(pwd)"

# Si on est dans /home/forge, aller dans le sous-dossier
if [[ "$(pwd)" == "/home/forge" ]]; then
    echo "✅ Détection du répertoire Forge standard"
    if [ -d "bracongostages.bigfive.dev" ]; then
        echo "📁 Navigation vers bracongostages.bigfive.dev..."
        cd bracongostages.bigfive.dev
        echo "📁 Nouveau répertoire: $(pwd)"
    else
        echo "❌ Répertoire bracongostages.bigfive.dev non trouvé dans /home/forge"
        echo "📋 Contenu de /home/forge:"
        ls -la
        error_exit "Impossible de trouver le répertoire du projet"
    fi
fi

# Vérification que nous sommes dans le bon répertoire
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json non trouvé dans $(pwd)"
    echo "📋 Contenu du répertoire:"
    ls -la
    error_exit "Ce n'est pas un répertoire Laravel valide"
fi

if [ ! -f "artisan" ]; then
    echo "❌ artisan non trouvé dans $(pwd)"
    error_exit "Ce n'est pas un répertoire Laravel valide"
fi

echo "✅ Répertoire Laravel correct: $(pwd)"

# 2. Mise à jour Git (optionnelle)
echo "📥 Mise à jour du code..."
if [ -d ".git" ]; then
    echo "✅ Repository Git détecté, tentative de mise à jour..."
    if git pull origin main 2>/dev/null; then
        echo "✅ Code mis à jour avec succès"
    else
        echo "⚠️ Échec de la mise à jour Git, continuation avec le code existant"
    fi
else
    echo "ℹ️ Aucun repository Git détecté, utilisation du code existant"
fi

# 3. Configuration de l'environnement
echo "📋 Configuration de l'environnement..."
if [ -f ".env.production" ]; then
    cp .env.production .env
    echo "✅ Fichier .env.production copié"
elif [ -f ".env.example" ]; then
    echo "⚠️ Copie de .env.example vers .env"
    cp .env.example .env
    echo "⚠️ N'oubliez pas de configurer les variables d'environnement !"
else
    echo "ℹ️ Utilisation du fichier .env existant"
fi

# 4. Installation des dépendances
echo "📦 Installation des dépendances..."

# Composer
echo "🔧 Installation Composer..."

# Nettoyer le cache Composer et le dossier vendor si nécessaire
echo "🧹 Nettoyage préalable..."
if [ -d "vendor" ]; then
    echo "⚠️ Dossier vendor existant, suppression..."
    rm -rf vendor
fi

# S'assurer que Composer peut écrire
echo "🔧 Configuration des permissions pour Composer..."
mkdir -p vendor
chmod -R 775 vendor
if [ "$USER" = "forge" ]; then
    chown -R forge:forge vendor
fi

# Installation Composer
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
echo "✅ Composer installé"

# Permissions finales pour vendor
echo "🔧 Permissions finales pour vendor..."
chmod -R 755 vendor
if [ "$USER" = "forge" ]; then
    chown -R forge:forge vendor
fi

# Node.js
echo "🔧 Installation Node.js..."
if [ -f "package-lock.json" ]; then
    # Installer toutes les dépendances (y compris dev) pour pouvoir builder
    npm ci --no-audit
else
    npm install --no-audit
fi
echo "✅ Node.js installé"

# 5. Création des répertoires
echo "📁 Création des répertoires Laravel..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 6. Permissions
echo "🔧 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 7. Build des assets
echo "🎨 Build des assets..."
if command -v npx >/dev/null 2>&1; then
    npx vite build
else
    # Fallback si npx n'est pas disponible
    ./node_modules/.bin/vite build
fi
echo "✅ Assets construits"

# 8. Configuration Laravel
echo "⚙️ Configuration Laravel..."
php artisan key:generate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Base de données
echo "🗄️ Migration de la base de données..."
php artisan migrate --force --no-interaction

# 10. Lien symbolique
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link

# 11. Optimisations
echo "⚡ Optimisations finales..."
php artisan optimize

# 12. Redémarrage des services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 13. Vérifications
echo "✅ Vérifications finales..."
php artisan about --only=environment

echo ""
echo "🎉 Déploiement terminé avec succès !"
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"
echo ""
echo "📁 Répertoire final: $(pwd)"
