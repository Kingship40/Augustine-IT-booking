FROM php:8.2-apache

# Install PostgreSQL client development libraries and PDO extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Configure Apache to allow overrides and use correct directory path parameters
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite for your query routes
RUN a2enmod rewrite

# Copy all platform files into the required Apache working folder path
COPY . /var/www/html/

# Adjust file system permissions so Apache can execute files cleanly
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80