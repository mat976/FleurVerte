# Dev image for Symfony with fixtures auto-load

FROM composer:2 AS composer_stage

# Final stage: PHP CLI with extensions and Symfony CLI
FROM php:8.2-cli

# Install system deps and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git curl unzip zip gnupg ca-certificates libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /app

# Expose the dev HTTP port
EXPOSE 8000

# Start: migrations + fixtures + server
CMD ["sh", "-c", "php bin/console doctrine:database:create --if-not-exists && php bin/console doctrine:migrations:migrate --no-interaction && php bin/console doctrine:fixtures:load --no-interaction && symfony serve --no-tls --port=8000 --allow-http --allow-all-ip"]