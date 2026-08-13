#!/bin/sh
set -e

if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_URL:-}" ]; then
  export DB_URL="$DATABASE_URL"
fi

# Render injects PORT; artisan serve needs it.
export PORT="${PORT:-10000}"

php artisan migrate --force
# Insert-only CRM demo rows (firstOrCreate). Never truncates or overwrites existing data.
php artisan db:seed --class=SafeDemoCrmSeeder --force || true
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "$#" -eq 0 ]; then
  set -- php artisan serve --host=0.0.0.0 --port="$PORT"
elif [ "$1" = "php" ] && [ "${2:-}" = "artisan" ] && [ "${3:-}" = "serve" ]; then
  set -- php artisan serve --host=0.0.0.0 --port="$PORT"
fi

exec "$@"
