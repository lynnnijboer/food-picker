#!/bin/sh
# The app is self-contained and uses SQLite instead of an external database
# service, so no cloud database resource needs to be provisioned. The image
# already ships with a migrated + seeded SQLite file (see Dockerfile), but
# some hosts don't persist build-time filesystem changes into the running
# container, so this re-creates and re-applies them on every boot as a
# safety net. Both migrate and the seeder are idempotent.
set -e

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
fi

php artisan migrate --force --seed
