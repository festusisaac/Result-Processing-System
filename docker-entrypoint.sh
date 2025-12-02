#!/bin/bash
set -e

echo "🚀 Starting deployment script..."

# 1. Create .env file if missing
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Set APP_URL if RAILWAY_PUBLIC_DOMAIN is available
if [ ! -z "$RAILWAY_PUBLIC_DOMAIN" ]; then
    echo "Setting APP_URL to https://$RAILWAY_PUBLIC_DOMAIN..."
    sed -i "s|APP_URL=.*|APP_URL=https://$RAILWAY_PUBLIC_DOMAIN|g" /var/www/html/.env
fi

# 2. Aggressively force SQLite in .env
# This ensures that even if Laravel reads the file directly, it sees SQLite
echo "Forcing SQLite configuration in .env..."
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/g' /var/www/html/.env
sed -i 's/DB_HOST=.*/DB_HOST=/g' /var/www/html/.env
sed -i 's/DB_PORT=.*/DB_PORT=/g' /var/www/html/.env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=\/var\/www\/html\/database\/database.sqlite/g' /var/www/html/.env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=/g' /var/www/html/.env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=/g' /var/www/html/.env

# 3. Export variables for good measure (Process env wins over .env)
export DB_CONNECTION=sqlite
export DB_DATABASE=/var/www/html/database/database.sqlite

# 4. Create database file
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database.sqlite file..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    
    echo "Running initial migrations and seeding..."
    # Explicitly pass connection to be 100% sure
    php artisan migrate --force --database=sqlite
    php artisan db:seed --class=DatabaseSeeder --force
else
    echo "Database exists. Running migrations..."
    php artisan migrate --force --database=sqlite
fi

# Ensure permissions
chown -R www-data:www-data /var/www/html/database

# 5. Clear and cache config
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Setup complete. Starting Apache..."
exec apache2-foreground
