#!/bin/sh
set -e

if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_URL:-}" ]; then
  export DB_URL="$DATABASE_URL"
fi

# Render injects PORT; artisan serve needs it.
export PORT="${PORT:-10000}"

# Prefer completing boot even if a non-critical migrate step races; log failures to stderr.
php artisan migrate --force || echo "migrate failed" >&2
# Insert-only CRM demo rows (firstOrCreate). Never truncates or overwrites existing data.
php artisan db:seed --class=SafeDemoCrmSeeder --force || true
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

if [ "$#" -eq 0 ]; then
  set -- php artisan serve --host=0.0.0.0 --port="$PORT"
elif [ "$1" = "php" ] && [ "${2:-}" = "artisan" ] && [ "${3:-}" = "serve" ]; then
  set -- php artisan serve --host=0.0.0.0 --port="$PORT"
fi

exec "$@"
