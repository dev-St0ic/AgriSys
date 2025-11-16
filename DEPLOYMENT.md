# 🚀 AgriSys Production Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. **Local Environment Setup**

-   [x] PHP 8.2+ installed
-   [x] Composer installed
-   [x] Node.js & NPM installed
-   [x] Laravel application working locally

### 2. **Hostinger VPS Setup**

-   [ ] Purchase Hostinger KVM 2 plan (recommended)
-   [ ] Set up domain/subdomain
-   [ ] Configure DNS settings
-   [ ] Enable SSL certificate

### 3. **Database Setup**

-   [ ] Create MySQL database in Hostinger panel
-   [ ] Note database credentials (name, username, password)
-   [ ] Ensure MySQL version compatibility

---

## 🔧 Step-by-Step Deployment Process

### **Phase 1: Local Preparation**

#### 1. Run the deployment preparation script:

**For Windows:**

```bash
deploy.bat
```

**For Linux/Mac:**

```bash
chmod +x deploy.sh
./deploy.sh
```

#### 2. Create production environment file:

```bash
# Copy the production template
cp .env.production .env.server

# Edit with your production settings
# Update these critical values:
# - APP_URL=https://yourdomain.com
# - DB_HOST=localhost
# - DB_DATABASE=your_database_name
# - DB_USERNAME=your_db_username
# - DB_PASSWORD=your_db_password
# - MAIL_* settings
# - FACEBOOK_REDIRECT_URI
# - Remove/secure API keys
```

#### 3. Test production build locally:

```bash
# Test with production config
php artisan config:cache
php artisan serve
```

### **Phase 2: File Upload**

#### 1. **Upload Methods** (Choose one):

**Option A: FTP/SFTP**

```bash
# Upload all files to your domain's public_html folder
# Exclude: .git, node_modules, .env
```

**Option B: Git Deployment**

```bash
# On server (if Git is available)
git clone https://github.com/dev-St0ic/AgriSys.git
cd AgriSys/agrisys
```

#### 2. **File Structure on Server:**

```
/public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Web root should point here
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env            ← Your production environment file
├── artisan
├── composer.json
└── ...
```

### **Phase 3: Server Configuration**

#### 1. **Set up environment:**

```bash
# Copy your production environment file
cp .env.server .env

# Install dependencies (if not done locally)
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate --force
```

#### 2. **Configure database:**

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force
```

#### 3. **Set permissions:**

```bash
# For shared hosting (via file manager)
# Set folders to 755: storage/, bootstrap/cache/
# Set files to 644: most files

# For VPS (via SSH)
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage/app storage/logs
```

#### 4. **Create storage link:**

```bash
php artisan storage:link
```

#### 5. **Final optimization:**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Phase 4: Web Server Configuration**

#### **Apache (.htaccess already configured)**

-   Ensure mod_rewrite is enabled
-   Point document root to `/public` folder
-   Security headers are automatically applied

#### **Nginx Configuration** (if using):

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/agrisys/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔐 Security Configuration

### 1. **Environment Variables**

```bash
# Critical settings in .env:
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

# Remove or secure these in production:
# ANTHROPIC_API_KEY (use secure environment or remove)
# Any exposed passwords or secrets
```

### 2. **File Permissions**

```bash
# Recommended permissions:
# Directories: 755
# Files: 644
# Storage: 775
# Sensitive files (.env): 600
```

### 3. **Security Headers**

The SecurityHeaders middleware is automatically applied in production.

---

## 🧪 Testing Your Deployment

### 1. **Basic Functionality Test:**

-   [ ] Homepage loads correctly
-   [ ] User registration works
-   [ ] Login functionality
-   [ ] Application forms submit
-   [ ] Analytics dashboard displays
-   [ ] Email notifications send

### 2. **Performance Test:**

-   [ ] Page load times < 3 seconds
-   [ ] Analytics charts render properly
-   [ ] File uploads work
-   [ ] Database queries respond quickly

### 3. **Security Test:**

-   [ ] HTTPS is enforced
-   [ ] Security headers present
-   [ ] Hidden files are protected
-   [ ] Error pages don't expose sensitive info

---

## 📊 Post-Deployment Optimization

### 1. **CDN Setup (Optional but Recommended):**

```bash
# Cloudflare setup:
# 1. Add domain to Cloudflare
# 2. Update nameservers
# 3. Enable auto-minification
# 4. Set caching rules
```

### 2. **Monitoring Setup:**

```bash
# Log monitoring
tail -f storage/logs/laravel.log

# Performance monitoring
php artisan queue:work --daemon
```

### 3. **Backup Strategy:**

```bash
# Database backup (schedule via cron)
mysqldump -u username -p database_name > backup.sql

# File backup
tar -czf backup.tar.gz /path/to/agrisys
```

---

## 🆘 Troubleshooting Common Issues

### **500 Internal Server Error:**

```bash
# Check logs
tail storage/logs/laravel.log

# Common fixes:
php artisan config:clear
chmod -R 755 storage bootstrap/cache
```

### **Database Connection Error:**

```bash
# Verify .env settings:
# DB_HOST (usually 'localhost' for shared hosting)
# DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Test connection:
php artisan migrate:status
```

### **Missing Storage Link:**

```bash
# Recreate storage link:
rm public/storage
php artisan storage:link
```

### **Permission Denied Errors:**

```bash
# Fix permissions:
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### **Assets Not Loading:**

```bash
# Clear compiled assets:
php artisan view:clear
npm run build

# Check public path in config/app.php
```

---

## 🎯 Success Indicators

✅ **Your deployment is successful when:**

-   Homepage loads without errors
-   Users can register and login
-   Applications can be submitted
-   Analytics dashboard shows data
-   Email notifications are sent
-   All static assets load properly
-   HTTPS is working
-   No 500/404 errors in logs

---

## 📞 Support

If you encounter issues:

1. Check `storage/logs/laravel.log` for error details
2. Verify .env configuration matches your hosting setup
3. Ensure all file permissions are correct
4. Test database connectivity
5. Confirm PHP version compatibility (8.2+)

**Remember:** Always backup your database and files before making changes!

---

_This deployment guide ensures your AgriSys application runs smoothly in production with optimal security and performance settings._
