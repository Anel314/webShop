FROM php:8.2-apache

# 1. Install system dependencies & PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# 2. Enable rewrite for Flight
RUN a2enmod rewrite

# 3. Allow .htaccess (Best practice: target the specific directory)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 4. Copy app files
COPY . /var/www/html

# 5. Set permissions
RUN chown -R www-data:www-data /var/www/html

# Railway uses a dynamic PORT, so we don't hardcode 80 in the setup
# but we keep EXPOSE for documentation.
EXPOSE 80