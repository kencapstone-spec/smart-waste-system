#!/usr/bin/env bash
set -e

echo "🚀 Starting Smart Waste System on Render..."

# Enable multi-worker process handling in PHP built-in server (concurrent requests)
export PHP_CLI_SERVER_WORKERS=4

# Pre-cache configuration, routes, and views for instantaneous response times
echo "⚡ Warming up caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

PORT_TO_USE="${PORT:-8080}"
echo "🌟 Server listening on port $PORT_TO_USE with 4 concurrent workers..."

exec php artisan serve --host=0.0.0.0 --port="$PORT_TO_USE" --no-reload
