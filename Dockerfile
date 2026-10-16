FROM php:8.4-fpm

# Dependencias del sistema + extensiones de PHP que usa Laravel.
# GD con JPEG y FreeType permite re-codificar las fotos para quitarles el EXIF (T-111).
RUN apt-get update && apt-get install -y \
        git curl zip unzip \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer, copiado desde su imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
