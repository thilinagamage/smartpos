# ── Stage 1: Node — build Vite assets ────────────────────────────
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js ./
RUN npm run build

# ── Stage 2: PHP runtime ──────────────────────────────────────────
FROM php:8.4-fpm-alpine AS app

# Runtime packages + build deps for compiling PHP extensions
RUN apk add --no-cache \
    nginx \
    postgresql-client \
    postgresql-dev \
    libpng \
    libpng-dev \
    libzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo_pgsql gd zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .
COPY --from=frontend /app/public/build public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]