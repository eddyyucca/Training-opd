# Deployment Guide

## Server Requirements

- PHP 8.2+
- MySQL 8+ or MariaDB
- Composer
- Node.js and npm
- Web server pointing to the `public` directory

## Environment Setup

1. Copy `.env.example` to `.env`
2. Create an empty MySQL database for the app, for example `opd`
3. Fill these values:

```env
APP_NAME="Organization and People Development"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Asia/Makassar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opd
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

## Optional Testing Database

If you run automated tests on MySQL too, create a separate database such as `opd_testing` and copy `.env.testing.example` to `.env.testing`.

## First Deploy

Run these commands on the server:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm install
npm run build
php artisan optimize
```

## Web Root

Point the domain or subdomain document root to:

```text
/path-to-project/public
```

## Writable Paths

Make sure these directories are writable:

- `storage`
- `bootstrap/cache`

## Recommended Production Commands

If you update the app:

```bash
php artisan migrate --force
npm run build
php artisan optimize:clear
php artisan optimize
```

## Optional Queue Worker

If database queue is used in production, run:

```bash
php artisan queue:work --tries=1 --timeout=0
```
