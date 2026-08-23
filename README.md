# 🗞️ Noticias Platform - Plataforma de Noticias Automatizada con IA

Plataforma de noticias automatizada con IA y RSS construida con Laravel 12, FrankenPHP, PostgreSQL (pgvector), Redis, Reverb, Horizon y Filament v3.

## 🚀 Características Principales

- **Motor de Ingesta RSS**: Sistema automatizado para consultar fuentes RSS y procesar noticias.
- **Cerebro de IA**: Pipeline de procesamiento y enriquecimiento de artículos con modelos de IA (OpenRouter).
- **Generación de Imágenes**: Creación automática de imágenes de portada contextuales.
- **Sistema Anti-Duplicados**: Detección de contenido similar con pgvector y algoritmos de proximidad.
- **Frontend en Tiempo Real**: Actualizaciones instantáneas vía WebSockets con Laravel Reverb.
- **SEO Técnico Avanzado**: Schema Markup, OpenGraph, Twitter Cards y Sitemaps dinámicos optimizados para Google News.
- **Sistema de Tags Inteligente**: Extracción y asignación automática de etiquetas temáticas.
- **Panel de Administración**: Panel completo y moderno basado en Filament v3.

---

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 12 (PHP 8.3)
- **Servidor Web**: FrankenPHP (Caddy Server + Worker Mode + HTTP/3)
- **Base de Datos**: PostgreSQL 17 + extensión `pgvector`
- **Cache y Colas**: Redis 7
- **Panel Administrativo**: Filament v3
- **WebSockets**: Laravel Reverb
- **Gestión de Colas**: Laravel Horizon
- **Frontend**: Blade + Alpine.js + Tailwind CSS v4 (Vite)
- **Email Testing**: Mailpit

---

## 📦 Guía de Instalación Paso a Paso

Sigue estos pasos en orden para levantar la plataforma desde un clon limpio del repositorio.

### 1. Requisitos Previos

- **Docker** (v24+) y **Docker Compose** (v2.20+)
- **Git**
- **WSL2** (en Windows) o terminal **Linux / macOS**

### 2. Clonar el Repositorio

```bash
git clone <repository-url>
cd news
```

### 3. Configurar Variables de Entorno

Copia el archivo de ejemplo para generar tu archivo `.env`:

```bash
cp .env.example .env
```

> [!TIP]
> Verifica que las credenciales de base de datos en tu `.env` coincidan con las del contenedor (`DB_HOST=postgres`, `DB_PORT=5432`, `DB_DATABASE=noticias`, `DB_USERNAME=noticias`, `DB_PASSWORD=noticias123`, `REDIS_HOST=redis`).

### 4. Construir e Iniciar los Contenedores

Levanta los 6 servicios en segundo plano con reconstrucción de imágenes:

```bash
docker compose up -d --build
```

### 5. Instalar Dependencias de Backend (PHP / Composer)

Una vez que los contenedores estén corriendo, instala las dependencias de Laravel:

```bash
docker compose exec app composer install
```

### 6. Generar la Clave de Aplicación (APP_KEY)

Genera la clave de encriptación de Laravel:

```bash
docker compose exec app php artisan key:generate
```

### 7. Compilar Assets del Frontend (Vite)

Instala las dependencias de Node.js y compila los assets para que el manifest de Vite esté disponible:

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

> [!NOTE]
> Durante el desarrollo activo del frontend también puedes ejecutar `docker compose exec app npm run dev` para hot-reloading de estilos y scripts.

### 8. Ejecutar Migraciones y Seeders de Base de Datos

Inicializa la estructura de tablas y los datos esenciales (categorías, fuentes iniciales y usuario admin):

```bash
docker compose exec app php artisan migrate --seed
```

### 9. Reiniciar Servicios de Fondo (Horizon & Reverb)

Para asegurar que los workers de colas y WebSockets tomen las dependencias y configuraciones recién generadas:

```bash
docker compose restart horizon reverb
```

---

## 📊 Servicios y Accesos

| Servicio | URL Local | Puerto | Descripción |
| :--- | :--- | :--- | :--- |
| **FrankenPHP (App)** | [http://localhost:8000](http://localhost:8000) | `8000` | Frontend de noticias |
| **Panel de Administración** | [http://localhost:8000/admin](http://localhost:8000/admin) | `8000` | Panel Filament v3 |
| **Horizon Dashboard** | [http://localhost:8000/horizon](http://localhost:8000/horizon) | `8000` | Monitor de colas y tareas |
| **Reverb (WebSockets)** | `ws://localhost:8080` | `8080` | Servidor WebSockets |
| **Mailpit (Email UI)** | [http://localhost:8025](http://localhost:8025) | `8025` | Bandeja de pruebas de email |
| **PostgreSQL (pgvector)** | `localhost:5432` | `5432` | Base de datos relacional y vectorial |
| **Redis** | `localhost:6379` | `6379` | Cache de alta velocidad y colas |

---

## 🔍 Detalles Técnicos: ¿Por qué no funcionaba y cómo se solucionó?

Si te encontraste con el error `Container is restarting, wait until the container is running`, aquí se explican las causas técnicas raíz y las soluciones aplicadas en la configuración:

### 1. Ausencia del directorio `storage/` en un clon limpio
- **Problema**: El script de entrada [entrypoint.sh](file:///docker/frankenphp/entrypoint.sh) ejecutaba `chown -R www-data:www-data /app/storage` con `set -e` habilitado. Como Git no guarda carpetas vacías sin `.gitkeep`, `/app/storage` no existía, provocando que el comando fallara con código de error y el contenedor se apagara inmediatamente en un bucle continuo de reinicios (*crash loop*).
- **Solución**: Se actualizó `entrypoint.sh` para crear automáticamente mediante `mkdir -p` toda la estructura necesaria (`storage/framework/{cache,sessions,testing,views}`, `storage/logs`, `storage/app/public` y `bootstrap/cache`) antes de asignar permisos.

### 2. Comandos Artisan ejecutados antes del `composer install`
- **Problema**: `entrypoint.sh` intentaba ejecutar `php artisan key:generate` o limpiezas de caché durante el arranque del contenedor. Sin embargo, en un clon nuevo `vendor/autoload.php` todavía no existe, lo que provocaba un error fatal de PHP (`Failed opening required '/app/vendor/autoload.php'`).
- **Solución**: Se condicionaron los comandos Artisan dentro de `entrypoint.sh` para que únicamente se ejecuten si el archivo `/app/vendor/autoload.php` ya está presente.

### 3. Composer no estaba instalado dentro de la imagen
- **Problema**: La imagen base `dunglas/frankenphp:php8.3-bookworm` no incluye el binario de Composer por defecto. Al ejecutar `docker compose exec app composer install`, el comando fallaba con `composer: not found`.
- **Solución**: Se añadió `COPY --from=composer:2 /usr/bin/composer /usr/bin/composer` junto con `git` y `unzip` dentro de [docker/frankenphp/Dockerfile](file:///docker/frankenphp/Dockerfile).

### 4. Error 500 por falta del manifiesto de Vite
- **Problema**: Al abrir la página principal por primera vez, Laravel lanzaba una excepción `Vite manifest not found at: /app/public/build/manifest.json`.
- **Solución**: Se documentó e integró el paso obligatorio de compilación `docker compose exec app npm install && docker compose exec app npm run build`.

### 5. Healthchecks erróneos en Horizon y Reverb
- **Problema**: Como los contenedores `horizon` y `reverb` usan la misma imagen base de FrankenPHP pero corren como workers CLI (vía `supervisord` y `artisan reverb:start`), el healthcheck HTTP heredado del servidor web en el puerto 80 marcaba erróneamente estos servicios como `unhealthy`.
- **Solución**: Se añadió `healthcheck: disable: true` para `horizon` y `reverb` en [docker-compose.yml](file:///docker-compose.yml).

---

## 🔧 Comandos Frecuentes y Mantenimiento

### Ver Logs en Tiempo Real

```bash
# Ver logs de todos los servicios
docker compose logs -f

# Ver logs solo del servidor web
docker compose logs -f app

# Ver logs de tareas en segundo plano
docker compose logs -f horizon

# Ver logs de WebSockets
docker compose logs -f reverb
```

### Limpieza y Regeneración de Caché

```bash
docker compose exec app php artisan optimize:clear
```

### Reset Completo de Base de Datos y Volúmenes

Si necesitas limpiar la base de datos y recrear todo el entorno desde cero:

```bash
# 1. Detener y eliminar volúmenes persistentes
docker compose down -v

# 2. Volver a levantar contenedores
docker compose up -d

# 3. Re-ejecutar migraciones y seeders
docker compose exec app php artisan migrate --seed

# 4. Reiniciar workers
docker compose restart horizon reverb
```

### Ingesta Manual de Noticias RSS

Para forzar una sincronización manual del motor RSS sin esperar al scheduler de Horizon:

```bash
docker compose exec app php artisan rss:fetch
```

---

## 🏗️ Estructura del Proyecto

```
news/
├── app/                    # Lógica de la aplicación Laravel
│   ├── Console/           # Comandos Artisan (ej. Ingesta RSS)
│   ├── Http/              # Controladores, Middleware y Requests
│   ├── Models/            # Modelos Eloquent
│   ├── Providers/         # Service Providers
│   └── Services/          # Servicios (IA, embeddings, RSS, imágenes)
├── config/                # Archivos de configuración
├── database/              # Migraciones, factories y seeders
├── docker/                # Configuración de contenedores
│   ├── frankenphp/        # Dockerfile, entrypoint, Caddyfile y supervisor
│   └── postgres/          # Scripts de inicialización y extensiones
├── public/                # Assets públicos y build de Vite
├── resources/             # Vistas Blade, CSS y JavaScript
├── routes/                # Rutas web, API, consola y canales
├── storage/               # Archivos generados, logs y cache
└── tests/                 # Pruebas automatizadas Pest / PHPUnit
```

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**.