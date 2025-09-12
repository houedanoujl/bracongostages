# 📋 Documentation BRACONGO Stages

## 🍺 Présentation de la Plateforme

**BRACONGO Stages** est une plateforme web dédiée à la gestion des stages au sein de la société BRACONGO. Elle permet aux étudiants de postuler pour des opportunités de stage et aux administrateurs de gérer efficacement les candidatures.

### 🎯 Objectifs Principaux
- **Simplifier** le processus de candidature pour les étudiants
- **Centraliser** la gestion des stages pour BRACONGO
- **Automatiser** le suivi des candidatures et notifications
- **Améliorer** la communication entre candidats et entreprise

---

## 🌐 Accès à la Plateforme

### URLs d'accès
- **Site public** : `https://bracongostages.bigfive.dev`
- **Interface d'administration** : `https://bracongostages.bigfive.dev/admin`

### 👤 Comptes d'Accès Admin

**Compte Super Administrateur :**
- **Username** : admin@bracongo.com
- **pssword** :  BracongoAdmin2024! !
- **Permissions** : Accès complet à toutes les fonctionnalités

---

## 🏗️ Architecture de la Plateforme

### Technologies Utilisées
- **Framework** : Laravel 10
- **Interface Admin** : Filament 3
- **Base de données** : MySQL
- **Frontend** : Blade Templates + Livewire
- **Stockage** : Système de fichiers local
- **Queue** : Redis (pour les notifications)

### Composants Principaux
1. **Module Candidatures** - Gestion des demandes de stage
2. **Module Documents** - Téléchargement et stockage des pièces
3. **Module Évaluations** - Système d'évaluation post-stage
4. **Module Opportunités** - Gestion des offres de stage
5. **Module Configuration** - Paramétrage des listes déroulantes

---

## 👥 Fonctionnalités Utilisateurs

### 🎓 Pour les Candidats (Frontend Public)

#### **1. Candidature en Ligne**
- **Route** : `/candidature`
- **Formulaire complet** avec :
  - Informations personnelles (nom, prénom, email, téléphone)
  - Informations académiques (établissement, niveau d'études, faculté)
  - Préférences de stage (directions, durée, objectifs)
  - Téléchargement de documents (CV, lettre de motivation, etc.)

#### **2. Suivi de Candidature**
- **Route** : `/suivi`
- **Code de suivi unique** : Format `BRC-XXXXXXXX`
- **Statuts disponibles** :
  - ⏳ `Non traité` - Candidature reçue
  - 👀 `En cours d'examen` - Dossier en cours d'évaluation
  - ✅ `Validé` - Candidature acceptée avec dates de stage
  - ❌ `Rejeté` - Candidature refusée avec motif

#### **3. Espace Personnel Candidat**
- **Inscription** : `/candidat/register`
- **Connexion** : `/candidat/login`
- **Tableau de bord** : `/candidat/dashboard`
- **Fonctionnalités** :
  - Gestion du profil personnel
  - Historique des candidatures
  - Téléchargement de documents
  - Changement de mot de passe

#### **4. Pages Informatives**
- **Accueil moderne** : `/` - Design moderne BRACONGO
- **Opportunités** : `/opportunites` - Liste des stages disponibles
- **Contact** : `/contact` - Formulaire de contact

### 📊 Données Collectées
- **Informations personnelles** : Nom, prénom, email, téléphone
- **Informations académiques** : Établissement, niveau, faculté
- **Préférences** : Directions souhaitées, période, objectifs
- **Documents** : CV, lettre de motivation, relevés de notes

---

## 🔧 Administration (Backend Filament)

### 📋 Modules d'Administration

#### **1. Gestion des Candidatures**
**Resource** : `CandidatureResource`
- **Vue d'ensemble** : Tableau complet avec filtres avancés
- **Actions disponibles** :
  - ✅ **Valider** avec définition des dates de stage
  - ❌ **Rejeter** avec saisie du motif
  - 👀 **Examiner** - Marquer en cours d'examen
  - 📄 **Voir les documents** associés
  - 💬 **Ajouter des commentaires internes**

**Filtres disponibles** :
- Par statut (Non traité, En examen, Validé, Rejeté)
- Par établissement
- Par niveau d'étude
- Par direction souhaitée
- Par période de candidature

**Exports disponibles** :
- Export Excel/CSV de toutes les candidatures
- Export filtré selon critères
- Export avec documents joints

#### **2. Gestion des Documents**
**Resource** : `DocumentResource`
- **Visualisation** : Aperçu des documents téléchargés
- **Téléchargement** : Accès direct aux fichiers
- **Organisation** : Classement par candidature et type
- **Sécurité** : Accès protégé et traçable

#### **3. Gestion des Opportunités**
**Resource** : `OpportuniteResource`
- **Création d'offres** avec :
  - Titre et description détaillée
  - Compétences recherchées
  - Durée et période
  - Directions concernées
- **Publication/Dépublication** 
- **Gestion des candidatures liées**

#### **4. Évaluations Post-Stage**
**Resource** : `EvaluationResource`
- **Formulaires d'évaluation** personnalisables
- **Notes et commentaires** 
- **Statistiques de performance**
- **Suivi de la satisfaction**

#### **5. Configuration du Système**
**Resource** : `ConfigurationResource` et `ConfigurationListeResource`

**Types configurables** :
- **Établissements partenaires** - Liste des universités/écoles
- **Directions** - Départements de BRACONGO
- **Niveaux d'études** - Bac+1 à Doctorat
- **Postes de stage** - Types de missions proposées

### 📈 Tableaux de Bord et Statistiques

#### **Dashboard Administrateur**
- **Métriques en temps réel** :
  - Nombre total de candidatures
  - Taux d'acceptation/rejet
  - Candidatures par mois
  - Répartition par établissement
  - Répartition par direction

#### **Rapports Avancés**
- **Rapport mensuel** des candidatures
- **Analyse de performance** par direction
- **Suivi des stages** en cours
- **Évaluations** et satisfaction

### 🔔 Notifications et Communication

#### **Notifications Automatiques**
- **Email candidat** : Changement de statut
- **Notifications admin** : Nouvelles candidatures
- **Rappels automatiques** : Fin de stage approchante

#### **Gestion des Emails**
- **Templates personnalisables**
- **Variables dynamiques** (nom, code suivi, etc.)
- **Historique d'envoi**

---

## 🚀 Déploiement et Maintenance

### 📋 Scripts de Déploiement
**Fichier** : `deploy-final.sh`

**Fonctionnalités** :
- Installation automatique des dépendances
- Configuration de l'environnement
- Migration de base de données
- Optimisation Laravel
- Configuration des permissions
- Tests de santé

### 🏥 Monitoring et Tests

#### **Endpoints de Test**
- **Health Check** : `/api/health`
- **Test Application** : `/test`
- **Vérification Extensions** : PHP, MySQL, Redis

#### **Logs et Débogage**
- **Logs Laravel** : `storage/logs/laravel.log`
- **Logs de Queue** : Traitement asynchrone
- **Monitoring erreurs** : Suivi automatisé

### 🔐 Sécurité et Permissions

#### **Authentification Multiple**
- **Admin** : Système Filament (users table)
- **Candidats** : Système dédié (candidats table)
- **Guards séparés** pour chaque type d'utilisateur

#### **Protection des Données**
- **Validation stricte** des formulaires
- **Sanitisation** des uploads
- **Chiffrement** des données sensibles
- **Backup automatique**

---

## 📱 API et Intégrations

### 🔗 APIs Disponibles

#### **API Statistiques**
- **Route** : `/api/evaluations/statistiques`
- **Format** : JSON
- **Données** : Métriques d'évaluation

#### **API Health Check**
- **Route** : `/api/health`
- **Monitoring** : État des services (DB, Cache, Queue)

---

## 🎯 Workflows Métier

### 📋 Processus de Candidature

```
1. 🎓 CANDIDAT
   ├── Création compte (optionnel)
   ├── Remplissage formulaire
   ├── Upload documents
   └── Réception code suivi (BRC-XXXXXXXX)

2. 📧 NOTIFICATION AUTO
   ├── Email confirmation candidat
   ├── Notification admin
   └── Mise à jour tableau de bord

3. 👤 TRAITEMENT ADMIN
   ├── Examen dossier
   ├── Validation documents
   ├── Décision (Valider/Rejeter)
   └── Définition dates (si validé)

4. 📧 NOTIFICATION DÉCISION
   ├── Email automatique candidat
   ├── Mise à jour statut suivi
   └── Archivage dossier

5. 📊 POST-STAGE (si validé)
   ├── Évaluation stagiaire
   ├── Évaluation tuteur
   ├── Génération certificat
   └── Statistiques globales
```

### 🔄 Gestion des Statuts

| Statut | Description | Actions Admin | Notifications |
|--------|-------------|---------------|---------------|
| **Non traité** | Candidature reçue | Examiner, Valider, Rejeter | Nouvelle candidature |
| **En cours d'examen** | Dossier étudié | Valider, Rejeter | En cours d'étude |
| **Validé** | Stage accepté | Définir dates, Évaluer | Félicitations + dates |
| **Rejeté** | Candidature refusée | Réactiver (si erreur) | Motif de rejet |

---

## 🛠️ Commandes Utiles

### 🔧 Administration Laravel
```bash
# Gestion des utilisateurs admin
php artisan make:filament-user

# Gestion de la base de données
php artisan migrate
php artisan db:seed

# Optimisation
php artisan optimize
php artisan config:cache
php artisan route:cache

# Gestion des queues
php artisan queue:work
php artisan queue:restart

# Notifications programmées
php artisan stages:notifier-fin-stage
```

### 📊 Maintenance et Monitoring
```bash
# Vérification logs
tail -f storage/logs/laravel.log

# Nettoyage
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Backup base de données
mysqldump bracongo_stages > backup_$(date +%Y%m%d).sql
```

---

## 📞 Support et Contact

### 🆘 En cas de Problème

#### **Erreurs Courantes**
1. **Erreur 500** : Vérifier permissions `storage/` et `bootstrap/cache/`
2. **Connexion DB** : Vérifier configuration `.env`
3. **Upload fichiers** : Vérifier `php.ini` (upload_max_filesize)
4. **Emails** : Vérifier configuration SMTP

#### **Logs à Consulter**
- `storage/logs/laravel.log` - Erreurs application
- `/var/log/nginx/error.log` - Erreurs serveur web
- `/var/log/mysql/error.log` - Erreurs base de données

### 📧 Contacts Support
- **Email technique** : support@bigfive.dev
- **Email métier** : stages@bracongo.cg
- **Documentation** : README.md du projet

---

## 🔮 Évolutions Futures

### 📋 Fonctionnalités Prévues
- **Module de reporting avancé**
- **Intégration calendrier** pour planification
- **Chat en temps réel** candidat-admin
- **Application mobile** dédiée
- **API publique** pour partenaires

### 🚀 Améliorations Techniques
- **Cache Redis** pour performances
- **Elasticsearch** pour recherche avancée
- **Docker** pour déploiement
- **CI/CD** automatisé

---

*Documentation générée le 12 septembre 2025 - Version 1.0*
*BRACONGO Stages - "Ensemble, construisons l'avenir" 🍺*