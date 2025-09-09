#!/bin/bash

# Script de réparation pour le build Vite
# À utiliser quand vite: not found
set -e

echo "🔧 Réparation du build Vite..."

# Navigation vers le bon répertoire
if [[ "$(pwd)" == "/home/forge" ]]; then
    if [ -d "bracongostages.bigfive.dev" ]; then
        echo "📁 Navigation vers bracongostages.bigfive.dev..."
        cd bracongostages.bigfive.dev
    else
        echo "❌ Répertoire bracongostages.bigfive.dev non trouvé"
        exit 1
    fi
fi

echo "📁 Répertoire de travail: $(pwd)"

# 1. Vérifier que package.json existe
if [ ! -f "package.json" ]; then
    echo "❌ package.json non trouvé"
    exit 1
fi

# 2. Nettoyer node_modules si nécessaire
if [ -d "node_modules" ]; then
    echo "🧹 Nettoyage de node_modules..."
    rm -rf node_modules
fi

# 3. Réinstaller toutes les dépendances (y compris dev)
echo "📦 Réinstallation des dépendances Node.js..."
if [ -f "package-lock.json" ]; then
    npm ci --no-audit
else
    npm install --no-audit
fi

# 4. Vérifier que Vite est installé
echo "🔍 Vérification de Vite..."
if [ -f "node_modules/.bin/vite" ]; then
    echo "✅ Vite trouvé dans node_modules"
else
    echo "❌ Vite non trouvé, installation..."
    npm install vite --save-dev
fi

# 5. Build des assets
echo "🎨 Build des assets avec Vite..."
if command -v npx >/dev/null 2>&1; then
    echo "🔧 Utilisation de npx vite build..."
    npx vite build
else
    echo "🔧 Utilisation de ./node_modules/.bin/vite build..."
    ./node_modules/.bin/vite build
fi

echo "✅ Build Vite terminé avec succès !"
echo "🚀 Les assets sont maintenant construits"
