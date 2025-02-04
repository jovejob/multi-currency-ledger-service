# Use PHP 8.3 as the base image
FROM php:8.3-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
  libpq-dev \
  git \
  unzip \
  && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Symfony CLI (optional for using built-in server)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
  && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Set working directory
WORKDIR /var/www/html

# Copy the Symfony project files
COPY . .

# Set the correct permissions for Symfony
RUN chown -R www-data:www-data /var/www/html

# Expose the necessary ports for the Symfony server
EXPOSE 8000

# Start Symfony built-in server with --allow-all-ip flag
CMD symfony serve --no-tls --allow-all-ip --port=8000






# # Use PHP 8.3 as the base image
# FROM php:8.3-fpm

# # Install system dependencies and PHP extensions
# RUN apt-get update && apt-get install -y \
#   libpq-dev \
#   git \
#   unzip \
#   && docker-php-ext-install pdo pdo_pgsql

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www/html

# # Copy the Symfony project files
# COPY . .

# # Set the correct permissions for Symfony
# RUN chown -R www-data:www-data /var/www/html

# # Expose PHP-FPM port
# EXPOSE 9000

# # Start PHP-FPM server
# CMD ["php-fpm"]
