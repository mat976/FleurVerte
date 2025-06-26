FROM php:8.1-apache

WORKDIR /var/www/html

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP nécessaires pour Symfony
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    intl \
    gd

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuration d'Apache
RUN a2enmod rewrite
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Copie des fichiers du projet
COPY . .

# Installation des dépendances PHP
RUN composer install --no-interaction

# Définition des permissions
RUN chown -R www-data:www-data /var/www/html/var

EXPOSE 80

CMD ["apache2-foreground"]