#!/bin/bash
set -e

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Caching config ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Setting storage link ==="
php artisan storage:link || true

echo "=== Starting Apache ==="
apache2-foreground
