FROM php:8.4-cli-alpine

RUN apk add --no-cache \
        bash \
        git \
        unzip \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-install pdo_mysql bcmath intl zip mbstring \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cache de deps: só refaz install se composer.json/lock mudarem.
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .

RUN composer dump-autoload --optimize \
    && chmod +x docker/entrypoint.sh \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["./docker/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
