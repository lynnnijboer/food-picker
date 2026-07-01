# syntax=docker/dockerfile:1
FROM serversideup/php:8.4-fpm-nginx

USER root
WORKDIR /var/www/html

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js 22 (needed by the Vite / Wayfinder asset build)
RUN apt-get update \
    && apt-get install -y ca-certificates curl gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY . .

# PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build front-end assets. The Wayfinder Vite plugin calls `php artisan`, so a
# temporary .env + app key lets the framework boot during the build.
RUN cp .env.example .env \
    && php artisan key:generate \
    && npm ci \
    && npm run build \
    && rm -f .env \
    && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data
