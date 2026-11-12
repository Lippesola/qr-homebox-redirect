# Install PHP dependencies in a separate stage, so git/unzip/composer stay out of the final image
FROM composer:2@sha256:ca11f643d7791ebdd90a34fc20caf11380d434007ae539a9ea75cd9212e13dca AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM php:8.4.27-apache-trixie@sha256:884f64b47174de2c0e813270fe7b24a18605729fcebe201302d98c38805530d0

# Production PHP settings (no error output to visitors), mod_rewrite for .htaccess
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite

WORKDIR /var/www/html

# Files stay owned by root: Apache workers (www-data) only need to read them
COPY --from=vendor /app/vendor ./vendor
COPY app/ ./

EXPOSE 80

LABEL org.opencontainers.image.source="https://github.com/Lippesola/qr-homebox-redirect"
