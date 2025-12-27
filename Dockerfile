FROM php:8.2-fpm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install nginx
RUN apt-get update && apt-get install -y nginx

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy app
COPY . /var/www/html
WORKDIR /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Railway port
EXPOSE 8080

# Start both services
CMD php-fpm -D && nginx -g "daemon off;"
