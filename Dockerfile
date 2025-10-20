FROM php:8.2-cli-alpine
RUN apk add --no-cache sqlite-dev \
 && docker-php-ext-configure pdo_sqlite \
 && docker-php-ext-install pdo_sqlite
WORKDIR /var/www
EXPOSE 8080
CMD ["php","-S","0.0.0.0:8080","-t","public"]

