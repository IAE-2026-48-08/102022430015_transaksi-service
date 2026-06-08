#!/bin/bash

echo "==> Copying .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "==> Installing dependencies..."
composer install --no-dev --no-interaction --ignore-platform-reqs --no-scripts
composer dump-autoload --optimize --no-scripts

echo "==> Generating app key..."
php artisan key:generate --force --no-interaction || true

echo "==> Waiting for MySQL to be ready..."
until php -r "new PDO('mysql:host=db;port=3306;dbname=transaction_db', 'laravel', 'secret');" 2>/dev/null; do
    echo "MySQL not ready yet, retrying in 3s..."
    sleep 3
done

echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Seeding database..."
php artisan db:seed --force --no-interaction

echo "==> Publishing Swagger config..."
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --no-interaction || true

echo "==> Generating Swagger docs..."
php artisan l5-swagger:generate || true

echo "==> Clearing caches..."
php artisan config:clear
php artisan cache:clear

echo "==> Starting PHP-FPM..."
exec php-fpm