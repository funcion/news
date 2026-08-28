#!/bin/bash
set -e

# Crear directorios necesarios si no existen
mkdir -p /app/storage/framework/{cache,sessions,testing,views}
mkdir -p /app/storage/logs
mkdir -p /app/storage/app/public
mkdir -p /app/bootstrap/cache

# Asegurar symlink correcto para storage (Sirve Media Library)
if [ ! -L /app/public/storage ]; then
    ln -sfn ../storage/app/public /app/public/storage
    echo "Created storage symlink"
else
    if [ ! -d /app/public/storage/ ]; then
        rm -f /app/public/storage
        ln -sfn ../storage/app/public /app/public/storage
        echo "Repaired storage symlink"
    fi
fi

# Configurar permisos
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Ejecutar comandos de Laravel
if [ -f /app/vendor/autoload.php ]; then
    if [ -n "$DB_HOST" ]; then
        echo "Esperando conexión a PostgreSQL en $DB_HOST:$DB_PORT..."
        for i in {1..30}; do
            if php -r "try { new PDO('pgsql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:'5432').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
                echo "PostgreSQL conectado exitosamente."
                break
            fi
            echo "Esperando a PostgreSQL ($i/30)..."
            sleep 1
        done
    fi

    php artisan config:clear || true
    php artisan cache:clear || true
    php artisan view:clear || true
    php artisan route:clear || true
    php artisan migrate --force || true

    if [ -z "$APP_KEY" ]; then
        php artisan key:generate --force || true
    fi
fi

# Iniciar FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
