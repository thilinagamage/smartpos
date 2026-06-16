#!/bin/sh
set -e

echo "Starting SmartPOS container..."

# Wait briefly to ensure storage/cache dirs are writable
mkdir -p /var/www/storage/logs /var/www/storage/framework/sessions \
         /var/www/storage/framework/views /var/www/storage/framework/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Start php-fpm in the background
php-fpm -D

# Start nginx in the foreground (keeps the container alive)
echo "Starting nginx..."
nginx -g "daemon off;"