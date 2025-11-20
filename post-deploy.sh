#!/bin/bash

# =================================================
# AgriSys Post-Deployment Script (Run on Server)
# =================================================
# Run this script AFTER uploading your files to the production server

echo "🔧 Running AgriSys post-deployment setup..."

# 0. CRITICAL: Clear all caches FIRST to avoid cached 'env' errors
echo "🧹 Clearing any old cached configuration..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 1. Set proper ownership (adjust www-data to your web server user)
echo "👤 Setting proper file ownership..."
sudo chown -R www-data:www-data /var/www/html/agrisys
sudo chown -R www-data:www-data /var/www/html/agrisys/storage
sudo chown -R www-data:www-data /var/www/html/agrisys/bootstrap/cache

# 2. Set proper permissions
echo "🔒 Setting proper file permissions..."
sudo find /var/www/html/agrisys -type f -exec chmod 644 {} \;
sudo find /var/www/html/agrisys -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/html/agrisys/storage
sudo chmod -R 775 /var/www/html/agrisys/bootstrap/cache

# 3. Create storage link if it doesn't exist
echo "🔗 Ensuring storage link exists..."
php artisan storage:link

# 4. Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 5. Seed initial data (if needed)
echo "🌱 Seeding database..."
php artisan db:seed --force

# 6. Clear and cache everything
echo "🧹 Final cache optimization..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
# Now cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Restart web server (optional)
echo "🔄 Restarting web services..."
sudo systemctl restart apache2
# Or if using nginx:
# sudo systemctl restart nginx

echo "✅ AgriSys deployment complete!"
echo "🌐 Your application should now be live at your domain!"
