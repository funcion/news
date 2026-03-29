# Usar la imagen oficial más reciente de FrankenPHP con PHP 8.3
FROM dunglas/frankenphp:php8.3-bookworm

# Instalar extensiones PHP necesarias para Laravel 12
RUN install-php-extensions \
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
    @composer

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . /app

# Crear directorios necesarios y establecer permisos
RUN mkdir -p /app/storage/logs \
    && mkdir -p /app/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copiar script de entrada
COPY docker/frankenphp/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Exponer puertos
EXPOSE 80 443

# Comando por defecto - FrankenPHP se ejecuta automáticamente
CMD ["/entrypoint.sh"]