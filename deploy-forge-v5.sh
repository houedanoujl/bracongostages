#!/bin/bash

# Script de déploiement BRACONGO Stages pour Laravel Forge (Version v5)
# Inclut: Mailtrap emails, assets Filament, composer.lock, corrections récentes
set -e

echo "🚀 Déploiement BRACONGO Stages (Version v5 - $(date '+%Y-%m-%d %H:%M'))..."

# Variables d'environnement
FORGE_SITE_PATH=${FORGE_SITE_PATH:-/home/forge/bracongostages.bigfive.dev}
FORGE_SITE_USER=${FORGE_SITE_USER:-forge}
FORGE_SITE_BRANCH=${FORGE_SITE_BRANCH:-main}
export COMPOSER_ALLOW_SUPERUSER=1

# 0. NAVIGATION VERS LE RÉPERTOIRE DU SITE (CRITIQUE!)
echo "📂 Navigation vers $FORGE_SITE_PATH..."
cd $FORGE_SITE_PATH

# Vérification du répertoire Laravel
if [ ! -f "artisan" ] || [ ! -f "composer.json" ]; then
    echo "❌ Ce n'est pas un répertoire Laravel valide: $(pwd)"
    exit 1
fi
echo "✅ Répertoire Laravel détecté: $(pwd)"

# 1. Mise à jour du code depuis Git
echo "📥 Mise à jour du code..."
git fetch origin
git reset --hard origin/$FORGE_SITE_BRANCH

# 2. CRÉATION DES RÉPERTOIRES LARAVEL
echo "📁 Création des répertoires Laravel requis..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p storage/app/public/documents
mkdir -p storage/app/public/documents_candidat
mkdir -p public/storage
mkdir -p public/css/filament
mkdir -p public/js/filament

# 3. Nettoyage des fichiers temporaires (peuvent appartenir à root)
echo "🧹 Nettoyage des fichiers temporaires..."
rm -rf storage/app/livewire-tmp/* 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true

# 3b. CONFIGURATION DES PERMISSIONS AVANT COMPOSER
# Note: || true car certains fichiers peuvent appartenir à root
echo "🔧 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache 2>/dev/null || true
# vendor/ doit aussi appartenir à forge pour que composer puisse écrire
if [ -d "vendor" ]; then
    chown -R $FORGE_SITE_USER:$FORGE_SITE_USER vendor 2>/dev/null || true
fi

# 4. Installation Composer (composer.lock est commité dans le repo)
echo "📦 Installation des dépendances Composer..."
if [ -n "$FORGE_COMPOSER" ]; then
    $FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
else
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
fi

# 4b. Regénérer le classmap pour inclure tous les composants
echo "📦 Régénération du classmap Composer..."
composer dump-autoload --optimize 2>/dev/null || true

# 4c. Découverte explicite des packages (inclut MailtrapSdkProvider)
echo "📦 Découverte des packages Laravel..."
php artisan package:discover --ansi || true

# 5. Installation Node.js
echo "📦 Installation des dépendances Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --no-audit
else
    npm install --no-audit
fi

# 6. Build des assets frontend
echo "🎨 Build des assets frontend..."
npm run build

# 7. PUBLICATION DES ASSETS FILAMENT (IMPORTANT!)
echo "🎨 Publication des assets Filament..."
php artisan filament:assets

# 8. Lien symbolique storage
echo "🔗 Lien symbolique storage..."
php artisan storage:link --force

# 9. Migration de la base de données
echo "🗄️ Migration base de données..."
php artisan migrate --force --no-interaction

# 9b. Seeding des templates email (insère uniquement si absents)
echo "📧 Vérification des templates email..."
php artisan db:seed --class=EmailTemplateSeeder --force --no-interaction 2>/dev/null || echo "⚠️ Seeding templates email ignoré (déjà présents ou erreur)"

# 10. Nettoyage complet des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 11. Régénération des caches
echo "⚙️ Régénération des caches Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 11b. Cache des composants Filament (RelationManagers, Resources, Pages, Widgets)
echo "⚙️ Optimisation des composants Filament..."
php artisan filament:optimize 2>/dev/null || true

# 12. Permissions finales
echo "🔧 Permissions finales..."
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache public/css public/js 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chmod -R 755 public/css public/js 2>/dev/null || true

# 13. Redémarrage services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 14. Vérification email Mailtrap
echo "📧 Vérification configuration email..."
php artisan tinker --execute="
try {
    \$mailer = config('mail.default');
    \$transport = config(\"mail.mailers.{\$mailer}.transport\");
    \$apiKey = config(\"mail.mailers.{\$mailer}.apiKey\") ?: env('MAILTRAP_API_KEY');
    echo \"Mailer: {\$mailer}, Transport: {\$transport}\n\";
    if (\$apiKey) {
        echo '✅ Clé API Mailtrap configurée (' . substr(\$apiKey, 0, 8) . '...)';
    } else {
        echo '⚠️ Clé API Mailtrap NON configurée - vérifier .env';
    }
} catch (\Exception \$e) {
    echo '❌ Erreur config email: ' . \$e->getMessage();
}
" 2>/dev/null || echo "⚠️ Vérification email ignorée"

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "🎉 Déploiement terminé avec succès !"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"
echo ""
echo "📝 Nouveautés v5 :"
echo "   • 📧 Emails Mailtrap (envoi automatique sur changement de statut)"
echo "   • 📄 Documents candidat visibles sur le profil"
echo "   • 👤 Gestion des tuteurs dans l'admin"
echo "   • ✏️ Éditeur WYSIWYG pour les templates email"
echo "   • 🔒 composer.lock commité pour déploiement fiable"
echo ""
echo "🍺 BRACONGO Stages est prêt !"
