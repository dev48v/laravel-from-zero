# Production image for Render.
#
# Render's free web-service tier expects the container to bind to $PORT.
# Laravel's built-in server is fine for a demo — we're proxying TheMealDB,
# so request volume is low and there's no heavy app state.

FROM php:8.4-cli-alpine

# Install git + unzip so composer can grab packages.
RUN apk add --no-cache git unzip

# Install Composer into the image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency manifests first so the install layer caches across builds.
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --prefer-dist --no-progress --no-scripts \
    && composer clear-cache

# Copy the rest of the source.
COPY . .

# Post-install tasks that need the full tree.
RUN composer dump-autoload --optimize --no-dev \
    && php artisan config:clear \
    && mkdir -p storage/framework/sessions storage/framework/views \
                storage/framework/cache/data storage/logs database \
    && touch database/database.sqlite \
    && chmod -R 777 storage bootstrap/cache database

# Render injects $PORT at runtime (usually 10000). APP_KEY + every other
# config value comes from Render's envVars, so no .env file is needed and
# `key:generate` isn't called at boot (it would fail trying to write .env).
EXPOSE 10000
CMD sh -c 'php artisan config:cache && php -S 0.0.0.0:${PORT:-10000} -t public'
