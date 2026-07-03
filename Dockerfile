FROM php:8.2-apache

# Install PostgreSQL client development libraries and PDO extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Explicitly change Apache's DocumentRoot pointing to the public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Reconfigure Apache directory parameters to allow global overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Enable Apache mod_rewrite for custom query path routing parameters
RUN a2enmod rewrite

# Copy all platform files into the container
COPY . /var/www/html/

# Adjust file system ownership parameters so Apache can read and execute files cleanly
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
