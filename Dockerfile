# Install PHP dependencies in a separate stage, so git/unzip/composer stay out of the final image
FROM composer:2@sha256:cd3a90dc5624c59a2f9ddf80dad08569f06416db31de519024f249d63bca554b AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM php:8.5.13-apache-trixie@sha256:d24a308e0ef159f2abec5fcaf4a5bb7c71c1d5ccc0b92f3c77e69c52173e4e4c

# Production PHP settings (no error output to visitors), mod_rewrite for .htaccess
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite

WORKDIR /var/www/html

# Files stay owned by root: Apache workers (www-data) only need to read them
COPY --from=vendor /app/vendor ./vendor
COPY app/ ./

EXPOSE 80

LABEL org.opencontainers.image.source="https://github.com/Lippesola/qr-homebox-redirect"
