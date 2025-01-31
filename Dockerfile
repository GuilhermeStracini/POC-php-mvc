FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip pdo pdo_mysql
RUN composer self-update
RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \--filename=composer && chmod +x /usr/bin/composer 

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
