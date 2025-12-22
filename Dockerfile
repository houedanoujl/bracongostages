# Multi-stage Dockerfile pour Laravel BRACONGO
FROM php:8.2-fpm as base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Redis extension using pickle (more reliable than pecl)
RUN curl -L -o /tmp/pickle.phar https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar \
    && php /tmp/pickle.phar install redis --defaults \
    && docker-php-ext-enable redis \
    && rm /tmp/pickle.phar

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Development stage
FROM base as development

# Install Xdebug for development (using pickle for reliability)
RUN curl -L -o /tmp/pickle.phar https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar \
    && php /tmp/pickle.phar install xdebug --defaults \
    && docker-php-ext-enable xdebug \
    && rm /tmp/pickle.phar

# Copy application
COPY . .

# Make scripts executable
RUN chmod +x /var/www/docker/scripts/*.sh

# Create Laravel directories if they don't exist and set permissions
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Production stage
FROM base as production

# Install production PHP extensions and optimizations
RUN docker-php-ext-install opcache

# Copy optimized PHP configuration
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"] 