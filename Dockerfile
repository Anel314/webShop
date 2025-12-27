FROM php:8.2-apache

# 1. PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# 2. ENABLE rewrite
RUN a2enmod rewrite

# 3. ☢️ ABSOLUTE NUCLEAR MPM FIX ☢️
# Remove ALL MPMs from EVERY possible location
RUN rm -f \
    /etc/apache2/mods-enabled/mpm_event.load \
    /etc/apache2/mods-enabled/mpm_worker.load \
    /etc/apache2/mods-enabled/mpm_prefork.load \
    /etc/apache2/mods-available/mpm_event.load \
    /etc/apache2/mods-available/mpm_worker.load

# Re-enable ONLY prefork
RUN ln -s /etc/apache2/mods-available/mpm_prefork.load \
          /etc/apache2/mods-enabled/mpm_prefork.load

# 4. Railway PORT fix
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' \
    /etc/apache2/sites-available/000-default.conf

# 5. App files
COPY . /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    chown -R www-data:www-data /var/www/html

# 6. Start Apache
CMD ["apache2-foreground"]
