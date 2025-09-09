#!/bin/bash

# Script de déploiement manuel pour Laravel Forge
# À utiliser quand Git ne fonctionne pas correctement
set -e

echo "🚀 Déploiement Manuel BRACONGO Stages..."

# 1. Navigation vers le bon répertoire
echo "🔍 Vérification du répertoire..."
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
        echo ""
        echo "💡 Solutions possibles:"
        echo "   1. Vérifiez que vous êtes dans le bon répertoire sur Forge"
        echo "   2. Copiez manuellement les fichiers depuis votre machine locale"
        echo "   3. Utilisez SCP/SFTP pour transférer les fichiers"
        exit 1
    fi
fi

# Vérification finale
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json non trouvé après navigation"
    echo "📋 Contenu du répertoire:"
    ls -la
    exit 1
fi

echo "✅ Répertoire Laravel correct détecté"

# 2. Copie du fichier .env de production
echo "📋 Configuration de l'environnement..."
if [ -f ".env.production" ]; then
    cp .env.production .env
    echo "✅ Fichier .env.production copié"
elif [ -f ".env.example" ]; then
    echo "⚠️ Fichier .env.production manquant, copie de .env.example"
    cp .env.example .env
    echo "⚠️ N'oubliez pas de configurer les variables d'environnement !"
else
    echo "⚠️ Aucun fichier .env trouvé, utilisation du .env existant"
fi

# 3. Installation Composer (optimisé production)
echo "📦 Installation Composer..."

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

# 4. Création des répertoires Laravel requis
echo "📁 Création des répertoires..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p public/storage

# 5. Permissions Laravel
echo "🔧 Permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# 6. Installation Node.js (avec dépendances de dev pour le build)
echo "📦 Installation Node.js..."
if [ -f "package-lock.json" ]; then
    # Installer toutes les dépendances (y compris dev) pour pouvoir builder
    npm ci --no-audit
else
    npm install --no-audit
fi
echo "✅ Node.js installé avec succès"

# 7. Build des assets
echo "🎨 Build des assets..."
if command -v npx >/dev/null 2>&1; then
    npx vite build
else
    # Fallback si npx n'est pas disponible
    ./node_modules/.bin/vite build
fi
echo "✅ Assets construits avec succès"

# 8. Artisan commands
echo "⚙️ Configuration Laravel..."
php artisan key:generate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Migration base de données
echo "🗄️ Migration BDD..."
php artisan migrate --force --no-interaction

# 10. Lien symbolique storage
echo "🔗 Lien symbolique..."
php artisan storage:link

# 11. Optimisations
echo "⚡ Optimisations..."
php artisan optimize

# 12. Redémarrage services
echo "🔄 Redémarrage..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 13. Vérifications finales
echo "✅ Vérifications..."
php artisan about --only=environment

echo "🎉 Déploiement manuel terminé avec succès !"
echo "🌐 https://bracongostages.bigfive.dev"
echo "⚙️ https://bracongostages.bigfive.dev/admin"
