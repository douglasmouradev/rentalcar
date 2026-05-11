FROM php:8.3-apache-bookworm

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers

COPY docker/apache-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/public/assets/uploads /var/www/html/storage/logs 2>/dev/null || true

EXPOSE 80
