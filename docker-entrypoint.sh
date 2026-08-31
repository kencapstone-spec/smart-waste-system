#!/usr/bin/env bash
set -e

# Optimize application
php artisan optimize:clear || true

# Run database migrations in production
if [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration warning: DB not ready or failed"
fi

# Ensure storage link exists
php artisan storage:link || true

PORT_TO_USE="${PORT:-8080}"
echo "Starting Laravel server on port $PORT_TO_USE..."

exec php artisan serve --host=0.0.0.0 --port="$PORT_TO_USE"
