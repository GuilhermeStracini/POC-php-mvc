FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
