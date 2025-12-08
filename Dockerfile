FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    git \
    sqlite \
    sqlite-dev \
    unzip \
 && docker-php-ext-configure pdo_sqlite \
 && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-dev --optimize-autoloader \
 && mkdir -p app/log \
 && chmod -R 777 app/log

EXPOSE 8080

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]

