# ===================================================
# Production Dockerfile for Coolify (Laravel 13 + FrankenPHP)
# ===================================================
FROM dunglas/frankenphp:php8.3-bookworm

# 1. Copiar Composer binario
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 2. Instalar Node.js 22.x LTS + supervisor + extensiones PHP requeridas
RUN apt-get update && apt-get install -y curl gnupg supervisor git unzip libpq-dev libpng-dev libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && install-php-extensions \
        pdo_pgsql \
        pgsql \
        redis \
        gd \
        zip \
        exif \
        pcntl \
        bcmath \
        intl \
        opcache \
        sockets \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/log/supervisor /var/run/supervisor /etc/supervisor/conf.d

WORKDIR /app

# 3. Copiar archivos de dependencias primero para aprovechar cache de capas Docker
COPY composer.json composer.lock package.json package-lock.json* /app/

# 4. Instalar dependencias de backend y frontend
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader \
    && npm ci --no-audit --prefer-offline || npm install

# 5. Copiar todo el código fuente del proyecto
COPY . /app

# 6. Compilar assets de frontend (Vite) y optimizar autoload de Composer
RUN npm run build \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative

# 7. Configuración de FrankenPHP, Caddy, PHP y Supervisor
COPY docker/frankenphp/entrypoint.sh /entrypoint.sh
COPY docker/frankenphp/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile
COPY docker/frankenphp/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/frankenphp/conf.d/ /etc/supervisor/conf.d/

# 8. Permisos de storage y bootstrap/cache
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 80 443

CMD ["/entrypoint.sh"]