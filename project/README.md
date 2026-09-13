# Impact Web Application

For comprehensive project documentation, architecture overviews, and feature walk-throughs, please refer to the [Root README.md](../README.md).

## Quickstart Commands

```bash
# 1. Dependencies
composer install
npm install

# 2. Environment & Database
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# 3. Build Assets & Serve
npm run build
php artisan serve
```

## Running Automated Tests

```bash
php artisan test
```
