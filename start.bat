@echo off
echo === FleurVerte - Demarrage ===
echo.

echo [1/3] Arret des anciens conteneurs...
podman-compose down 2>nul

echo [2/3] Build et demarrage...
podman-compose up -d --build

echo [3/3] Attente du demarrage...
timeout /t 5 /nobreak >nul

echo.
echo === Conteneurs actifs ===
podman ps

echo.
echo === FleurVerte pret sur http://localhost:8080 ===
pause
