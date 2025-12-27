FROM php:8.2-apache

# 1. Forcefully disable the event MPM and enable prefork at the system level
# We use '|| true' so the build doesn't fail if they are already toggled
RUN a2dismod mpm_event || true && a2enmod mpm_prefork || true

# 2. Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# 3. Enable rewrite
RUN a2enmod rewrite

# 4. Railway Port Fix (CRITICAL)
# Apache defaults to 80, but Railway expects the $PORT variable.
# This ensures Apache listens to what Railway tells it to.
RUN sed -i "s/Listen 80/Listen \${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:\${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# 5. App Setup
COPY . /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN chown -R www-data:www-data /var/www/html

# Start Apache in the foreground
CMD ["apache2-foreground"]