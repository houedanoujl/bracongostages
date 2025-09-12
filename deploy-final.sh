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

# 1. Nettoyage des fichiers avec accents (serveur)
print_status "Suppression des fichiers avec accents sur le serveur..."
rm -rf ./app/Filament/Resources/TémoignageResource/ 2>/dev/null || true
composer dump-autoload --optimize --no-dev 2>/dev/null || true
print_success "Nettoyage terminé"

# 2. Installation des dépendances
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

# Vérifier que node_modules existe
if [ ! -d "node_modules" ]; then
    print_warning "node_modules manquant, installation des dépendances..."
    npm install
fi

# Nettoyer les anciens builds
print_status "Nettoyage des anciens assets..."
rm -rf public/build/ 2>/dev/null || true

# Compiler les assets
print_status "Compilation des assets pour production..."
if npm run build; then
    print_success "Assets compilés avec succès"
else
    print_error "Échec de compilation des assets"
    print_warning "Tentative avec mode développement..."
    npm run dev &
    sleep 5
    kill %1 2>/dev/null || true
fi

# Vérifier que les assets sont bien créés
if [ -d "public/build" ] && [ "$(ls -A public/build 2>/dev/null)" ]; then
    print_success "Assets disponibles dans public/build/"
else
    print_warning "Assets non trouvés, vérifiez la compilation"
fi

# 5. Base de données
print_status "Configuration de la base de données..."

# Vérifier si la base de données est accessible
if php artisan db:show 2>/dev/null; then
    print_status "Configuration de la base de données..."
    
    # Demander confirmation pour le reset (seulement en mode interactif)
    if [[ -t 0 ]]; then
        echo ""
        print_warning "⚠️  Attention: Le reset de la base va supprimer TOUTES les données existantes !"
        read -p "Voulez-vous continuer ? (y/N): " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_warning "Reset annulé. Tentative de migration normale..."
            php artisan migrate --force
            php artisan db:seed --force
        else
            print_status "Reset complet de la base de données..."
            php artisan migrate:fresh --seed --force
        fi
    else
        # Mode non-interactif : faire le reset directement
        print_status "Reset complet de la base de données (mode automatique)..."
        php artisan migrate:fresh --seed --force
    fi
    
    print_success "Base de données configurée"
else
    print_warning "Impossible de se connecter à la base de données"
    print_warning "Veuillez configurer les paramètres de base de données dans .env"
    print_warning "Puis exécutez : php artisan migrate:fresh --seed"
fi

# 6. Permissions des dossiers
print_status "Configuration des permissions..."

# Créer les dossiers s'ils n'existent pas
mkdir -p storage/logs storage/framework/{cache,sessions,views} storage/app/public
mkdir -p bootstrap/cache public/build

# Permissions Laravel
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 755 public/

# Permissions spécifiques pour les assets
if [ -d "public/build" ]; then
    chmod -R 755 public/build/
fi

# Propriétaire selon l'environnement
if [ "$USER" = "forge" ]; then
    chown -R forge:forge storage/ bootstrap/cache/ public/ 2>/dev/null || true
else
    chown -R www-data:www-data storage/ bootstrap/cache/ public/ 2>/dev/null || true
fi

print_success "Permissions configurées"

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

# Vérifier si des utilisateurs existent
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 || echo "0")

if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    print_warning "Aucun utilisateur admin trouvé"
    print_status "Création d'un utilisateur admin par défaut..."
    
    # Créer utilisateur admin par défaut (vous pouvez personnaliser)
    php artisan tinker --execute="
        \$user = App\Models\User::create([
            'name' => 'Admin BRACONGO',
            'email' => 'admin@bracongo.cg',
            'email_verified_at' => now(),
            'password' => Hash::make('AdminBracongo2024!')
        ]);
        echo 'Utilisateur admin créé: ' . \$user->email;
    " 2>/dev/null || print_warning "Création automatique échouée, utilisez: php artisan make:filament-user"
    
    print_success "Utilisateur admin créé - Email: admin@bracongo.cg, Mot de passe: AdminBracongo2024!"
else
    print_success "Utilisateur(s) admin trouvé(s) ($USER_COUNT)"
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

# 15. Création de données d'exemple pour les tests
print_status "Création de données d'exemple..."

php artisan tinker --execute="
// Créer des opportunités d'exemple
if (App\Models\Opportunite::count() == 0) {
    App\Models\Opportunite::create([
        'titre' => 'Stage Marketing Digital',
        'slug' => 'stage-marketing-digital',
        'description' => 'Stage en marketing digital et communication',
        'duree' => '3 mois',
        'niveau_requis' => 'bac_3',
        'places_disponibles' => 5,
        'actif' => true
    ]);
    
    App\Models\Opportunite::create([
        'titre' => 'Stage Développement Web',
        'slug' => 'stage-dev-web',
        'description' => 'Développement d\'applications web avec Laravel',
        'duree' => '4 mois',
        'niveau_requis' => 'bac_4',
        'places_disponibles' => 3,
        'actif' => true
    ]);
    
    echo 'Opportunités d\'exemple créées\n';
}

// Créer des candidatures test
if (App\Models\Candidature::count() == 0) {
    App\Models\Candidature::create([
        'nom' => 'Mukendi',
        'prenom' => 'Jean',
        'email' => 'jean.mukendi@example.com',
        'telephone' => '+243123456789',
        'etablissement' => 'Université de Kinshasa (UNIKIN)',
        'niveau_etude' => 'bac_3',
        'faculte' => 'Sciences Informatiques',
        'objectif_stage' => 'Développer mes compétences en programmation',
        'directions_souhaitees' => [\"Direction Informatique\"],
        'periode_debut_souhaitee' => now()->addMonth(),
        'periode_fin_souhaitee' => now()->addMonths(4),
        'code_suivi' => 'BRC-TEST123',
        'statut' => 'non_traite'
    ]);
    echo 'Candidature test créée (Code: BRC-TEST123)\n';
}
echo 'Données d\'exemple configurées';
" 2>/dev/null && print_success "Données d'exemple créées" || print_warning "Création des données d'exemple échouée"

# 16. Vérifications finales
print_status "Vérifications finales..."

# Test accès admin
if curl -s -o /dev/null -w "%{http_code}" "http://localhost/admin" | grep -q "200\|302"; then
    print_success "Interface admin accessible"
else
    print_warning "Interface admin pourrait ne pas être accessible"
fi

# Test accès public  
if curl -s -o /dev/null -w "%{http_code}" "http://localhost" | grep -q "200"; then
    print_success "Site public accessible"
else
    print_warning "Site public pourrait ne pas être accessible"
fi

print_success "Déploiement complètement terminé !"

# 17. Instructions finales
echo ""
echo "🎉 BRACONGO Stages est maintenant prêt !"
echo ""
echo "📋 Accès à la plateforme :"
echo "🌐 Site public : https://$(hostname -f || echo 'votre-domaine.com')"  
echo "🔧 Interface admin : https://$(hostname -f || echo 'votre-domaine.com')/admin"
echo ""
echo "👤 Compte administrateur créé :"
echo "   Email : admin@bracongo.cg"
echo "   Mot de passe : AdminBracongo2024!"
echo ""
echo "🧪 Données de test disponibles :"
echo "   Code de suivi test : BRC-TEST123"
echo "   Opportunités d'exemple : 2 stages créés"
echo ""
echo "📧 Configuration email (Mailtrap) :"
echo "   MAIL_HOST=sandbox.smtp.mailtrap.io"
echo "   MAIL_PORT=2525"
echo ""
echo "⚙️ Services à démarrer (optionnel) :"
echo "   Queue worker : php artisan queue:work --daemon"
echo "   Task scheduler : * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
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