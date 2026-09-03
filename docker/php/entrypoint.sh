#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required. Generate it before starting the production stack." >&2
    exit 1
fi

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link --no-interaction
fi

if [ "${WAIT_FOR_DATABASE:-true}" = "true" ]; then
    attempts=0
    until php artisan db:show --no-interaction >/dev/null 2>&1; do
        attempts=$((attempts + 1))
        if [ "$attempts" -ge 60 ]; then
            echo "Database is not available after 60 attempts." >&2
            exit 1
        fi
        sleep 2
    done
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${RUN_BOOTSTRAP_SEED:-false}" = "true" ]; then
    php artisan db:seed --class=Database\\Seeders\\ProductionSeeder --force --no-interaction
fi

if [ "${CACHE_CONFIGURATION:-true}" = "true" ]; then
    php artisan optimize --no-interaction
fi

exec "$@"
