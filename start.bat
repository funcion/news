@echo off
echo 🚀 Iniciando Noticias Platform...

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker no está instalado. Por favor instala Docker primero.
    exit /b 1
)

REM Verificar si Docker Compose está instalado
docker compose version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Compose no está instalado. Por favor instala Docker Compose primero.
    exit /b 1
)

REM Crear directorios necesarios
echo 📁 Creando directorios necesarios...
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\testing" mkdir "storage\framework\testing"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Iniciar contenedores
echo 🐳 Iniciando contenedores Docker...
docker compose up -d

REM Esperar a que los servicios estén listos
echo ⏳ Esperando a que los servicios estén listos...
timeout /t 10 /nobreak >nul

REM Verificar estado de los contenedores
echo 📊 Verificando estado de los contenedores...
docker compose ps

REM Instalar dependencias si no existen
if not exist "vendor\autoload.php" (
    echo 📦 Instalando dependencias de Composer...
    docker compose exec frankenphp composer install --no-interaction --prefer-dist --optimize-autoloader
)

REM Generar clave de aplicación si no existe
findstr /B "APP_KEY=" .env | findstr /V "APP_KEY=base64:" >nul
if errorlevel 0 (
    echo 🔑 Generando clave de aplicación...
    docker compose exec frankenphp php artisan key:generate
)

REM Ejecutar migraciones
echo 🗄️ Ejecutando migraciones de base de datos...
docker compose exec frankenphp php artisan migrate --force

echo ✅ ¡Noticias Platform está lista!
echo.
echo 🌐 Accede a la aplicación en: http://localhost
echo 📊 Panel de administración: http://localhost/admin
echo 📨 Mailpit (emails): http://localhost:8025
echo.
echo 📝 Comandos útiles:
echo    docker compose logs -f      # Ver logs en tiempo real
echo    docker compose down         # Detener todos los servicios
echo    docker compose exec frankenphp php artisan [comando]  # Ejecutar comandos Artisan