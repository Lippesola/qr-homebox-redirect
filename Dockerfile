# Install PHP dependencies in a separate stage, so git/unzip/composer stay out of the final image
FROM composer:2@sha256:5b2137f112f939a92788ceb5e55f54859d9dbf467a44037cf7760bdda949f365 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM php:8.5.13-apache-trixie@sha256:256c25149bca6639f538ece3c5dda90684409227dec546b87ca174950569b8f1

# Production PHP settings (no error output to visitors), mod_rewrite for .htaccess
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite

WORKDIR /var/www/html

# Files stay owned by root: Apache workers (www-data) only need to read them
COPY --from=vendor /app/vendor ./vendor
COPY app/ ./

EXPOSE 80

LABEL org.opencontainers.image.source="https://github.com/Lippesola/qr-homebox-redirect"
