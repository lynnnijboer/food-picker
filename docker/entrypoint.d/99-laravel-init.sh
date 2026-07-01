#!/bin/sh
# Render's free plan does not run preDeployCommand, so database migrations and
# seeding happen here on container start instead. Both are idempotent.
set -e

# On SQLite the database file lives on Render's ephemeral filesystem, so it is
# (re)created on every boot. migrate + seed then rebuild the schema and data.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
fi

php artisan migrate --force --seed
