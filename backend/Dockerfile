FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip curl \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql pgsql zip bcmath gd intl mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan storage:link || true

ENV PORT=10000
EXPOSE 10000

CMD php artisan migrate --force \
  && php artisan config:cache \
  && php artisan route:cache \
  && php artisan view:cache \
  && php artisan serve --host=0.0.0.0 --port=${PORT}
