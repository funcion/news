#!/bin/bash
set -e

echo "=== Glodaxia News - Production Entrypoint ==="

# Crear directorios necesarios si no existen
mkdir -p /app/storage/framework/{cache,sessions,testing,views}
mkdir -p /app/storage/logs
mkdir -p /app/storage/app/public
mkdir -p /app/bootstrap/cache

# Asegurar symlink correcto para storage (Sirve Media Library)
if [ ! -L /app/public/storage ]; then
    ln -sfn ../storage/app/public /app/public/storage
    echo "[OK] Created storage symlink"
elif [ ! -d /app/public/storage/ ]; then
    rm -f /app/public/storage
    ln -sfn ../storage/app/public /app/public/storage
    echo "[OK] Repaired storage symlink"
fi

# Configurar permisos
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Ejecutar comandos de Laravel
if [ -f /app/vendor/autoload.php ]; then
    if [ -n "$DB_HOST" ]; then
        echo "Esperando conexion a PostgreSQL en $DB_HOST:${DB_PORT:-5432}..."
        for i in $(seq 1 30); do
            if php -r "try { new PDO('pgsql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:'5432').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
                echo "[OK] PostgreSQL conectado exitosamente."
                break
            fi
            echo "  Esperando a PostgreSQL ($i/30)..."
            sleep 2
        done
    fi

    # Limpiar caches viejos antes de recrear
    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan event:clear 2>/dev/null || true

    # Ejecutar migraciones
    echo "Ejecutando migraciones..."
    php artisan migrate --force 2>&1 || true

    # Generar APP_KEY si no existe
    if [ -z "$APP_KEY" ]; then
        php artisan key:generate --force || true
    fi

    # Cachear para produccion (CRITICO para rendimiento)
    echo "Cacheando configuracion de produccion..."
    php artisan config:cache 2>&1 || true
    php artisan route:cache 2>&1 || true
    php artisan view:cache 2>&1 || true
    php artisan event:cache 2>&1 || true

    echo "[OK] Laravel configurado para produccion."
fi

echo "=== Iniciando Supervisor (FrankenPHP + Horizon + Scheduler) ==="

# Iniciar supervisord como PID 1 (maneja FrankenPHP + Horizon + Scheduler)
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
