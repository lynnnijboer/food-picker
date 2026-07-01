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

# Build front-end assets and provision the database. The Wayfinder Vite
# plugin calls `php artisan`, so a temporary .env + app key lets the
# framework boot during the build. The app is self-contained: it uses SQLite
# rather than an external database service, so no cloud database resource
# needs to be provisioned. The SQLite file is migrated and seeded here so it
# ships pre-populated inside the image.
RUN cp .env.example .env \
    && php artisan key:generate \
    && npm ci \
    && npm run build \
    && mkdir -p database \
    && touch database/database.sqlite \
    && php artisan migrate --force --seed \
    && rm -f .env \
    && rm -rf node_modules

# Re-run migrations on container start too, in case the host doesn't persist
# build-time filesystem changes into the running container. serversideup
# executes /etc/entrypoint.d/*.sh before boot; migrate + the seeder are both
# idempotent, so repeating them here is safe.
COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/

# `database` is writable so the SQLite file can be updated at runtime.
RUN chown -R www-data:www-data storage bootstrap/cache database

USER www-data
