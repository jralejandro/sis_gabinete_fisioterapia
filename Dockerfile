# Dockerfile - PHP-FPM para Laravel
FROM php:8.3-fpm

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zlib1g-dev libpng-dev libonig-dev libxml2-dev \
    libicu-dev libjpeg-dev libpng-dev libwebp-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath gd intl

# Instalar Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar composer.json y composer.lock (cache de dependencias)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-scripts || true

# Copiar toda la aplicación
COPY . .

# Permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
