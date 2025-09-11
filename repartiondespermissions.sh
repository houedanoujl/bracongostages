cat > diagnose-filament.sh << 'EOF'
#!/bin/bash
echo "🔍 Diagnostic des fichiers Filament..."

echo "📁 Tous les fichiers Témoignage:"
find app/Filament/Resources/ -name "*Témoignage*" -o -name "*Temoignage*"

echo ""
echo "�� Contenu du dossier Resources:"
ls -la app/Filament/Resources/

echo ""
echo "🗑️ Suppression agressive des fichiers avec accents..."
find app/Filament/Resources/ -name "*Témoignage*" -delete
find app/Filament/Resources/ -type d -name "*Témoignage*" -exec rm -rf {} + 2>/dev/null || true

echo ""
echo "✅ Vérification après suppression:"
ls -la app/Filament/Resources/ | grep -i témoignage

echo ""
echo "🧹 Nettoyage du cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
composer dump-autoload --optimize

echo "🎉 Diagnostic terminé !"
EOF

chmod +x diagnose-filament.sh
./diagnose-filament.sh