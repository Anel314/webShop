FROM php:8.2-apache

# Disable all MPMs first
RUN a2dismod mpm_event mpm_worker || true

# Enable prefork MPM
RUN a2enmod mpm_prefork

# Enable rewrite (Flight needs it)
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy project
COPY . /var/www/html/

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
