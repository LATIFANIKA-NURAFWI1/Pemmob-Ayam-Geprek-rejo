#!/bin/sh
# ================================================================
# Docker Startup Script — Ayam Geprek Rejo
# Dijalankan setiap container start
# ================================================================
set -e

echo "==> [1/5] Running database migrations..."
php artisan migrate --force

echo "==> [2/5] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> [3/5] Linking storage..."
php artisan storage:link || true

echo "==> [4/5] Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [5/5] Starting services (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
