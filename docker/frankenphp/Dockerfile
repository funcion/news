# ===================================================
# FrankenPHP + Laravel 12
# Nota: docker-compose monta ./:/app:cached, as� que
# el c�digo se monta desde el host. Este Dockerfile
# instala el entorno de ejecuci�n y Composer.
# ===================================================
FROM dunglas/frankenphp:php8.3-bookworm

# Copiar Composer binario
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js 22.x LTS + supervisor + extensiones PHP
RUN apt-get update && apt-get install -y curl gnupg supervisor git unzip \
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

COPY docker/frankenphp/entrypoint.sh /entrypoint.sh
COPY docker/frankenphp/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile
COPY docker/frankenphp/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/frankenphp/conf.d/ /etc/supervisor/conf.d/

RUN chmod +x /entrypoint.sh

EXPOSE 80 443

CMD ["/entrypoint.sh"]