#!/usr/bin/env bash
# Exit on error
set -o errexit

echo "Installing PHP Dependencies..."
composer install --optimize-autoloader --no-dev

echo "Installing Node Dependencies and Building Frontend..."
npm install
npm run build

echo "Creating Storage Link..."
php artisan storage:link || true

echo "Clearing & Caching..."
php artisan optimize:clear

echo "Running Database Migrations..."
php artisan migrate --force
