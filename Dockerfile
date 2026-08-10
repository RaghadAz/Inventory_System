FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
     zip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install intl zip gd pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Create Laravel storage directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache\
    chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader
RUN composer install --optimize-autoloader --no-interaction

CMD php artisan serve --host=0.0.0.0 --port=${PORT}
