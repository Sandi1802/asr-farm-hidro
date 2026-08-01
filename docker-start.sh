#!/bin/bash

echo "=== Running migrations (errors won't stop Apache) ==="
php artisan migrate --force || echo "WARNING: Migration failed, continuing anyway..."

echo "=== Caching config ==="
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Setting storage link ==="
php artisan storage:link || true

echo "=== Starting Apache ==="
exec apache2-foreground

