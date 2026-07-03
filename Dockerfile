FROM php:8.2-apache

# Install PostgreSQL development libraries and PDO extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite for your routing systems
RUN a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80