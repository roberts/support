# Dockerfile for Laravel 12 on PHP 8.4 - Google Cloud Run
# Multi-stage build: Builder stage for assets, Production stage for runtime

# ============================================
# Builder Stage: Node.js for Vite assets
# ============================================
FROM node:22-alpine AS builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install Node dependencies
RUN npm ci --no-audit --no-fund

# Copy application source (needed for Vite build)
COPY . .

# Build production assets with Vite
RUN npm run build

# ============================================
# Production Stage: PHP 8.4 with Apache
# ============================================
FROM php:8.4-apache

LABEL maintainer="{{ $projectId }}"
LABEL description="Laravel 12 Application"

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Custom PHP configuration
RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=20M" > $PHP_INI_DIR/conf.d/uploads.ini \
    && echo "post_max_size=20M" >> $PHP_INI_DIR/conf.d/uploads.ini \
    && echo "max_execution_time=300" >> $PHP_INI_DIR/conf.d/timeouts.ini

# Configure OPcache for production
RUN echo "opcache.enable=1" > $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.save_comments=1" >> $PHP_INI_DIR/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> $PHP_INI_DIR/conf.d/opcache.ini

# Configure Apache DocumentRoot to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set Apache to run as www-data (security best practice)
RUN echo "ServerTokens Prod" >> /etc/apache2/conf-available/security.conf \
    && echo "ServerSignature Off" >> /etc/apache2/conf-available/security.conf

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only, optimized)
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Copy application code
COPY . .

# Copy built assets from builder stage
COPY --from=builder /app/public/build ./public/build

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Run Composer scripts
RUN composer dump-autoload --optimize --classmap-authoritative

# Generate optimized Laravel caches
RUN php artisan event:cache || true

# Copy and set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 8080 (Cloud Run default)
EXPOSE 8080

# Configure Apache to listen on PORT environment variable
RUN sed -i 's/Listen 80/Listen ${PORT:-8080}/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:${PORT:-8080}/g' /etc/apache2/sites-available/000-default.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/up || exit 1

# Switch to non-root user for security
USER www-data

# Use entrypoint script to run migrations and start Apache
ENTRYPOINT ["docker-entrypoint.sh"]
