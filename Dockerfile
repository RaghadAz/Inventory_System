FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
     libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install intl gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs
RUN composer install --optimize-autoloader --no-interaction
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

CMD php artisan serve --host 0.0.0.0 --port 8000
