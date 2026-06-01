#!/bin/bash
set -e

# Asegurar symlink correcto para storage (Sirve Media Library)
if [ ! -L /app/public/storage ]; then
    ln -sfn ../storage/app/public /app/public/storage
    echo "Created storage symlink"
else
    # Verificar que no sea un symlink roto
    if [ ! -d /app/public/storage/ ]; then
        rm -f /app/public/storage
        ln -sfn ../storage/app/public /app/public/storage
        echo "Repaired storage symlink"
    fi
fi

# Configurar permisos
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Limpiar caché de Laravel
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Ejecutar migraciones si es necesario
php artisan migrate --force || true

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Iniciar FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
