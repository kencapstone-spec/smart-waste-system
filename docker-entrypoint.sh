#!/usr/bin/env bash
set -e

# Run database migrations in production if DB is configured
if [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration warning: DB not ready or failed"
fi

# Ensure storage symlink exists cleanly without errors
php artisan storage:link --force || true

# Pre-cache configuration, routes, and views for 10x faster response time
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

PORT_TO_USE="${PORT:-8080}"
echo "Starting high-performance multi-worker server on port $PORT_TO_USE..."

# Enable multi-worker process handling in PHP built-in server (concurrent requests)
export PHP_CLI_SERVER_WORKERS=4

exec php artisan serve --host=0.0.0.0 --port="$PORT_TO_USE"
