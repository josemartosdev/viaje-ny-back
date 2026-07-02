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

FROM php:8.3-cli-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo_mysql intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN if [ ! -f .env ]; then \
    printf "APP_ENV=prod\nAPP_DEBUG=0\nAPP_SECRET=change-me-in-railway\nDATABASE_URL=sqlite:///%%kernel.project_dir%%/var/data.db\n" > .env; \
    fi \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

ENV APP_ENV=prod
ENV APP_DEBUG=0

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

EXPOSE 8080
CMD ["start"]

