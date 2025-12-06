## 🚀 AgriSys Deployment Checklist

### ✅ Pre-Deployment (Local)

#### Environment Setup

-   [ ] Copy `.env.production` to `.env` and update with production values
-   [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
-   [ ] Generate new `APP_KEY` for production
-   [ ] Update `APP_URL` with your domain

#### Database Configuration

-   [ ] Update database credentials in `.env`
-   [ ] Test database connection locally
-   [ ] Backup current database (if updating)

#### Security & API Keys

-   [ ] Update `FACEBOOK_REDIRECT_URI` with production domain
-   [ ] Secure or remove `ANTHROPIC_API_KEY` (if not needed in production)
-   [ ] Set up production mail credentials

#### Build & Optimization

-   [ ] Run `./deploy.bat` (Windows) or `./deploy.sh` (Linux/Mac)
-   [ ] Verify all caches are cleared and rebuilt
-   [ ] Test application locally with production config
-   [ ] Build frontend assets with `npm run build`

### 🌐 Hostinger VPS Setup

#### Server Configuration

-   [ ] Purchase and set up Hostinger KVM 2 VPS
-   [ ] Configure domain and SSL certificate
-   [ ] Set up MySQL database
-   [ ] Ensure PHP 8.2+ is installed and configured

#### File Upload

-   [ ] Upload all files via SFTP/FTP
-   [ ] Set web root to `/public` directory
-   [ ] Upload production `.env` file
-   [ ] Exclude `.git`, `node_modules`, and development files

### 🔧 Server-Side Deployment

#### Initial Setup

-   [ ] Run `composer install --no-dev --optimize-autoloader`
-   [ ] Generate application key: `php artisan key:generate --force`
-   [ ] Run migrations: `php artisan migrate --force`
-   [ ] Seed database: `php artisan db:seed --force`
-   [ ] Create storage link: `php artisan storage:link`

#### Permissions & Security

-   [ ] Set directory permissions: `755` for folders
-   [ ] Set file permissions: `644` for files
-   [ ] Set storage permissions: `775` for storage directories
-   [ ] Verify `.env` file permissions are `600`

#### Performance Optimization

-   [ ] Cache configurations: `php artisan config:cache`
-   [ ] Cache routes: `php artisan route:cache`
-   [ ] Cache views: `php artisan view:cache`
-   [ ] Verify all caches are working

### 🧪 Testing & Verification

#### Functionality Testing

-   [ ] Homepage loads correctly
-   [ ] User registration works
-   [ ] Login functionality works
-   [ ] Application forms submit successfully
-   [ ] Analytics dashboard displays properly
-   [ ] File uploads work
-   [ ] Email notifications send

#### Performance Testing

-   [ ] Page load times are acceptable (< 3 seconds)
-   [ ] Charts and analytics render properly
-   [ ] Database queries respond quickly
-   [ ] Static assets load from CDN (if configured)

#### Security Testing

-   [ ] HTTPS is enforced
-   [ ] Security headers are present (check with online tools)
-   [ ] Hidden files are protected (test `.env` access)
-   [ ] Error pages don't expose sensitive information
-   [ ] CSRF protection is working

### 🔍 Post-Deployment Monitoring

#### Log Monitoring

-   [ ] Set up log rotation for `storage/logs/laravel.log`
-   [ ] Monitor for any 500 errors
-   [ ] Check for database connection issues
-   [ ] Monitor email delivery logs

#### Performance Monitoring

-   [ ] Set up uptime monitoring
-   [ ] Monitor server resources (CPU, RAM, disk)
-   [ ] Track page load times
-   [ ] Monitor database performance

#### Backup Setup

-   [ ] Schedule regular database backups
-   [ ] Set up file backup routine
-   [ ] Test backup restoration process
-   [ ] Document backup locations and procedures

### 🆘 Emergency Procedures

#### Common Issues & Solutions

-   [ ] Document rollback procedure
-   [ ] Prepare maintenance mode commands
-   [ ] Have database backup restoration steps ready
-   [ ] Keep local development environment synced

#### Support Contacts

-   [ ] Document hosting provider support details
-   [ ] Have domain registrar contact information
-   [ ] Maintain list of key personnel and access credentials

---

### 📞 Quick Commands Reference

```bash
# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# Rebuild all caches
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Check application status
php artisan about

# Run in maintenance mode
php artisan down

# Exit maintenance mode
php artisan up

# Check logs
tail -f storage/logs/laravel.log
```

---

### ✅ Deployment Complete When:

-   [ ] All checklist items above are completed
-   [ ] Application is accessible via production domain
-   [ ] All functionality works as expected
-   [ ] Performance meets requirements
-   [ ] Security measures are in place
-   [ ] Monitoring and backups are configured

**🎉 Your AgriSys application is now live and ready for production use!**
