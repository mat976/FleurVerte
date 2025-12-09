# Production image for Symfony on Render

FROM composer:2 AS composer_stage

# Final stage: PHP CLI with extensions and Symfony CLI
FROM php:8.2-cli

# Install system deps and PHP extensions (PostgreSQL)
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

# Install Node.js for asset building
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install PHP dependencies (with APP_ENV=prod to skip dev bundles)
ENV APP_ENV=prod
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN composer run-script --no-dev post-install-cmd || true

# Build frontend assets
RUN npm install && npm run build

# Expose the HTTP port (Render uses 10000 by default, but we'll use PORT env var)
EXPOSE 10000

# Start script for Render (schema update + fixtures if no users)
CMD ["sh", "-c", "\
  php bin/console doctrine:schema:update --force --no-interaction && \
  USER_COUNT=$(php bin/console doctrine:query:sql 'SELECT COUNT(*) as cnt FROM \"user\"' 2>/dev/null | grep -oE '[0-9]+' | tail -1 || echo '0') && \
  echo \"User count: $USER_COUNT\" && \
  if [ \"$USER_COUNT\" = \"0\" ] || [ -z \"$USER_COUNT\" ]; then \
    echo 'No users found, loading fixtures...' && \
    php bin/console doctrine:fixtures:load --no-interaction; \
  else \
    echo 'Users exist, skipping fixtures'; \
  fi && \
  symfony serve --no-tls --port=${PORT:-10000} --allow-http --allow-all-ip"]