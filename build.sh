#!/usr/bin/env bash
set -o errexit

composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
