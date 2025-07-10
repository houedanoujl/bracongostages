# 🍺 BRACONGO Stages - Plateforme de Gestion des Stages

Application Laravel complète pour la gestion des candidatures de stage chez BRACONGO (Brasseries du Congo).

## 🚀 Fonctionnalités

### Interface Candidat (Livewire)
- ✅ Formulaire de candidature multi-étapes avec design BRACONGO
- ✅ Upload de documents (CV, lettre de motivation, certificats)
- ✅ Suivi en temps réel du statut de candidature avec timeline
- ✅ Notifications automatiques par email

### Administration (Filament)
- ✅ Panel d'administration moderne avec couleurs BRACONGO
- ✅ Gestion complète des candidatures avec filtres avancés
- ✅ Actions rapides : validation/rejet direct depuis la liste
- ✅ Gestion des utilisateurs et documents
- ✅ Dashboard avec statistiques et widgets

### Système de Notifications
- ✅ Queues Redis pour notifications asynchrones
- ✅ Emails automatiques à chaque changement de statut
- ✅ Logging complet des actions
- ✅ Interface Mailpit pour développement

## 🛠 Architecture Technique

### Stack
- **Backend** : Laravel 10 avec Livewire 3 et Filament 3
- **Frontend** : Tailwind CSS avec design système BRACONGO
- **Base de données** : MySQL 8
- **Cache & Queues** : Redis 7
- **Mail** : Mailpit (dev) / SMTP (prod)
- **Containerisation** : Docker & Docker Compose

### Structure du Projet
```
├── app/
│   ├── Enums/StatutCandidature.php      # États des candidatures
│   ├── Models/                          # Modèles Eloquent
│   │   ├── Candidature.php              # Modèle principal
│   │   ├── User.php                     # Utilisateurs admin
│   │   └── Document.php                 # Documents attachés
│   ├── Livewire/                        # Composants interface candidat
│   │   ├── CandidatureForm.php          # Formulaire multi-étapes
│   │   └── SuiviCandidature.php         # Suivi avec timeline
│   ├── Filament/                        # Administration
│   │   └── Resources/                   # Resources CRUD
│   ├── Jobs/                            # Jobs asynchrones
│   │   └── SendCandidatureNotification.php
│   └── Notifications/                   # Notifications email
│       └── CandidatureStatusChanged.php
├── resources/views/                     # Vues Blade
│   ├── layouts/app.blade.php            # Layout principal BRACONGO
│   └── livewire/                        # Vues Livewire
└── docker/                              # Configuration Docker
```

## 🐳 Installation & Déploiement

### Prérequis
```bash
# Installer Docker et Docker Compose
sudo apt update
sudo apt install docker.io docker-compose
sudo usermod -aG docker $USER
sudo systemctl enable docker
sudo systemctl start docker
```

### 1. Cloner et configurer
```bash
git clone <repository>
cd bracongostages

# Copier et configurer l'environnement
cp .env.example .env
# Éditer .env si nécessaire
```

### 2. Lancer avec Docker
```bash
# Construire et démarrer tous les services
docker-compose up -d

# Installer les dépendances PHP
docker-compose exec app composer install

# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Migrer la base de données
docker-compose exec app php artisan migrate

# Créer un utilisateur admin
docker-compose exec app php artisan make:filament-user

# Installer les dépendances NPM et compiler les assets
docker-compose exec app npm install
docker-compose exec app npm run build
```

### 3. Démarrer les workers de queue
```bash
# Worker pour les notifications
docker-compose exec app php artisan queue:work redis --queue=notifications

# Worker général
docker-compose exec app php artisan queue:work redis
```

## 🌐 Accès aux Services

- **Application** : http://localhost:8000
- **Administration** : http://localhost:8000/admin
- **Mailpit** : http://localhost:8025 (emails de développement)
- **Base de données** : localhost:3306

## 📊 Utilisation

### Interface Candidat
1. **Candidature** : `/candidature` - Formulaire en 4 étapes
2. **Suivi** : `/candidature/suivi/{code}` - Timeline du processus

### Administration
1. **Connexion** : `/admin` avec utilisateur créé
2. **Candidatures** : Gestion complète avec actions rapides
3. **Dashboard** : Statistiques et vue d'ensemble

### Processus de Candidature
1. **Non traité** → Candidature reçue
2. **Analyse dossier** → Dossier en cours d'analyse
3. **Attente test** → Convocation au test technique
4. **Attente résultats** → Analyse des résultats
5. **Attente affectation** → Attribution de la direction
6. **Validé** → Stage confirmé avec dates
7. **Rejeté** → Candidature non retenue

## 🎨 Design System BRACONGO

### Couleurs Principales
- **Orange BRACONGO** : `#f97316` (Tailwind orange-500)
- **Dégradés** : orange-600 à orange-500
- **Secondaires** : Gris neutres et couleurs d'état

### Composants
- Navigation avec logo et branding BRACONGO
- Formulaires avec validation temps réel
- Timeline interactive pour le suivi
- Badges de statut colorés
- Notifications toast et emails

## 🔧 Configuration Avancée

### Variables d'Environnement Importantes
```env
# Application
APP_NAME="BRACONGO Stages"
APP_URL=http://localhost:8000

# Base de données
DB_HOST=mysql
DB_DATABASE=bracongo_stages
DB_USERNAME=bracongo_user
DB_PASSWORD=bracongo_pass_2024

# Redis pour cache et queues
REDIS_HOST=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_FROM_ADDRESS="stages@bracongo.cd"
MAIL_FROM_NAME="BRACONGO Stages"
```

### Commandes Utiles
```bash
# Monitoring des queues
docker-compose exec app php artisan queue:monitor

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Logs en temps réel
docker-compose logs -f app

# Backup base de données
docker-compose exec mysql mysqldump -u bracongo_user -p bracongo_stages > backup.sql
```

## 🚀 Déploiement Production

### 1. Optimisations Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2. Variables d'environnement
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 3. Monitoring
- Configurez Laravel Horizon pour Redis
- Supervisord pour les workers de queue
- Logs centralisés
- Monitoring des performances

## 📧 Configuration Email Production

### SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Services recommandés
- **Mailgun** : Simple et fiable
- **SendGrid** : Excellent deliverability
- **Amazon SES** : Économique pour gros volumes

## 🔐 Sécurité

### Authentification
- Filament avec protection CSRF
- Middleware d'authentification
- Validation côté serveur complète

### Uploads
- Validation des types de fichiers
- Limitation de taille (2MB max)
- Stockage sécurisé

### Base de données
- Migrations avec contraintes
- Relations définies
- Indexation pour performance

## 📝 Développement

### Tests
```bash
# Tests unitaires
php artisan test

# Tests de navigation
php artisan test --filter CandidatureTest
```

### Debugging
```bash
# Laravel Telescope (optionnel)
composer require laravel/telescope
php artisan telescope:install
```

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature
3. Commiter les changements
4. Pusher vers la branche
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est la propriété de BRACONGO (Brasseries du Congo).

---

**Développé avec ❤️ pour BRACONGO** 🍺
