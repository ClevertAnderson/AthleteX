#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html
echo "Clearing old caches..."
php artisan config:clear
php artisan route:clear
echo "Caching config..."
php artisan config:cache
echo "Running migrations..."
php artisan migrate --force
echo "Seeding default accounts..."
php artisan db:seed --force
echo "Fixing nginx rewrite rule for Laravel front controller..."
sed -i 's#try_files $uri $uri/ =404;#try_files $uri $uri/ /index.php?$query_string;#g' /etc/nginx/sites-available/default.conf
sed -i 's#try_files $uri $uri/ =404;#try_files $uri $uri/ /index.php?$query_string;#g' /etc/nginx/sites-available/default-ssl.conf