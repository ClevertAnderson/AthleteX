#!/bin/bash

echo "Caching Laravel configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running Database Migrations..."
php artisan migrate --force

echo "Starting the web server..."
exec /start.sh