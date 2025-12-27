FROM php:8.2-apache

# 1. Install extensions & Rewrite
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite

# 2. THE NUCLEAR FIX: 
# We delete the 'mods-enabled' links entirely and recreate only the ones we want.
# This prevents the 'pre-run' scripts from seeing multiple MPM choices.
RUN rm /etc/apache2/mods-enabled/mpm_event.load || true
RUN rm /etc/apache2/mods-enabled/mpm_worker.load || true
RUN ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

# 3. Railway Port Fix
# Railway sets a $PORT env var; Apache must use it or the healthcheck fails.
RUN sed -i "s/Listen 80/Listen \${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:\${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# 4. App Setup
COPY . /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN chown -R www-data:www-data /var/www/html

# 5. Force Apache to start with a clean environment
ENTRYPOINT ["docker-php-entrypoint"]
CMD ["apache2-foreground"]