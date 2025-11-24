FROM php:8.5-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    zlib1g-dev && rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install zip pdo pdo_mysql && \
    a2enmod rewrite && \
    curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \
    --filename=composer && chmod +x /usr/bin/composer && \
    composer self-update

WORKDIR /var/www/html

COPY ./app /var/www/html/app
COPY ./public /var/www/html/public
COPY ./src /var/www/html/src
COPY .htaccess /var/www/html/
COPY composer.json /var/www/html/composer.json
COPY composer.lock /var/www/html/composer.lock

RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
