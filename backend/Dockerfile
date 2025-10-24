# Sử dụng PHP + Apache
FROM php:8.2-apache
# Cài đặt extension PHP cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    # Thêm gói Client (libmariadb3) và gói Dev (libmariadb-dev)
    libmariadb3 libmariadb-dev \ 
    && rm -rf /var/lib/apt/lists/* \
    # Thêm 'pdo' và 'pdo_mysql'
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Bật rewrite module để Laravel chạy đúng route
RUN a2enmod rewrite

# Chỉnh DocumentRoot trỏ về thư mục public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Cho phép .htaccess của Laravel hoạt động
RUN echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/laravel.conf && \
    a2enconf laravel

# Copy toàn bộ code vào container
WORKDIR /var/www/html
COPY . /var/www/html

RUN usermod -u 1000 www-data


# Cài composer (nếu chưa có)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cài đặt dependency Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && php -r "file_exists('.env') || copy('.env.example', '.env');" \
    && php artisan config:clear \
    || true

# Set quyền cho Laravel storage và cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 cho Apache
EXPOSE 80
