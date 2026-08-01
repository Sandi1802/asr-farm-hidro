#!/bin/bash

echo "=== Passing environment variables to Apache ==="
# Export all container env vars so Apache/PHP can read them
printenv | grep -v "^_" | while IFS='=' read -r name value; do
    echo "export $name=\"$value\""
done >> /etc/apache2/envvars

echo "=== Running migrations ==="
php artisan migrate --force || echo "WARNING: Migration failed, continuing anyway..."

echo "=== Caching config ==="
php artisan config:clear || true
php artisan cache:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Setting storage link ==="
php artisan storage:link || true

echo "=== Starting Apache ==="
exec apache2-foreground
