# Install PHP dependencies in a separate stage, so git/unzip/composer stay out of the final image
FROM composer:2@sha256:30621647c7caf81d0a173f42b5e46ab7fed94d5a0e19e7259429517bb9f9d342 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM php:8.5.13-apache-trixie@sha256:fcf1877806085ea8ceec1072f93c1fed4df701f5da21ff5c976c3c362e4b7859

# Production PHP settings (no error output to visitors), mod_rewrite for .htaccess
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite

WORKDIR /var/www/html

# Files stay owned by root: Apache workers (www-data) only need to read them
COPY --from=vendor /app/vendor ./vendor
COPY app/ ./

EXPOSE 80

LABEL org.opencontainers.image.source="https://github.com/Lippesola/qr-homebox-redirect"
