# Use the official PHP 8.2 FPM image as a base
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies & Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Install PHP extensions
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy Nginx config and startup script
COPY docker/render-nginx.conf /etc/nginx/sites-available/default
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 10000 for Render
EXPOSE 10000

# Set the entrypoint
CMD ["/usr/local/bin/start.sh"]
