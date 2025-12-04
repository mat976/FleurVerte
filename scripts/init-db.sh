#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"$DATABASE_HOST" -P"$DATABASE_PORT" -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" --silent; do
    echo "MySQL not ready, waiting..."
    sleep 2
done

echo "MySQL is ready!"

# Create database if it doesn't exist
echo "Creating database if not exists..."
mysql -h"$DATABASE_HOST" -P"$DATABASE_PORT" -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DATABASE_NAME;"

echo "Database created or already exists."

# Run migrations
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Database initialization complete!"
