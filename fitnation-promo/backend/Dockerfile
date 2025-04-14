# Use official PHP image with Apache
FROM php:8.1-apache

# Install PostgreSQL PDO extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Set the working directory in the container
WORKDIR /var/www/html

# Copy application files into the container
COPY . .

# Expose container's port 8081 (for friend version)
EXPOSE 8081

# Start Apache server in foreground
CMD ["apache2-foreground"]
