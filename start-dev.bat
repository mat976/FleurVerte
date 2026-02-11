@echo off
echo === Fleur Verte - Demarrage Dev ===

echo [1/4] Demarrage PostgreSQL via Podman...
podman machine start 2>nul
podman-compose -f podman-compose.yml up -d

echo [2/4] Attente de la BDD (5s)...
timeout /t 5 /nobreak >nul

echo [3/4] Schema + Fixtures...
php bin/console doctrine:schema:update --force --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

echo [4/4] Lancement Symfony...
symfony serve --no-tls --port=8000
