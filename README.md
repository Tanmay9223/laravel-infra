# Laravel v12 Project


A brief description of what your Laravel application does.

## 🚀 Features

- Laravel 12 with Passport API Authentication
- User and Admin roles
- RESTful API endpoints
- Database seeding with factories
- Modular service structure

---

## 📦 Requirements

- PHP >= 8.2
- Composer
- Web Server(Apache or Nginx)
- PostgreSQL

---

## 🛠️ Installation

# 1. Clone the repo
git clone https://github.com/your-name/your-laravel-app.git
cd your-laravel-app

# 2. Install PHP dependencies
composer install

# 3. Copy .env file and configure
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Set up your .env file
- Configure DB settings
- Set APP_URL
- Set MAIL_ credentials

# 6. Run database migrations and seeders
php artisan migrate --seed

# 7. Link storage (for public file uploads)
php artisan storage:link

# 8. Install encryption keys and clients
php artisan passport:install

Encryption keys not found. Do you want to generate them? (yes/no) [no]:
> yes

Would you like to run the migrations? (yes/no) [no]:
> yes

Would you like to create the clients now? (yes/no) [yes]: / Would you like to create the "personal access" and "password grant" clients? (yes/no) [yes]:
> no

# 9. Create a personal access client
php artisan passport:client --personal

# 10. You can now run the application:
php artisan serve

