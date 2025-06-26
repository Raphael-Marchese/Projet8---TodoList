# Dockerfile
FROM php:7.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       zip unzip \
       libicu-dev \
       libxml2-dev \
       libzip-dev \
       libonig-dev \
    && docker-php-ext-install \
       intl \
       pdo \
       pdo_mysql \
       mbstring \
       xml \
       zip \
       opcache \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
