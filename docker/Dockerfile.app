# Use the official PHP image as a base image
FROM php:8.3.8-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    vim \
    sudo \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring pdo pdo_mysql zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Set working directory
WORKDIR /var/www

RUN php artisan key:generate
RUN sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/g" .env
RUN sed -i "s/DB_DATABASE=.*/DB_DATABASE=laravel_db/g" .env
RUN sed -i "s/DB_USERNAME=.*/DB_USERNAME=laravel_user/g" .env
RUN sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=your_password/g" .env

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
