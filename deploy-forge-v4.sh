#!/bin/bash

# Script de déploiement BRACONGO Stages pour Laravel Forge (Version v4)
# Inclut: poste_souhaite, assets Filament, corrections récentes
set -e

echo "🚀 Déploiement BRACONGO Stages (Version v4 - $(date '+%Y-%m-%d %H:%M'))..."

# Variables d'environnement
FORGE_SITE_PATH=${FORGE_SITE_PATH:-/home/forge/bracongostages.bigfive.dev}
FORGE_SITE_USER=${FORGE_SITE_USER:-forge}
FORGE_SITE_BRANCH=${FORGE_SITE_BRANCH:-main}

# 0. NAVIGATION VERS LE RÉPERTOIRE DU SITE (CRITIQUE!)
echo "📂 Navigation vers $FORGE_SITE_PATH..."
cd $FORGE_SITE_PATH

# 1. Mise à jour du code depuis Git
echo "📥 Mise à jour du code..."
git fetch origin
git reset --hard origin/$FORGE_SITE_BRANCH
git pull origin $FORGE_SITE_BRANCH

# 2. CRÉATION DES RÉPERTOIRES LARAVEL AVANT TOUTE AUTRE OPÉRATION
echo "📁 Création des répertoires Laravel requis..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p storage/app/public/documents
mkdir -p storage/app/public/documents_candidat
mkdir -p public/storage
mkdir -p public/css/filament
mkdir -p public/js/filament

# 3. CONFIGURATION DES PERMISSIONS AVANT COMPOSER
echo "🔧 Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage bootstrap/cache
fi

# Vérification que bootstrap/cache est accessible en écriture
if [ ! -w bootstrap/cache ]; then
    echo "❌ Erreur: bootstrap/cache n'est pas accessible en écriture"
    exit 1
fi
echo "✅ Répertoires Laravel créés et permissions configurées"

# 4. Configuration de l'environnement
echo "📋 Configuration de l'environnement..."
if [ ! -f .env ]; then
    cat > .env << 'EOL'
APP_NAME="BRACONGO Stages"
APP_ENV=production
APP_KEY=base64:+DiT/dEhYPOyDTCYA3gPRrRoH4ts/a0uoxhRhO48zGs=
APP_DEBUG=false
APP_URL=https://bracongostages.bigfive.dev

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forge
DB_USERNAME=forge
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bracongostages.bigfive.dev"
MAIL_FROM_NAME="${APP_NAME}"

FILAMENT_FILESYSTEM_DISK=public
EOL
    echo "✅ Fichier .env créé"
else
    echo "✅ Fichier .env existe déjà"
fi

# 5. Installation des dépendances Composer (AVEC les répertoires créés)
echo "📦 Installation des dépendances Composer..."
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 6. Installation des dépendances Node.js (production uniquement)
echo "📦 Installation des dépendances Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --no-audit
else
    npm install --no-audit
fi

# 7. Build des assets avec Vite
echo "🎨 Build des assets frontend..."
npm run build

# 8. PUBLICATION DES ASSETS FILAMENT (IMPORTANT!)
echo "🎨 Publication des assets Filament..."
php artisan filament:assets

# 9. Lien symbolique storage (AVANT les caches)
echo "🔗 Lien symbolique storage..."
php artisan storage:link --force

# 10. Migration de la base de données
echo "🗄️ Migration base de données..."
php artisan migrate --force --no-interaction

# 11. Migration des données niveau_etude (convertir labels en clés)
echo "🔄 Migration des données niveau_etude..."
php artisan tinker --execute="
\$mapping = [
    'École Secondaire' => 'ecole_secondaire',
    'Bac+1' => 'bac_1',
    'Bac+2' => 'bac_2',
    'Licence' => 'bac_3',
    'Bac+3' => 'bac_3',
    'Bac+4' => 'bac_4',
    'Master' => 'bac_5',
    'Bac+5' => 'bac_5',
    'Doctorat' => 'doctorat',
];
App\Models\Candidat::all()->each(function(\$c) use (\$mapping) {
    if (isset(\$mapping[\$c->niveau_etude])) {
        \$c->update(['niveau_etude' => \$mapping[\$c->niveau_etude]]);
    }
});
echo 'Migration niveau_etude terminée';
" 2>/dev/null || echo "Migration niveau_etude ignorée"

# 12. Nettoyage complet des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 13. Configuration Laravel (régénération des caches)
echo "⚙️ Configuration Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 14. Optimisations finales
echo "⚡ Optimisations..."
php artisan optimize

# 15. Redémarrage des services
echo "🔄 Redémarrage des services..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart all 2>/dev/null || true
fi

# 16. Configuration finale des permissions (sécurité)
echo "🔧 Vérification finale des permissions..."
chown -R $FORGE_SITE_USER:$FORGE_SITE_USER storage bootstrap/cache public/css public/js
chmod -R 775 storage bootstrap/cache
chmod -R 755 public/css public/js

# 17. Vérifications finales
echo "✅ Vérifications finales..."
php artisan about --only=environment

# 18. Vérification du champ poste_souhaite
echo "🔍 Vérification du champ poste_souhaite..."
php artisan tinker --execute="
if (Schema::hasColumn('candidatures', 'poste_souhaite')) {
    echo '✅ Champ poste_souhaite existe dans la table candidatures';
} else {
    echo '❌ Champ poste_souhaite MANQUANT!';
}
" 2>/dev/null || echo "Vérification ignorée"

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "🎉 Déploiement terminé avec succès !"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "🌐 Site: https://bracongostages.bigfive.dev"
echo "⚙️ Admin: https://bracongostages.bigfive.dev/admin"
echo ""
echo "📧 Comptes par défaut :"
echo "   • Admin: admin@bracongo.com / BracongoAdmin2024!"
echo "   • DG: dg@bracongo.com / BracongoDG2024!"
echo ""
echo "📝 Modifications incluses dans ce déploiement :"
echo "   • Champ 'Poste souhaité' dans le formulaire de candidature"
echo "   • Pré-remplissage des directions depuis l'opportunité sélectionnée"
echo "   • Certificat de scolarité obligatoire"
echo "   • Lettre de motivation limitée à 2 MB"
echo "   • Correction du pré-remplissage niveau_etude/faculte"
echo "   • Assets Filament publiés (CSS/JS admin)"
echo "   • Correction Livewire 404"
echo ""
echo "🍺 BRACONGO Stages est prêt pour les candidatures !"
