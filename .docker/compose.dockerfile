FROM php:7.3.10-apache

ENV DEBIAN_FRONTEND noninteractive

RUN a2enmod headers
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y --no-install-recommends apt-utils

RUN apt-get update && apt-get -y --fix-missing install nano \
    unzip libaio-dev libxml2-dev libjpeg-dev \
    libpango1.0-dev libgif-dev libpng-dev \
    git telnet zlib1g-dev libicu-dev \
    g++ vim wget gnupg iputils-ping

# Composer install
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install soap \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd && docker-php-ext-install opcache

RUN docker-php-ext-install gettext xml json simplexml pdo_mysql
RUN docker-php-ext-configure intl && docker-php-ext-install intl

#Add apache configs
ADD .docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf
ADD .docker/ports.conf /etc/apache2/ports.conf
ADD .docker/apache2.conf /etc/apache2/apache2.conf
ADD .docker/envvars /etc/apache2/envvars
ADD .docker/php.ini /usr/local/etc/php/php.ini

RUN service apache2 restart

RUN rm -rf /var/www/var/cache
RUN mkdir -p /var/www/var/cache
RUN chown -R www-data:www-data /var/www/var

WORKDIR /var/www
