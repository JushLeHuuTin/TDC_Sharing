# Sử dụng image PHP có sẵn Apache
FROM php:8.2-apache

# Cài extension cần cho Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy code từ backend vào container
COPY . .

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Phân quyền cho storage và cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
