# 🍺 BRACONGO Stages - Plateforme Complète de Gestion des Stages

Application Laravel complète pour la gestion des candidatures et évaluations de stage chez BRACONGO (Brasseries du Congo).

## 🚀 Fonctionnalités Complètes

### Interface Candidat (Livewire)
- ✅ **Formulaire de candidature multi-étapes** avec design BRACONGO
- ✅ **Upload de documents** (CV, lettre de motivation, certificats, etc.)
- ✅ **Suivi en temps réel** du statut de candidature avec timeline visuelle
- ✅ **Notifications automatiques** par email à chaque changement de statut
- ✅ **Évaluation post-stage** avec formulaire complet et détaillé
- ✅ **Interface responsive** optimisée mobile/desktop

### Administration (Filament)
- ✅ **Panel d'administration moderne** avec couleurs BRACONGO
- ✅ **Gestion complète des candidatures** avec filtres avancés
- ✅ **Actions rapides** : validation/rejet direct depuis la liste
- ✅ **Gestion des évaluations** avec analyse des retours stagiaires
- ✅ **Dashboard avec statistiques** et widgets de performance
- ✅ **Gestion des utilisateurs** et documents
- ✅ **Export de données** et rapports

### Système de Notifications Automatiques
- ✅ **Queues Redis** pour notifications asynchrones
- ✅ **Emails automatiques** à chaque changement de statut
- ✅ **Notification de fin de stage** avec invitation à évaluer
- ✅ **Templates d'emails** personnalisés BRACONGO
- ✅ **Logging complet** des actions et erreurs
- ✅ **Interface Mailpit** pour développement

### Système d'Évaluation
- ✅ **Formulaire d'évaluation complet** post-stage
- ✅ **Métriques de satisfaction** (1-5 étoiles)
- ✅ **Évaluation environnement de travail** (accueil, encadrement, conditions, ambiance)
- ✅ **Analyse des apprentissages** et compétences développées
- ✅ **Suggestions d'amélioration** pour futurs stagiaires
- ✅ **Statistiques d'évaluation** dans l'administration
- ✅ **Widgets de performance** sur le dashboard

### Automatisation
- ✅ **Commandes Artisan** pour notifications automatiques
- ✅ **Planification des tâches** (cron jobs)
- ✅ **Notifications de fin de stage** automatiques
- ✅ **Nettoyage automatique** des fichiers temporaires

## 🛠 Architecture Technique

### Stack Technologique
- **Backend** : Laravel 10 avec Livewire 3 et Filament 3
- **Frontend** : Tailwind CSS avec design système BRACONGO
- **Base de données** : MySQL 8 avec migrations optimisées
- **Cache & Queues** : Redis 7 pour performance
- **Mail** : Mailpit (dev) / SMTP (prod)
- **Containerisation** : Docker & Docker Compose
- **Monitoring** : Logs structurés et métriques

### Structure du Projet
```
├── app/
│   ├── Enums/StatutCandidature.php      # États des candidatures
│   ├── Models/                          # Modèles Eloquent
│   │   ├── Candidature.php              # Modèle principal
│   │   ├── Evaluation.php               # Évaluations post-stage
│   │   ├── User.php                     # Utilisateurs admin
│   │   └── Document.php                 # Documents attachés
│   ├── Livewire/                        # Composants interface candidat
│   │   └── CandidatureForm.php          # Formulaire multi-étapes
│   ├── Filament/                        # Administration
│   │   ├── Resources/                   # Ressources CRUD
│   │   └── Widgets/                     # Widgets dashboard
│   ├── Notifications/                   # Notifications email
│   ├── Jobs/                            # Tâches asynchrones
│   └── Console/Commands/                # Commandes Artisan
├── resources/views/
│   ├── home-modern.blade.php            # Page d'accueil
│   ├── suivi-simple.blade.php           # Suivi candidature
│   ├── evaluation.blade.php             # Évaluation post-stage
│   └── emails/                          # Templates emails
└── database/
    ├── migrations/                      # Structure BDD
    └── seeders/                         # Données de test
```

## 🚀 Installation et Démarrage

### Prérequis
- Docker et Docker Compose
- PHP 8.1+
- Composer
- Node.js 16+

### Installation Rapide
```bash
# Cloner le projet
git clone <repository>
cd bracongostages

# Démarrer les services
docker-compose up -d

# Installer les dépendances
composer install
npm install

# Configuration
cp .env.example .env
# Configurer les variables d'environnement

# Migrations et seeders
php artisan migrate --seed

# Compiler les assets
npm run build

# Démarrer les queues
php artisan queue:work

# Accès
# Frontend: http://localhost
# Admin: http://localhost/admin
# Mailpit: http://localhost:8025
```

### Variables d'Environnement
```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=bracongo_stages
DB_USERNAME=bracongo
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="stages@bracongo.cg"
MAIL_FROM_NAME="BRACONGO Stages"

# Queue
QUEUE_CONNECTION=redis
```

## 📊 Fonctionnalités d'Évaluation

### Métriques Collectées
- **Satisfaction générale** (1-5 étoiles)
- **Recommandation** (Oui/Peut-être/Non)
- **Environnement de travail** :
  - Accueil et intégration
  - Encadrement et suivi
  - Conditions de travail
  - Ambiance de travail
- **Apprentissages** :
  - Compétences développées
  - Réponse aux attentes
  - Aspects enrichissants
- **Suggestions d'amélioration**
- **Contact futur**

### Statistiques Disponibles
- Note moyenne globale
- Taux de satisfaction (≥4/5)
- Taux de recommandation
- Distribution par critères
- Évolution temporelle
- Comparaisons par établissement

## 🔧 Commandes Utiles

### Gestion des Stages
```bash
# Envoyer notifications de fin de stage
php artisan stages:notifier-fin-stage

# Envoyer notifications avec délai personnalisé
php artisan stages:notifier-fin-stage --jours=3

# Voir les logs des notifications
tail -f storage/logs/stages-notifications.log
```

### Maintenance
```bash
# Optimiser l'application
php artisan optimize

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Redémarrer les queues
php artisan queue:restart
```

### Données de Test
```bash
# Créer des données de test complètes
php artisan db:seed

# Créer seulement des évaluations
php artisan db:seed --class=EvaluationSeeder
```

## 📈 Monitoring et Logs

### Logs Disponibles
- `storage/logs/laravel.log` - Logs généraux
- `storage/logs/stages-notifications.log` - Notifications
- `storage/logs/queue.log` - Queues Redis

### Métriques à Surveiller
- Taux de conversion candidatures → validations
- Temps moyen de traitement
- Taux de satisfaction des évaluations
- Performance des queues
- Erreurs d'envoi d'emails

## 🎨 Personnalisation

### Couleurs BRACONGO
```css
/* Orange principal */
--color-orange: #f97316;
--color-orange-dark: #ea580c;

/* Rouge secondaire */
--color-red: #dc2626;
--color-red-dark: #b91c1c;

/* Gradients */
--gradient-primary: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
```

### Templates d'Emails
- Templates personnalisés dans `resources/views/emails/`
- Variables disponibles : `$candidature`, `$evaluation`
- Design responsive avec couleurs BRACONGO

## 🔒 Sécurité

### Mesures Implémentées
- Validation stricte des données
- Protection CSRF
- Sanitisation des uploads
- Logs de sécurité
- Rate limiting
- Authentification Filament

### Permissions
- Admin : Accès complet
- Candidats : Lecture seule de leurs données
- API : Authentification requise

## 🚀 Déploiement

### Production
```bash
# Optimiser pour la production
composer install --optimize-autoloader --no-dev
npm run build

# Migrations
php artisan migrate --force

# Démarrer les services
php artisan queue:work --daemon
php artisan schedule:work
```

### Variables de Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stages.bracongo.cg

# Mail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=stages@bracongo.cg
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Cache Redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 📞 Support

### Contact
- **Email** : stages@bracongo.cg
- **Téléphone** : +242 01 234 5678
- **Site web** : https://www.bracongo.cg

### Documentation
- Documentation complète disponible dans `/docs`
- Guide utilisateur admin
- Guide développeur
- API documentation

---

**BRACONGO - Brasseries du Congo**  
*Votre partenaire pour des stages enrichissants* 🍺
