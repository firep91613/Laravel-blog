FROM php:8.3-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpng-dev \
        libzip-dev \
        unzip \
        git \
        zip; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip \
        gd; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
