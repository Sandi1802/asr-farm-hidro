#!/usr/bin/env bash
# render-build.sh — Script build untuk Render.com

set -o errexit

echo "=== Installing PHP dependencies ==="
composer install --no-dev --optimize-autoloader

echo "=== Generating app key (if not set) ==="
php artisan key:generate --force

echo "=== Running database migrations ==="
php artisan migrate --force

echo "=== Clearing and caching config ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Installing NPM & building assets ==="
npm install
npm run build

echo "=== Setting storage symlink ==="
php artisan storage:link

echo "=== Build complete! ==="
