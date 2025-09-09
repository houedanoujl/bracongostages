# 🔧 Correction du Problème de Déploiement Forge

## Problème Identifié

L'erreur suivante se produit lors du déploiement sur Laravel Forge :

```
In PackageManifest.php line 178:
The /home/forge/bracongostages.bigfive.dev/bootstrap/cache directory must be present and writable.
```

## Cause du Problème

Le script `post-autoload-dump` de Composer essaie d'exécuter `php artisan package:discover` avant que le répertoire `bootstrap/cache` soit créé et configuré avec les bonnes permissions.

## Solutions Implémentées

### 1. Script de Déploiement Corrigé (`deploy-forge-fixed-v2.sh`)

Ce script résout le problème en :
- Créant les répertoires Laravel **AVANT** l'installation Composer
- Configurant les permissions **AVANT** l'installation Composer
- Vérifiant que `bootstrap/cache` est accessible en écriture

### 2. Script de Déploiement Simple (`deploy-forge-simple.sh`)

Ce script utilise une approche différente :
- Utilise `--no-scripts` avec Composer pour éviter l'exécution automatique des scripts
- Exécute manuellement `php artisan package:discover` après l'installation
- Plus simple et plus robuste

### 3. Modification du `composer.json`

Ajout d'un script `pre-install-cmd` qui crée automatiquement le répertoire `bootstrap/cache` si il n'existe pas.

## Utilisation

### Option 1 : Script Corrigé (Recommandé)
```bash
./deploy-forge-fixed-v2.sh
```

### Option 2 : Script Simple
```bash
./deploy-forge-simple.sh
```

### Option 3 : Déploiement Manuel
```bash
# 1. Créer les répertoires
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs

# 2. Permissions
chmod -R 775 storage bootstrap/cache
chown -R forge:forge storage bootstrap/cache

# 3. Installation Composer
$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Configuration Laravel
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Vérifications

Après le déploiement, vérifiez que :
- Le répertoire `bootstrap/cache` existe et est accessible en écriture
- Les fichiers `bootstrap/cache/packages.php` et `bootstrap/cache/services.php` sont créés
- L'application fonctionne correctement

## Notes Importantes

- Les scripts sont maintenant exécutables (`chmod +x`)
- Les deux approches sont testées et fonctionnelles
- Le script simple est recommandé pour les déploiements futurs
- La modification du `composer.json` ajoute une sécurité supplémentaire

## Support

Si vous rencontrez encore des problèmes, utilisez le script simple (`deploy-forge-simple.sh`) qui est le plus robuste.
