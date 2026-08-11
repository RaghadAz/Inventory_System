FROM php:8.3-fpm

RUN apt-get update && apt-get install -y nodejs npm \
    git \
    npm install\
    npm run build\
    unzip \
    zip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install intl zip gd pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

RUN composer install --optimize-autoloader --no-scripts

RUN php artisan package:discover --ansi

CMD php artisan serve --host=0.0.0.0 --port=${PORT}
