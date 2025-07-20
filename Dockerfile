FROM php:8.4-apache

# Copy PHP files into Apache document root
COPY app/ /var/www/html/

# Expose port 80
EXPOSE 80
