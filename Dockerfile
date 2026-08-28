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

# 3. Copiar todo el código fuente del proyecto
COPY . /app

# 4. Limpieza absoluta de caches locales heredados
RUN rm -rf /app/vendor /app/node_modules /app/bootstrap/cache/*.php /app/public/build

# 5. Instalar dependencias de producción, compilar Vite y generar autoload limpio
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && (npm ci --no-audit --prefer-offline || npm install) \
    && npm run build \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative --no-scripts

# 6. Configuración de FrankenPHP, Caddy, PHP y Supervisor
COPY docker/frankenphp/entrypoint.sh /entrypoint.sh
COPY docker/frankenphp/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile
COPY docker/frankenphp/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/frankenphp/conf.d/ /etc/supervisor/conf.d/

# 7. Permisos de storage y bootstrap/cache
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 80 443

CMD ["/entrypoint.sh"]
