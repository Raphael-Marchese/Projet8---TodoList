# Dockerfile
FROM php:8.3-fpm

# Installation des dépendances système
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       zip unzip \
       libicu-dev \
       libxml2-dev \
       libzip-dev \
       libonig-dev \
       curl \
    && docker-php-ext-install \
       intl \
       pdo \
       pdo_mysql \
       mbstring \
       xml \
       zip \
       opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier une config xdebug (tu peux l’éditer depuis docker-compose si besoin)
COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Définir le répertoire de travail
WORKDIR /var/www/html

# Exposer le port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
