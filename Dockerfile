FROM php:8.4-apache

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan (termasuk libicu-dev untuk intl)
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql zip intl gd

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Set Apache DocumentRoot ke folder /public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Install dependensi PHP tanpa mengecek batasan platform tertentu (jika ada)
RUN composer install --no-dev --optimize-autoloader

# Perbaiki permission storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
