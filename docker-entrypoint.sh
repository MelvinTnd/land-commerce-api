#!/bin/bash
set -e

echo "========================================="
echo "   Blackmaket - Démarrage du serveur"
echo "========================================="

# ── 1. Vérifier APP_KEY ────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY manquante, génération automatique..."
    php artisan key:generate --force
else
    echo "✅ APP_KEY détectée"
fi

# ── 2. Créer les dossiers storage si nécessaires ───────────────────────────
echo "📁 Vérification des dossiers storage..."
mkdir -p /var/www/html/storage/app/public/products
mkdir -p /var/www/html/storage/app/public/logos
mkdir -p /var/www/html/storage/app/public/banners
mkdir -p /var/www/html/storage/logs
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/storage

# ── 3. Lier le stockage public ─────────────────────────────────────────────
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 4. Attendre la base de données ────────────────────────────────────────
echo "⏳ Vérification de la connexion base de données..."
MAX_RETRIES=15
RETRY=0
until php artisan db:show --no-interaction 2>/dev/null || [ $RETRY -eq $MAX_RETRIES ]; do
    RETRY=$((RETRY+1))
    echo "   Tentative $RETRY/$MAX_RETRIES..."
    sleep 3
done

# ── 5. Migrations ──────────────────────────────────────────────────────────
echo "🗄️  Exécution des migrations..."
php artisan migrate --force --no-interaction

# ── 6. Cache (vider d'abord pour forcer la relecture des variables) ────────
echo "🔧 Mise en cache de la configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

echo ""
echo "✅ Application prête !"
echo "🚀 Démarrage d'Apache sur le port 80..."
echo "   APP_URL          = ${APP_URL}"
echo "   CLOUDINARY       = ${CLOUDINARY_CLOUD_NAME:-non configuré}"
echo "========================================="

exec apache2-foreground
