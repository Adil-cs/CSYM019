FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo \
    pdo_mysql \
    gd

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Create necessary directories
RUN mkdir -p /var/www/html/uploads/events \
    && mkdir -p /var/www/html/css \
    && mkdir -p /var/www/html/config \
    && mkdir -p /var/www/html/public \
    && mkdir -p /var/www/html/Admin

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod -R 755 /var/www/html/Admin \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 755 /var/www/html/config

# Expose port 80
EXPOSE 80