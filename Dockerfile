# Dev image to run Symfony app with `symfony serve` inside Docker

# Stage with Composer for faster copy
FROM composer:2 AS composer_stage

# Final stage: PHP CLI with extensions and Symfony CLI
FROM php:8.2-cli

# Install system deps and PHP extensions needed by the app
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git curl unzip zip gnupg ca-certificates libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer from composer_stage
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /app

# Expose the dev HTTP port
EXPOSE 8000

# Default command: install deps then run symfony server in HTTP (no TLS) with access from outside
CMD ["sh", "-lc", "composer install --no-interaction --prefer-dist --no-progress && symfony serve --no-tls --port=8000 --allow-http --ansi --allow-all-ip"]