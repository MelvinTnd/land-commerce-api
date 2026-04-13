#!/bin/bash
set -e

echo "========================================="
echo "   BéninMarket - Démarrage du serveur"
echo "========================================="

# ── 1. Vérifier APP_KEY ────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY manquante, génération automatique..."
    php artisan key:generate --force
else
    echo "✅ APP_KEY détectée"
fi

# ── 2. Lier le stockage public ─────────────────────────────────────────────
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 3. Attendre la base de données ────────────────────────────────────────
echo "⏳ Vérification de la connexion base de données..."
MAX_RETRIES=10
RETRY=0
until php artisan db:show --no-interaction 2>/dev/null || [ $RETRY -eq $MAX_RETRIES ]; do
    RETRY=$((RETRY+1))
    echo "   Tentative $RETRY/$MAX_RETRIES..."
    sleep 3
done

# ── 4. Migrations ──────────────────────────────────────────────────────────
echo "🗄️  Exécution des migrations..."
php artisan migrate --force --no-interaction

# ── 5. Cache de configuration ──────────────────────────────────────────────
echo "🔧 Mise en cache de la configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Application prête !"
echo "🚀 Démarrage d'Apache sur le port 80..."
echo "========================================="

exec apache2-foreground
