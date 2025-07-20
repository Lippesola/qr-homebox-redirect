FROM php:8.2-apache

# Copy PHP files into Apache document root
COPY app/ /var/www/html/

# Expose port 80
EXPOSE 80
