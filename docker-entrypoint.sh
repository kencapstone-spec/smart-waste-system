#!/usr/bin/env bash
set -e

# Run database migrations in production if DB is configured (supports standard DB_* and Railway MYSQL*/PG* variables)
if [ -n "$DB_HOST" ] || [ -n "$MYSQLHOST" ] || [ -n "$MYSQL_URL" ] || [ -n "$DATABASE_URL" ] || [ -n "$PGHOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration warning: DB not ready or failed"
fi

# Ensure storage symlink exists cleanly without errors
php artisan storage:link --force || true

# Pre-cache configuration, routes, events, and views for 10x faster response time
echo "Caching Laravel configuration, routes, events, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

PORT_TO_USE="${PORT:-10000}"

# Configure Nginx to listen on the container's assigned PORT
sed -i "s/listen [0-9]*;/listen $PORT_TO_USE;/" /etc/nginx/http.d/default.conf

# Start PHP-FPM in background
echo "Starting PHP-FPM daemon..."
php-fpm -D

# Start Nginx in foreground to serve static assets with gzip & cache, and reverse proxy PHP
echo "Starting Nginx production server on port $PORT_TO_USE..."
exec nginx -g 'daemon off;'
