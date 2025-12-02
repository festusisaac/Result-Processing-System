#!/bin/bash
set -e

echo "🚀 Starting deployment script..."

# 0. CRITICAL: Clear all caches FIRST (before anything else)
echo "Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 1. Create .env file if missing
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Force SQLite configuration in .env
echo "Configuring SQLite in .env..."
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/g' /var/www/html/.env
sed -i '/^DB_HOST=/d' /var/www/html/.env
sed -i '/^DB_PORT=/d' /var/www/html/.env
sed -i '/^DB_USERNAME=/d' /var/www/html/.env
sed -i '/^DB_PASSWORD=/d' /var/www/html/.env
echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> /var/www/html/.env

# 3. Export environment variables (process env takes precedence)
export DB_CONNECTION=sqlite
export DB_DATABASE=/var/www/html/database/database.sqlite

# 4. Ensure database directory exists
mkdir -p /var/www/html/database
chown -R www-data:www-data /var/www/html/database

# 5. Create database file
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# 6. Clear caches again before migrations
echo "Clearing caches before migrations..."
php artisan config:clear
php artisan cache:clear

# 7. Run migrations
echo "Running migrations with SQLite..."
php artisan migrate --force --database=sqlite 2>&1 || echo "Migrations may have already run"

# 8. Seed database if needed
if [ ! -f /var/www/html/database/.seeded ]; then
    echo "Seeding database..."
    php artisan db:seed --force --database=sqlite 2>&1 || true
    touch /var/www/html/database/.seeded
fi

# 9. Fix permissions
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment complete!"

echo "✅ Setup complete. Starting Apache..."
exec apache2-foreground
