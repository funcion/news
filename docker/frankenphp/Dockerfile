# ===================================================
# FrankenPHP + Laravel 12
# Nota: docker-compose monta ./:/app:cached, así que
# el código se monta desde el host. Este Dockerfile
# solo instala el entorno de ejecución.
# ===================================================
FROM dunglas/frankenphp:php8.3-bookworm

# Instalar Node.js 22.x LTS + supervisor + extensiones PHP (sin @composer - innecesario) 
RUN apt-get update && apt-get install -y curl gnupg supervisor \
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
COPY docker/frankenphp/conf.d/horizon.conf /etc/supervisor/conf.d/horizon.conf

RUN chmod +x /entrypoint.sh

EXPOSE 80 443

CMD ["/entrypoint.sh"]