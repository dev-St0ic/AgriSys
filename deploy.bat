@echo off
REM =================================================
REM AgriSys Production Deployment Script (Windows)
REM =================================================
REM This script prepares your AgriSys application for production deployment
REM Run this script before uploading to your hosting provider

echo 🚀 Starting AgriSys Production Deployment Preparation...

REM 1. Install production dependencies
echo 📦 Installing production dependencies...
composer install --no-dev --optimize-autoloader

REM 2. Clear all caches
echo 🧹 Clearing application caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

REM 3. Optimize for production
echo ⚡ Optimizing application for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

REM 4. Build frontend assets
echo 🎨 Building frontend assets...
npm install
npm run build

REM 5. Create symbolic link for storage
echo 🔗 Creating storage link...
php artisan storage:link

REM 6. Generate application key (if needed)
echo 🔑 Generating application key...
php artisan key:generate --force

echo ✅ Production deployment preparation complete!
echo.
echo 📋 Next Steps:
echo 1. Upload files to your hosting provider
echo 2. Copy .env.production to .env and update with your production settings
echo 3. Run database migrations: php artisan migrate --force
echo 4. Test your application
echo.
echo ⚠️  Important: Make sure to update .env with your production database and domain settings!

pause
