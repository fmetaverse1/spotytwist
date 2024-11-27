# Use the official PHP image from Docker Hub
FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy the application code into the container
COPY . /var/www/html

# Set permissions for the web root
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Expose the default Apache port
EXPOSE 80

# Set the working directory
WORKDIR /var/www/html
