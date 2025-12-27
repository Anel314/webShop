FROM php:8.2-apache

# Enable Apache rewrite (Flight needs this)
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Copy project
COPY . /var/www/html/

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
