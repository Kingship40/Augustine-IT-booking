FROM php:8.2-apache

# Install PostgreSQL client development libraries and PDO extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Explicitly set index.php as the primary DirectoryIndex configuration
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Reconfigure Apache directory parameters to allow global overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Enable Apache mod_rewrite for custom query path routing parameters
RUN a2enmod rewrite

# Copy all platform files into the required working directory path
COPY . /var/www/html/

# Adjust file system ownership parameters so Apache can read and execute files
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80