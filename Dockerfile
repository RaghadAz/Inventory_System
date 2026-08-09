FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD php artisan serve --host=0.0.0.0 --port=$PORT
