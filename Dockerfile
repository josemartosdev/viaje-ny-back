FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

FROM php:8.3-cli-alpine
RUN apk add --no-cache icu-libs icu-dev \
    && docker-php-ext-install pdo_mysql intl opcache \
    && apk del icu-dev \
    && rm -rf /tmp/* /var/cache/apk/*

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY docker/start.sh /usr/local/bin/start

RUN mkdir -p var/cache var/log \
    && chmod +x /usr/local/bin/start

ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 8080
CMD ["start"]
