#!/bin/bash

# Script de déploiement BRACONGO Stages - Version Robuste avec gestion d'erreurs
# Gère les cas où Git n'est pas disponible ou mal configuré
set -e

echo "🚀 Déploiement BRACONGO Stages (Version Robuste)..."

# Fonction pour afficher les erreurs de manière claire
error_exit() {
    echo "❌ ERREUR: $1" >&2
    exit 1
}

# Fonction pour vérifier si une commande existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# 1. Navigation vers le bon répertoire
echo "🔍 Vérification de l'environnement..."
echo "📁 Répertoire actuel: $(pwd)"

# Vérifier si on est dans le bon répertoire, sinon naviguer
if [ ! -f "composer.json" ]; then
    echo "⚠️ composer.json non trouvé dans le répertoire courant"
    echo "🔍 Recherche du répertoire du projet..."
    
    # Chercher le répertoire bracongostages.bigfive.dev
    if [ -d "bracongostages.bigfive.dev" ]; then
        echo "✅ Répertoire bracongostages.bigfive.dev trouvé, navigation..."
        cd bracongostages.bigfive.dev
        echo "📁 Nouveau répertoire: $(pwd)"
    else
        echo "❌ Répertoire bracongostages.bigfive.dev non trouvé"
        echo "📋 Contenu du répertoire actuel:"
        ls -la
        error_exit "Impossible de trouver le répertoire du projet Laravel"
    fi
fi

# Vérification finale
if [ ! -f "composer.json" ]; then
    error_exit "composer.json non trouvé. Êtes-vous dans le bon répertoire ?"
fi

if [ ! -f "artisan" ]; then
    error_exit "artisan non trouvé. Êtes-vous dans le répertoire Laravel ?"
fi

echo "✅ Répertoire Laravel correct détecté"

# 2. Récupération du code depuis Git (avec gestion d'erreur)
echo "📥 Mise à jour du code..."
if [ -d ".git" ]; then
    if command_exists git; then
        echo "✅ Repository Git détecté, mise à jour..."
        if git pull origin ${FORGE_SITE_BRANCH:-main}; then
            echo "✅ Code mis à jour avec succès"
        else
            echo "⚠️ Échec de la mise à jour Git, continuation avec le code existant"
        fi
    else
        echo "⚠️ Git non installé, continuation avec le code existant"
    fi
else
    echo "⚠️ Aucun repository Git détecté dans ce répertoire"
    echo "ℹ️ Vérifiez que le déploiement s'exécute dans le bon répertoire"
    echo "ℹ️ Ou que le repository a été correctement cloné sur le serveur"
    echo "ℹ️ Continuation avec le code existant..."
fi

# 3. Copie du fichier .env de production
echo "📋 Configuration de l'environnement..."
if [ -f ".env.production" ]; then
    cp .env.production .env
    echo "✅ Fichier .env.production copié"
elif [ -f ".env.example" ]; then
    echo "⚠️ Fichier .env.production manquant, copie de .env.example"
    cp .env.example .env
else
    echo "⚠️ Aucun fichier .env trouvé, utilisation du .env existant"
fi

# 4. Installation Composer (optimisé production)
echo "📦 Installation Composer..."
if command_exists composer; then
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
    echo "✅ Composer installé avec succès"

    # Permissions finales pour vendor
    echo "🔧 Permissions finales pour vendor..."
    chmod -R 755 vendor
    if [ "$USER" = "forge" ]; then
        chown -R forge:forge vendor
    fi
else
    error_exit "Composer non trouvé. Veuillez l'installer."
fi

# 5. Création des répertoires Laravel requis
echo "📁 Création des répertoires..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 6. Permissions Laravel
echo "🔧 Permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 7. Installation Node.js (avec dépendances de dev pour le build)
echo "📦 Installation Node.js..."
if command_exists npm; then
    if [ -f "package-lock.json" ]; then
        # Installer toutes les dépendances (y compris dev) pour pouvoir builder
        npm ci --no-audit
    else
        npm install --no-audit
    fi
    echo "✅ Node.js installé avec succès"
else
    echo "⚠️ npm non trouvé, skip de l'installation Node.js"
fi

# 8. Build des assets
echo "🎨 Build des assets..."
if command_exists npm && [ -f "package.json" ]; then
    if command_exists npx; then
        npx vite build
    else
        # Fallback si npx n'est pas disponible
        ./node_modules/.bin/vite build
    fi
    echo "✅ Assets construits avec succès"
else
    echo "⚠️ Build des assets ignoré (npm non disponible ou package.json manquant)"
fi

# 9. Artisan commands
echo "⚙️ Configuration Laravel..."
php artisan key:generate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Migration base de données
echo "🗄️ Migration BDD..."
php artisan migrate --force --no-interaction

# 11. Lien symbolique storage
echo "🔗 Lien symbolique..."
php artisan storage:link

# 12. Optimisations
echo "⚡ Optimisations..."
php artisan optimize

# 13. Redémarrage services
echo "🔄 Redémarrage..."
if command_exists supervisorctl; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 14. Vérifications finales
echo "✅ Vérifications..."
php artisan about --only=environment

echo "🎉 Déploiement terminé avec succès !"
echo "🌐 https://bracongostages.bigfive.dev"
echo "⚙️ https://bracongostages.bigfive.dev/admin"
