FROM php:8.2-fpm

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y software-properties-common
RUN apt-get update

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
       git \
       curl \
       libpng-dev \
       libonig-dev \
       libxml2-dev \
       zip \
       unzip \
       libcurl4-openssl-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install dom
RUN docker-php-ext-install curl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

RUN chown -R www:www /var/www
RUN chmod -R 755 /var/www/storage

# Change current user to www
USER www

RUN composer install --no-interaction --no-plugins --no-scripts --no-dev

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
