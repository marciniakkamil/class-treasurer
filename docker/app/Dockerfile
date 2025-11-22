# syntax=docker/dockerfile:1.7

# PHP 8.4 CLI for running Artisan and PHP built-in server
FROM php:8.4-cli

# Install system dependencies and PHP extensions needed by Laravel
# - sqlite3 / pdo_sqlite for default DB
# - pdo_mysql for MySQL support
# - pcntl for queue:work signals
# - zip (common for composer packages)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libsqlite3-dev; \
    docker-php-ext-configure pcntl --enable-pcntl; \
    docker-php-ext-install -j$(nproc) \
        pcntl \
        sockets \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        zip; \
    rm -rf /var/lib/apt/lists/*

# Install Composer (copy from official image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Default user can remain root for simplicity in local dev; volumes will map to host perms.
# If you prefer, uncomment to run as non-root:
# RUN useradd -ms /bin/bash laravel && chown -R laravel:laravel /var/www/html
# USER laravel

# Expose the port used by `php artisan serve`
EXPOSE 8000

# The command is provided in docker-compose to allow overriding.
CMD ["php", "-v"]
