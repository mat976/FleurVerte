# Production image for Symfony on Render

FROM composer:2 AS composer_stage

# Final stage: PHP CLI with extensions and Symfony CLI
FROM php:8.2-cli

# Install system deps and PHP extensions (PostgreSQL for Render)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git curl unzip zip gnupg ca-certificates libzip-dev libicu-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies (with APP_ENV=prod to skip dev bundles)
ENV APP_ENV=prod
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN composer run-script --no-dev post-install-cmd || true

# Expose the HTTP port (Render uses 10000 by default, but we'll use PORT env var)
EXPOSE 10000

# Start script for Render (fixtures only on fresh DB)
CMD ["sh", "-c", "\
  if php bin/console doctrine:database:create --if-not-exists --no-interaction 2>&1 | grep -q 'created'; then \
    echo 'New database created, loading fixtures...' && \
    php bin/console doctrine:migrations:migrate --no-interaction && \
    php bin/console doctrine:fixtures:load --no-interaction; \
  else \
    echo 'Database exists, running migrations only...' && \
    php bin/console doctrine:migrations:migrate --no-interaction; \
  fi && \
  symfony serve --no-tls --port=${PORT:-10000} --allow-http --allow-all-ip"]