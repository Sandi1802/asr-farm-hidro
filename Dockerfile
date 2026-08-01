FROM php:8.1-apache

# Suppress interactive dialogs during build
ENV DEBIAN_FRONTEND=noninteractive


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files (excludes .env via .dockerignore)
COPY . .

# Configure Apache - use our custom vhost
COPY docker-apache.conf /etc/apache2/sites-available/000-default.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install NPM and build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start script
COPY docker-start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
