#!/bin/sh
# =============================================================================
# Container entrypoint: wait for DB, run migrations, seed once, then serve.
# =============================================================================
set -e

PORT="${PORT:-8000}"

echo "Waiting for database..."
for i in 1 2 3 4 5; do
    if php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration; then
        break
    fi
    echo "Attempt $i failed, retrying in 5s..."
    sleep 5
done

USER_COUNT=$(php bin/console doctrine:query:sql 'SELECT COUNT(*) AS cnt FROM "user"' 2>/dev/null | grep -oE '[0-9]+' | tail -1 || echo '0')
echo "User count: ${USER_COUNT:-0}"

if [ "${USER_COUNT:-0}" = "0" ]; then
    echo "No users found, loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo "Users exist, skipping fixtures"
fi

exec symfony serve --no-tls --port="$PORT" --allow-http --allow-all-ip
