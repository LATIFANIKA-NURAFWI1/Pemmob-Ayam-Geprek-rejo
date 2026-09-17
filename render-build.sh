#!/usr/bin/env bash
# ================================================================
# Render.com Build Script — Ayam Geprek Rejo (Laravel 13)
# Dijalankan SEKALI saat deploy/rebuild
# ================================================================
set -e

echo "==> [1/6] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> [2/6] Installing Node.js dependencies & building assets..."
npm ci
npm run build

echo "==> [3/6] Caching Laravel config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> [4/6] Running database migrations..."
php artisan migrate --force

echo "==> [5/6] Linking storage..."
php artisan storage:link || true

echo "==> [6/6] Clearing runtime cache..."
php artisan cache:clear
php artisan queue:restart || true

echo "==> Build complete!"
