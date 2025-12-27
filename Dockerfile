FROM php:8.2-apache

# Remove ALL MPM modules
RUN rm -f /etc/apache2/mods-enabled/mpm_*

# Explicitly enable prefork MPM
RUN a2enmod mpm_prefork

# Enable rewrite for Flight
RUN a2enmod rewrite

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy app
COPY . /var/www/html

# Allow .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
