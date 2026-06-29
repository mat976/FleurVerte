# =============================================================================
# Production Dockerfile for Symfony on Render
# Multi-stage build: build assets → production image
# =============================================================================

# --------------- Stage 1: Composer (get binary) ---------------
FROM composer:2 AS composer_stage

# --------------- Stage 2: Node (build frontend assets) ---------------
FROM node:20-alpine AS node_stage
WORKDIR /build
COPY package*.json webpack.config.js ./
COPY assets/ assets/
RUN npm ci --no-audit && npm run build

# --------------- Stage 3: Production image ---------------
FROM php:8.2-cli-alpine

# Install system deps and PHP extensions
RUN apk add --no-cache \
    bash curl unzip zip libzip-dev icu-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache \
    && apk del --no-cache

# PHP production hardening
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'expose_php=Off'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/dev/stderr'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=30'; \
    echo 'post_max_size=20M'; \
    echo 'upload_max_filesize=10M'; \
    echo 'session.cookie_httponly=1'; \
    echo 'session.cookie_secure=1'; \
    echo 'session.use_strict_mode=1'; \
    } > /usr/local/etc/php/conf.d/production.ini

# Install Composer
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /app

# Copy composer files first (better layer caching)
COPY composer.json composer.lock symfony.lock ./

# Install PHP dependencies (prod only, no dev)
ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer run-script post-install-cmd || true

# Ensure fixtures bundle is available for initial seeding
RUN composer require doctrine/doctrine-fixtures-bundle --no-interaction --optimize-autoloader

# Copy application source
COPY bin/ bin/
COPY config/ config/
COPY migrations/ migrations/
COPY public/ public/
COPY src/ src/
COPY templates/ templates/
COPY .env .env
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy built frontend assets from node stage
COPY --from=node_stage /build/public/build/ public/build/

# Warm up Symfony cache
RUN php bin/console cache:warmup --env=prod --no-debug

# Create non-root user for runtime
RUN addgroup -S appgroup && adduser -S appuser -G appgroup \
    && chown -R appuser:appgroup /app/var /app/public/uploads 2>/dev/null || true

# Set proper permissions
RUN mkdir -p var/cache var/log public/uploads/avatars public/uploads/fleurs \
    && chown -R appuser:appgroup var/ public/uploads/

EXPOSE 8000

# Switch to non-root user
USER appuser

# Healthcheck
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -f "http://localhost:${PORT:-8000}/" || exit 1

# Start: run migrations → seed once if empty → serve
CMD ["entrypoint.sh"]