#!/bin/bash

# Script pour copier les fichiers vers Laravel Forge
# Remplacez les variables par vos vraies informations Forge

# Configuration Forge (À MODIFIER)
FORGE_SERVER="votre-serveur-forge.com"
FORGE_USER="forge"
FORGE_PATH="/home/forge/votre-site.com"
LOCAL_PATH="."

echo "🚀 Copie des fichiers vers Forge..."

# Vérification des fichiers essentiels
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json non trouvé. Êtes-vous dans le bon répertoire ?"
    exit 1
fi

echo "📁 Copie des fichiers essentiels..."

# Copie des fichiers et dossiers essentiels (exclut node_modules, vendor, etc.)
rsync -avz --progress \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.git/' \
    --exclude='.env' \
    --exclude='.env.local' \
    --exclude='*.log' \
    --exclude='.DS_Store' \
    --exclude='Thumbs.db' \
    $LOCAL_PATH/ $FORGE_USER@$FORGE_SERVER:$FORGE_PATH/

echo "✅ Fichiers copiés avec succès !"
echo ""
echo "📋 Prochaines étapes sur Forge :"
echo "   1. Connectez-vous à votre serveur Forge"
echo "   2. Naviguez vers le répertoire du site"
echo "   3. Exécutez le script de déploiement manuel"
echo "   4. Configurez le fichier .env"
echo ""
echo "🔧 Commandes à exécuter sur Forge :"
echo "   cd $FORGE_PATH"
echo "   chmod +x deploy-manual-forge.sh"
echo "   ./deploy-manual-forge.sh"
