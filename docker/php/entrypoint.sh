#!/usr/bin/env sh
set -e

cd /var/www/html

# Ensure Laravel can write to storage/cache (on macOS bind-mount chown may be blocked; ignore errors)
mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ] && [ -f composer.json ]; then
  composer install --no-interaction --prefer-dist
fi

# Generate app key only if APP_KEY is not set
if [ -f artisan ]; then
  php -r 'exit((bool)getenv("APP_KEY") ? 0 : 1);' 2>/dev/null || php artisan key:generate --force || true
fi

exec "$@"

