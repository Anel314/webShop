FROM php:8.2-apache

# 1. PHP extensions + rewrite
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# 2. HARD FIX: ensure ONLY prefork is enabled
RUN a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork

# 3. Railway PORT support
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' \
    /etc/apache2/sites-available/000-default.conf

# 4. App setup
COPY . /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && chown -R www-data:www-data /var/www/html

# 5. Start Apache
CMD ["apache2-foreground"]
