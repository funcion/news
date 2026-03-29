#!/bin/bash

# Script para iniciar la plataforma de noticias

echo "🚀 Iniciando Noticias Platform..."

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker primero."
    exit 1
fi

# Verificar si Docker Compose está instalado
if ! command -v docker compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permisos
echo "🔧 Configurando permisos..."
chmod -R 775 storage bootstrap/cache

# Iniciar contenedores
echo "🐳 Iniciando contenedores Docker..."
docker compose up -d

# Esperar a que los servicios estén listos
echo "⏳ Esperando a que los servicios estén listos..."
sleep 10

# Verificar estado de los contenedores
echo "📊 Verificando estado de los contenedores..."
docker compose ps

# Instalar dependencias si no existen
if [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    docker compose exec frankenphp composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar clave de aplicación si no existe
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d= -f2)" = "" ]; then
    echo "🔑 Generando clave de aplicación..."
    docker compose exec frankenphp php artisan key:generate
fi

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones de base de datos..."
docker compose exec frankenphp php artisan migrate --force

echo "✅ ¡Noticias Platform está lista!"
echo ""
echo "🌐 Accede a la aplicación en: http://localhost"
echo "📊 Panel de administración: http://localhost/admin"
echo "📨 Mailpit (emails): http://localhost:8025"
echo ""
echo "📝 Comandos útiles:"
echo "   docker compose logs -f      # Ver logs en tiempo real"
echo "   docker compose down         # Detener todos los servicios"
echo "   docker compose exec frankenphp php artisan [comando]  # Ejecutar comandos Artisan"