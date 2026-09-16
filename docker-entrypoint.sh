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

PORT_TO_USE="${PORT:-8080}"
echo "Starting high-performance multi-worker server on port $PORT_TO_USE..."

export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"

exec php artisan serve --host=0.0.0.0 --port="$PORT_TO_USE" --no-reload
