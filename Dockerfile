FROM php:8.2-apache

# Enable Apache rewrite module for clean routing
RUN a2enmod rewrite

# Install build dependencies, clear apt cache to keep images clean, and compile extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache document root to point to your public asset framework folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy project files into the live container working directory
COPY . /var/www/html/

# Apply correct file ownership permissions for Apache web server runtime operations
RUN chown -R www-data:www-data /var/www/html