# 🌾 AgriSys - Agricultural Management System

A comprehensive Laravel-based agricultural management system with role-based authentication and admin panel.

## 📋 Prerequisites

Before setting up the project, ensure you have the following installed:

-   **XAMPP** (PHP 8.1+, MySQL, Apache) - [Download here](https://www.apachefriends.org/)
-   **Composer** (PHP dependency manager) - [Download here](https://getcomposer.org/)
-   **Node.js & npm** (for frontend assets) - [Download here](https://nodejs.org/)
-   **Git** (version control) - [Download here](https://git-scm.com/)

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone [your-repository-url]
cd AgriSys/agrisys
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

#### Start XAMPP Services

-   Open XAMPP Control Panel
-   Start **Apache** and **MySQL** services

#### Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `agrisys`
3. Configure your `.env` file:

```env
APP_NAME=AgriSys
APP_ENV=local
APP_KEY=base64:[generated-key]
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agrisys
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Database Migrations & Seeders

```bash
# Create database tables
php artisan migrate

# Seed database with default users
php artisan db:seed
```

### 7. Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link
```

### 8. Start Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## 🔑 Default Login Credentials

| Role            | Email                    | Password      |
| --------------- | ------------------------ | ------------- |
| **Super Admin** | `superadmin@agrisys.com` | `password123` |
| **Admin**       | `admin@agrisys.com`      | `password123` |
| **User**        | `user@agrisys.com`       | `password123` |

## ✨ Features

### 🔐 Authentication System

-   **Role-based Access Control** (User, Admin, SuperAdmin)
-   **Secure Login/Logout** functionality
-   **Protected Admin Routes** with middleware

### 👥 Admin Management (SuperAdmin Only)

-   **Create** new admin users
-   **View** admin user details and permissions
-   **Edit** admin user information and roles
-   **Delete** admin users (with self-protection)
-   **List** all admin users with pagination

### 🎨 Modern UI/UX

-   **Responsive Design** with Bootstrap 5
-   **Professional Admin Dashboard** with statistics
-   **Font Awesome Icons** throughout the interface
-   **Clean Sidebar Navigation**
-   **Flash Messages** for user feedback

## 🛠️ Tech Stack

-   **Backend:** Laravel 11
-   **Frontend:** Bootstrap 5, Font Awesome
-   **Database:** MySQL
-   **Server:** Apache (XAMPP)
-   **Package Manager:** Composer, npm

## 📁 Project Structure

```
agrisys/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Authentication logic
│   │   │   └── AdminController.php     # Admin CRUD operations
│   │   └── Middleware/
│   │       └── AdminMiddleware.php     # Admin route protection
│   └── Models/
│       └── User.php                    # User model with roles
├── database/
│   ├── migrations/
│   │   └── add_role_to_users_table.php # Role field migration
│   └── seeders/
│       └── SuperAdminSeeder.php        # Default users seeder
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # Main layout
│       ├── auth/
│       │   └── login.blade.php         # Login page
│       └── admin/
│           ├── dashboard.blade.php     # Admin dashboard
│           └── admins/                 # Admin management views
├── routes/
│   └── web.php                         # Application routes
└── README.md                          # This file
```

## 🔧 Common Issues & Solutions

### Issue: Composer commands not working

**Solution:**

```bash
# Use full PHP path if needed
C:\xampp\php\php.exe artisan migrate
```

### Issue: Database connection errors

**Solutions:**

-   Ensure XAMPP MySQL service is running
-   Verify database name exists in phpMyAdmin
-   Check database credentials in `.env` file

### Issue: Permission errors

**Solutions:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan storage:link
```

### Issue: Routes not working

**Solution:**

```bash
php artisan route:clear
php artisan optimize:clear
```

## 🚀 Development Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run specific seeder
php artisan db:seed --class=SuperAdminSeeder

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName

# Create new middleware
php artisan make:middleware MiddlewareName

# Fresh migration (drops all tables)
php artisan migrate:fresh --seed
```

## 📝 Contributing

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Coding Standards

-   Follow **PSR-12** coding standards
-   Use **meaningful variable and function names**
-   Add **comments** for complex logic
-   Write **clean, readable code**

## 🐛 Troubleshooting

### XAMPP Issues

-   Ensure no other applications are using ports 80 (Apache) and 3306 (MySQL)
-   Check XAMPP error logs in `xampp/apache/logs/` and `xampp/mysql/data/`

### Laravel Issues

-   Check Laravel logs in `storage/logs/laravel.log`
-   Ensure proper file permissions on `storage/` and `bootstrap/cache/`

### Database Issues

-   Verify MySQL service is running in XAMPP
-   Check database exists and credentials are correct
-   Ensure migrations have been run

## 📞 Support

If you encounter any issues:

1. Check the **troubleshooting section** above
2. Review **Laravel documentation**: https://laravel.com/docs
3. Check **project issues** on GitHub
4. Contact the development team

## 📄 License

This project is licensed under the MIT License. See the LICENSE file for details.

---

**🌾 Happy Farming with AgriSys! 🚜**
