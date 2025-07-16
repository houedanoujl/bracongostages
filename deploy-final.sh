#!/bin/bash

# 🍺 BRACONGO Stages - Script de Déploiement Final
# Ce script finalise l'installation et la configuration de l'application

set -e  # Arrêter en cas d'erreur

echo "🚀 Déploiement final de BRACONGO Stages..."

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

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "composer.json" ]; then
    print_error "Ce script doit être exécuté depuis la racine du projet BRACONGO Stages"
    exit 1
fi

print_status "Vérification de l'environnement..."

# Vérifier les prérequis
if ! command -v php &> /dev/null; then
    print_error "PHP n'est pas installé"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    print_error "Composer n'est pas installé"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    print_error "Node.js/npm n'est pas installé"
    exit 1
fi

print_success "Prérequis vérifiés"

# 1. Installation des dépendances
print_status "Installation des dépendances PHP..."
composer install --optimize-autoloader --no-dev

print_status "Installation des dépendances Node.js..."
npm install

# 2. Configuration de l'environnement
if [ ! -f ".env" ]; then
    print_status "Création du fichier .env..."
    cp .env.example .env
    
    # Générer la clé d'application
    php artisan key:generate
    
    print_warning "Veuillez configurer le fichier .env avec vos paramètres de base de données et email"
else
    print_success "Fichier .env déjà présent"
fi

# 3. Optimisation de Laravel
print_status "Optimisation de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Compilation des assets
print_status "Compilation des assets..."
npm run build

# 5. Base de données
print_status "Configuration de la base de données..."

# Vérifier si la base de données est accessible
if php artisan db:show 2>/dev/null; then
    print_status "Migration de la base de données..."
    php artisan migrate --force
    
    print_status "Création des données de test..."
    php artisan db:seed --force
    
    print_success "Base de données configurée"
else
    print_warning "Impossible de se connecter à la base de données"
    print_warning "Veuillez configurer les paramètres de base de données dans .env"
    print_warning "Puis exécutez : php artisan migrate --seed"
fi

# 6. Permissions des dossiers
print_status "Configuration des permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || true

# 7. Création des liens symboliques
print_status "Création des liens symboliques..."
php artisan storage:link

# 8. Nettoyage du cache
print_status "Nettoyage du cache..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 9. Vérification des services
print_status "Vérification des services..."

# Vérifier Redis
if command -v redis-cli &> /dev/null; then
    if redis-cli ping &> /dev/null; then
        print_success "Redis est accessible"
    else
        print_warning "Redis n'est pas accessible"
    fi
else
    print_warning "Redis n'est pas installé"
fi

# 10. Configuration des queues
print_status "Configuration des queues..."
php artisan queue:restart

# 11. Création d'un utilisateur admin si nécessaire
print_status "Vérification de l'utilisateur admin..."
if ! php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -q "[1-9]"; then
    print_warning "Aucun utilisateur admin trouvé"
    print_warning "Créez un utilisateur admin avec : php artisan make:filament-user"
else
    print_success "Utilisateur(s) admin trouvé(s)"
fi

# 12. Test de l'application
print_status "Test de l'application..."

# Vérifier que l'application répond
if php artisan route:list | grep -q "candidature"; then
    print_success "Routes de candidature configurées"
else
    print_error "Routes de candidature manquantes"
fi

# 13. Configuration du cron pour les tâches planifiées
print_status "Configuration des tâches planifiées..."

# Ajouter les tâches cron si elles n'existent pas déjà
CRON_JOB="* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
if ! crontab -l 2>/dev/null | grep -q "schedule:run"; then
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    print_success "Tâche cron ajoutée"
else
    print_success "Tâche cron déjà configurée"
fi

# 14. Finalisation
print_status "Finalisation..."

# Créer un fichier de statut
cat > .deployed << EOF
# BRACONGO Stages - Statut de déploiement
Déployé le: $(date)
Version: $(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
PHP: $(php -v | head -n1)
Node: $(node -v)
Composer: $(composer --version | head -n1)
EOF

print_success "Déploiement terminé avec succès !"

# 15. Instructions finales
echo ""
echo "🎉 BRACONGO Stages est maintenant prêt !"
echo ""
echo "📋 Prochaines étapes :"
echo "1. Configurez votre serveur web (Apache/Nginx)"
echo "2. Configurez les paramètres SMTP dans .env"
echo "3. Créez un utilisateur admin : php artisan make:filament-user"
echo "4. Démarrez les workers de queue : php artisan queue:work"
echo ""
echo "🌐 Accès :"
echo "- Frontend : http://votre-domaine.com"
echo "- Admin : http://votre-domaine.com/admin"
echo "- Mailpit (dev) : http://localhost:8025"
echo ""
echo "📊 Commandes utiles :"
echo "- Voir les logs : tail -f storage/logs/laravel.log"
echo "- Redémarrer les queues : php artisan queue:restart"
echo "- Optimiser : php artisan optimize"
echo "- Notifications fin de stage : php artisan stages:notifier-fin-stage"
echo ""
echo "🔧 Support :"
echo "- Email : stages@bracongo.cg"
echo "- Documentation : README.md"
echo ""
print_success "Déploiement finalisé avec succès ! 🍺" 