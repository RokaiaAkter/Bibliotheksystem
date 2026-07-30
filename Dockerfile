FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

RUN mkdir -p storage/logs storage/backups \
    && chown -R www-data:www-data storage \
    && chmod -R 750 storage

EXPOSE 80

