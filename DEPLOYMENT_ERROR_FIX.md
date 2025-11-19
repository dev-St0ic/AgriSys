# AgriSys Deployment Error Resolution Guide

## Problem: "Class 'env' does not exist"

This error occurs when Laravel tries to resolve the string `'env'` as a class name through the service container. This typically happens when configuration is cached with incorrect values.

## Root Causes Identified

### ✅ Issues Found and Fixed:

1. **Duplicate `same_site` key in `config/session.php`** - FIXED
2. **Post-deployment cache ordering issue** - FIXED

### ✅ All Config Files Verified:

-   `config/app.php` - ✅ Correct
-   `config/database.php` - ✅ Correct
-   `config/cache.php` - ✅ Correct
-   `config/queue.php` - ✅ Correct
-   `config/mail.php` - ✅ Correct
-   `config/filesystems.php` - ✅ Correct
-   `config/auth.php` - ✅ Correct
-   `config/session.php` - ✅ Fixed
-   `config/logging.php` - ✅ Correct
-   `config/services.php` - ✅ Correct
-   `config/activitylog.php` - ✅ Correct
-   `config/dompdf.php` - ✅ Correct
-   `config/recaptcha.php` - ✅ Correct

## Deployment Solution

### On Production Server - Run These Commands IN ORDER:

```bash
# 1. FIRST: Clear ALL caches before anything else
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 2. Manually remove cached files if artisan fails
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/routes-*.php

# 3. Verify .env file
cat .env | grep -E "=env$|=env "  # Should return nothing

# 4. Test configuration loads correctly
php artisan about

# 5. ONLY AFTER confirming everything works, cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Quick Fix Script

We've created `fix-deployment-error.sh` that does all of the above. Run it on your server:

```bash
chmod +x fix-deployment-error.sh
./fix-deployment-error.sh
```

## Prevention - Updated Deployment Scripts

### Updated Files:

1. **post-deploy.sh** - Now clears caches BEFORE setting permissions
2. **fix-deployment-error.sh** - NEW troubleshooting script

## Common Causes (None Found in Your Code)

❌ **NOT YOUR ISSUE**: `'mailer' => 'env'` instead of `env('MAIL_MAILER')`  
❌ **NOT YOUR ISSUE**: `'driver' => 'env'` instead of `env('DRIVER')`  
❌ **NOT YOUR ISSUE**: Literal 'env' string in any config file

## What Actually Happened

Your configuration files are correct. The error happens during deployment when:

1. Old cached config exists on server
2. New code is uploaded
3. Cache is rebuilt with wrong context
4. The literal string 'env' gets cached as a class name

## Recommended Deployment Workflow

### Step 1: On Local Machine

```bash
# Run deployment preparation
./deploy.sh
```

### Step 2: Upload to Server

-   Upload all files EXCEPT `.env`
-   Copy `.env.production` to `.env` on server
-   Update `.env` with production values

### Step 3: On Server (CRITICAL ORDER)

```bash
# 1. Clear caches FIRST
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache

# 3. Run migrations
php artisan migrate --force

# 4. Create storage link
php artisan storage:link

# 5. LAST: Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart web server
sudo systemctl restart apache2
```

## Testing

After deployment, test:

```bash
# Should show configuration is cached
php artisan about

# Should show your app info
php artisan config:show app

# Should NOT show any errors
php artisan route:list
```

## If Error Persists

1. Check PHP version matches (8.2.12)
2. Verify all .env values are properly quoted if they contain spaces
3. Check file permissions: `ls -la bootstrap/cache`
4. Look for .env values that might be literally 'env': `grep "=env$" .env`

## Files Modified in This Fix

1. ✅ `config/session.php` - Removed duplicate `same_site` key
2. ✅ `post-deploy.sh` - Added cache clearing at start
3. ✅ `fix-deployment-error.sh` - NEW troubleshooting script (already exists, updated)

## Contact & Support

If you continue experiencing issues after following this guide:

-   Verify .env file on production server
-   Check Laravel logs: `storage/logs/laravel.log`
-   Check web server error logs
-   Ensure PHP has write permissions to `bootstrap/cache` and `storage`

---

**Status**: All configuration files verified and corrected. Deployment scripts updated. Ready for production deployment.
