#!/bin/bash

# ==========================================
# Script de lancement FleurVerte Symfony
# Port: 3010
# ==========================================

set -e

cd "$(dirname "$0")"
PROJECT_DIR="$(pwd)"

echo "=========================================="
echo "  FleurVerte - Lancement Symfony"
echo "=========================================="

# Vérifier si Docker est disponible et si on veut l'utiliser
if command -v docker &> /dev/null && [ "$1" == "docker" ]; then
    echo " Mode: Docker Compose"
    echo "------------------------------------------"
    docker compose up -d --build
    echo ""
    echo "✅ Application démarrée !"
    echo "   URL: http://localhost:3010"
    echo ""
    echo "📋 Logs: docker compose logs -f app"
    echo "🛑 Arrêter: docker compose down"
    exit 0
fi

# Mode PHP built-in (par défaut)
echo " Mode: PHP Built-in Server"
echo "------------------------------------------"

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP non trouvé !"
    exit 1
fi

PHP_VERSION=$(php -v | head -1 | grep -oE '[0-9]+\.[0-9]+' | head -1)
echo "✅ PHP $PHP_VERSION détecté"

# Vérifier la DB
if ! ss -tlnp | grep -q ':5432'; then
    echo "⚠️  PostgreSQL (port 5432) non détecté"
    echo "   Essai de démarrage du container Docker DB..."
    docker start fleurverte_db 2>/dev/null || true
    sleep 2
    if ss -tlnp | grep -q ':5432'; then
        echo "✅ DB démarrée"
    else
        echo "⚠️  DB non accessible - le site fonctionnera en mode limité"
    fi
else
    echo "✅ PostgreSQL détecté sur le port 5432"
fi

# Vérifier .env
if [ ! -f ".env.local" ]; then
    echo "⚠️  .env.local manquant - création..."
    cp .env .env.local 2>/dev/null || true
fi

# Nettoyer cache
echo "🧹 Nettoyage cache..."
php bin/console cache:clear --no-interaction 2>/dev/null || true

# Vérifier les migrations
echo "🗄️  Vérification migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration 2>/dev/null || true

# Vérifier si le port est déjà utilisé
if ss -tlnp | grep -q ':3010'; then
    echo "🛑 Port 3010 déjà utilisé - arrêt du processus existant..."
    pkill -f "php -S 0.0.0.0:3010" 2>/dev/null || true
    sleep 1
fi

# Lancer le serveur
echo "🚀 Démarrage serveur PHP sur le port 3010..."
php -S 0.0.0.0:3010 -t public > /tmp/fleurverte.log 2>&1 &
SERVER_PID=$!
echo $SERVER_PID > /tmp/fleurverte.pid

sleep 2

# Vérifier que le serveur a démarré
if kill -0 $SERVER_PID 2>/dev/null; then
    echo ""
    echo "=========================================="
    echo "  ✅ FleurVerte est EN LIGNE !"
    echo "=========================================="
    echo "   🌐 URL: http://localhost:3010"
    echo "   📁 Projet: $PROJECT_DIR"
    echo "   📝 Logs: tail -f /tmp/fleurverte.log"
    echo "   🛑 Arrêter: kill $SERVER_PID"
    echo "=========================================="
else
    echo "❌ Échec du démarrage du serveur"
    cat /tmp/fleurverte.log
    exit 1
fi
