#!/bin/bash
set -e

# Create database file if it doesn't exist
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    
    # Run migrations and seed for the first time
    echo "Running initial migrations and seeding..."
    php artisan migrate --force
    php artisan db:seed --force
fi

# Ensure database directory permissions
chown -R www-data:www-data /var/www/html/database

# Run migrations (for updates)
echo "Running migrations..."
php artisan migrate --force

# Cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
