FROM php:7.2.7-fpm-alpine3.7
RUN apk update; \
    apk upgrade;
RUN docker-php-ext-install mysqli

COPY config/php.ini /usr/local/etc/php/
RUN chown -R www-data:www-data /var/www/html/auction
RUN chmod 755 /var/www/html/auction
