#!/bin/sh
# Render's free plan does not run preDeployCommand, so database migrations and
# seeding happen here on container start instead. Both are idempotent.
set -e

php artisan migrate --force --seed
