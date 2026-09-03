#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "Installing PHP Dependencies..."
composer install --optimize-autoloader --no-dev

echo "Installing Node Dependencies and Building Frontend..."
npm install
npm run build

echo "Creating Storage Link..."
php artisan storage:link --force || true

echo "Caching Laravel Configuration, Routes & Views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Running Database Migrations..."
php artisan migrate --force
