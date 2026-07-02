FROM php:8.2-apache

# Enable Apache rewrite module for routing
RUN a2enmod rewrite

# Install PostgreSQL or MySQL drivers if your database layer requires them
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Configure Apache document root to look into your new public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy your organized project files into the container
COPY . /var/www/html/

# Set correct file permissions
RUN chown -R www-data:www-data /var/www/html