# Guía Definitiva de Despliegue en Coolify v4 (Glodaxia News)

> **Última actualización**: 28 de agosto de 2026 — Deploy exitoso verificado ✅
>
> Guía maestra, probada en producción, con todas las lecciones aprendidas y buenas prácticas
> para desplegar sin errores el stack completo de **Glodaxia News** en **Coolify v4**.

---

## 📋 Stack Tecnológico

| Componente | Tecnología | Versión |
|---|---|---|
| Framework | Laravel | 13.x |
| Servidor Web | FrankenPHP (Caddy) | latest |
| PHP | PHP | 8.3 |
| Base de datos | PostgreSQL + pgvector | 17 |
| Cache / Colas / Sesiones | Redis | 7.2 |
| Workers | Laravel Horizon + Scheduler | via Supervisor |
| Frontend Build | Vite | latest |
| Storage de medios | Cloudflare R2 (S3-compatible) | — |
| WebSockets | Ably | — |
| IA (Texto) | OpenRouter (DeepSeek, Qwen) | — |
| IA (Imágenes) | SiliconFlow (FLUX.1-schnell) | — |
| SEO | IndexNow (Bing/Yandex) + Jina AI | — |
| Deploy / Hosting | Coolify v4 | auto-deploy via GitHub |

---

## 🏗️ Arquitectura del Deploy

```
┌─────────────────────────────────────────────────────┐
│                    COOLIFY v4 (VPS)                  │
│                                                      │
│  ┌──────────────────────────────────────────────┐   │
│  │         Traefik (Reverse Proxy)               │   │
│  │         HTTPS automático + Let's Encrypt      │   │
│  └──────────────┬───────────────────────────────┘   │
│                  │ Puerto 80                         │
│  ┌──────────────▼───────────────────────────────┐   │
│  │     Contenedor App (supervisord PID 1)        │   │
│  │                                                │   │
│  │  ┌─────────────────────────────────────────┐  │   │
│  │  │ FrankenPHP (Caddy) — Servidor Web       │  │   │
│  │  │ Puerto 80, compresión gzip/zstd         │  │   │
│  │  │ Headers de seguridad, cache estáticos   │  │   │
│  │  └─────────────────────────────────────────┘  │   │
│  │  ┌─────────────────────────────────────────┐  │   │
│  │  │ Horizon — Procesador de Colas (Redis)   │  │   │
│  │  │ Jobs de IA, imágenes, scraping          │  │   │
│  │  └─────────────────────────────────────────┘  │   │
│  │  ┌─────────────────────────────────────────┐  │   │
│  │  │ Scheduler — Cron cada 60 segundos       │  │   │
│  │  │ Tareas programadas de Laravel           │  │   │
│  │  └─────────────────────────────────────────┘  │   │
│  └──────────────────────────────────────────────┘   │
│           │                        │                 │
│           ▼                        ▼                 │
│  ┌────────────────┐    ┌──────────────────┐         │
│  │ PostgreSQL 17   │    │ Redis 7.2         │         │
│  │ + pgvector      │    │ Cache + Colas     │         │
│  │ (contenedor     │    │ + Sesiones        │         │
│  │  independiente) │    │ (contenedor       │         │
│  └────────────────┘    │  independiente)   │         │
│                         └──────────────────┘         │
│                                                      │
│  📁 Volumen Persistente: /app/storage/app            │
│     (Imágenes sobreviven redeploys)                  │
└─────────────────────────────────────────────────────┘
```

**Principio clave**: Un solo contenedor de app con `supervisord` manejando los 3 procesos (FrankenPHP + Horizon + Scheduler). PostgreSQL y Redis son recursos independientes en Coolify. Esto evita duplicación de servicios y simplifica el deploy.

---

## ✅ PASO 1: Crear Base de Datos PostgreSQL 17 + PGVector en Coolify

1. En Coolify → tu proyecto → **+ New Resource** → **Database** → **PostgreSQL**
2. Configurar:
   - **Image**: `pgvector/pgvector:pg17` ⚠️ (NO la imagen default de postgres, necesitamos pgvector)
   - **Username**: `postgres`
   - **Password**: (se autogenera, copiar)
   - **Initial Database**: `postgres`
3. En **Ports Mappings**, agregar un puerto externo si necesitas acceso desde tu PC:
   - Ejemplo: `3000:5432` (accesible via `IP_VPS:3000`)
4. Hacer clic en **Save** y luego **Start**

### Datos del recurso activo:

| Campo | Valor |
|---|---|
| Recurso | `postgresql-database-dkkc8004sks44w8s8kk4soso` |
| Host Interno (`DB_HOST`) | `dkkc8004sks44w8s8kk4soso` |
| Puerto (`DB_PORT`) | `5432` |
| Base de datos (`DB_DATABASE`) | `postgres` |
| Usuario (`DB_USERNAME`) | `postgres` |
| Password (`DB_PASSWORD`) | `EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM` |
| Puerto Externo | `3000` |

> **⚠️ IMPORTANTE**: El `DB_HOST` debe ser el **Internal Hostname** del recurso (el UUID del contenedor), NUNCA `localhost` ni `127.0.0.1`. Los contenedores se comunican por la red interna de Docker.

---

## ✅ PASO 2: Crear Redis 7.2 en Coolify

1. En Coolify → tu proyecto → **+ New Resource** → **Database** → **Redis**
2. Configurar:
   - **Image**: `redis:7.2`
   - **Password**: (se autogenera, copiar)
3. Hacer clic en **Save** y luego **Start**

### Datos del recurso activo:

| Campo | Valor |
|---|---|
| Recurso | `redis-database-ngso04k040g08ocggg4wsws4` |
| Host Interno (`REDIS_HOST`) | `ngso04k040g08ocggg4wsws4` |
| Puerto (`REDIS_PORT`) | `6379` |
| Password (`REDIS_PASSWORD`) | `uXvERZI3cTnYIHZeM6jlEug0ZIGxZv72Bx7ThrV6HNJG91q9Ix55LIVWzptNIXnu` |

---

## 📦 PASO 3: Exportar Base de Datos Local e Importar en Coolify

### 3.1 Exportar desde tu PC Local:
```bash
cd /home/luisf/news
docker compose exec -T postgres pg_dump -U noticias noticias > backup_glodaxia.sql
```

### 3.2 Importar a Coolify:

**Opción A — Consola `psql` (recomendada)**:
```bash
PGPASSWORD="EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM" \
  psql -h IP_DE_TU_VPS -p 3000 -U postgres -d postgres < backup_glodaxia.sql
```

**Opción B — Cliente GUI (DBeaver / TablePlus / pgAdmin)**:
- Host: `IP_DE_TU_VPS`
- Port: `3000`
- Database: `postgres`
- User: `postgres`
- Password: `EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM`
- Ejecutar el script `backup_glodaxia.sql`

---

## 🚀 PASO 4: Crear la App Laravel en Coolify

1. En Coolify → tu proyecto → **+ New Resource** → **Public / Private Git Repository**
2. Seleccionar cuenta de GitHub y repositorio `funcion/news` (rama `master`)
3. **Build Pack**: **Docker Compose**
4. Configurar:
   - **Docker Compose Location**: `/docker-compose.prod.yml`
   - **Domains**: `https://glodaxia.com`

### ¿Por qué Docker Compose y no Dockerfile?

Usamos Docker Compose como Build Pack porque permite declarar volúmenes persistentes y configuraciones de red directamente en el archivo. Coolify lee el `docker-compose.prod.yml` y lo ejecuta. Nuestro compose tiene **un solo servicio** (`app`) — NO hay servicio `horizon` separado porque supervisord dentro del contenedor ya maneja todo.

---

## 💾 PASO 5: Configurar el Volumen Persistente

En la configuración de la app en Coolify:
1. Pestaña **Storages** → **+ Add Storage**:
   - **Volume Name**: `glodaxia_storage`
   - **Mount Path**: `/app/storage/app`
2. **Save**

> Esto garantiza que las imágenes generadas por IA y los uploads sobrevivan a cada redeploy.

---

## 🔑 PASO 6: Variables de Entorno (Copiar y Pegar Completo)

En la pestaña **Environment Variables** de la app en Coolify, pega este bloque:

```dotenv
APP_NAME="Glodaxia News"
APP_ENV=production
APP_KEY=base64:qyTzi6gpy0/bx/cAXudSRbrN8d4I5zvtNacLmgcid88=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://glodaxia.com
ASSET_URL=https://glodaxia.com

LOG_CHANNEL=daily
LOG_LEVEL=error

# --- CONEXIÓN POSTGRESQL COOLIFY ---
DB_CONNECTION=pgsql
DB_HOST=dkkc8004sks44w8s8kk4soso
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM

# --- CONEXIÓN REDIS COOLIFY ---
REDIS_HOST=ngso04k040g08ocggg4wsws4
REDIS_PASSWORD=uXvERZI3cTnYIHZeM6jlEug0ZIGxZv72Bx7ThrV6HNJG91q9Ix55LIVWzptNIXnu
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_LIFETIME=120
HORIZON_PREFIX=glodaxia_horizon:

# --- WEBSOCKETS (ABLY) ---
BROADCAST_CONNECTION=ably
BROADCAST_DRIVER=ably
ABLY_KEY=ljcq8Q.lmSrNw:RMkrNf6VTUXrJQxjvke7gyrqjIV-5Z6Ov2uNyRON7wI

# --- STORAGE R2 CLOUDFLARE ---
FILESYSTEM_DISK=local
MEDIA_DISK=r2
R2_ACCESS_KEY_ID=e01f7d19440820846d99dd24cf00c690
R2_SECRET_ACCESS_KEY=734cf6c89bce7502cc3f57279803160fcea5f73351485ee8d2cf6f668950bb9e
R2_BUCKET=glodaxia-media
R2_ENDPOINT=https://2f804b6957d992282275865a8626b949.r2.cloudflarestorage.com
R2_PUBLIC_URL=https://media.glodaxia.com
CLOUDFLARE_ZONE_ID=a143de8f9ff38c189f40c3456237f771
CLOUDFLARE_API_TOKEN=cfut_vnMkI1pY1qCg3GlpTi9x1pkKDgRNP5fKoTs4doKJ7ab4c423

# --- INTELIGENCIA ARTIFICIAL ---
OPENROUTER_API_KEY=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
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
SILICONFLOW_API_KEY=sk-rjxtzmhoryiwftvlvjjvwmlzyvtqmvpekxkhfjjzvchcdzaw
SILICONFLOW_IMAGE_MODEL=black-forest-labs/FLUX.1-schnell
JINA_API_KEY=jina_847316c404d144e88d30a70fee3de8fb_r6yZ2h3PEFYoM0m6JbSqubL5fLc
AI_MODELS_POOL=deepseek/deepseek-v4-flash-0731,qwen/qwen3.7-flash,deepseek/deepseek-chat
AI_MAX_TOKENS=10000
AI_CLASSIFICATION_MAX_TOKENS=1500

# --- INDEXNOW ---
INDEXNOW_KEY=e9a4f781c03d42bfa168925bc01e4a77

# --- SANCTUM ---
SANCTUM_STATEFUL_DOMAINS=glodaxia.com
```

> **⚠️ IMPORTANTE**: Coolify mostrará una advertencia sobre `APP_ENV=production` en build-time. Es inofensiva — nuestro Dockerfile no usa ARGs para estas variables. Las variables se inyectan en runtime al contenedor.

---

## 🚀 PASO 7: Desplegar

1. Haz clic en **Deploy** en Coolify
2. Coolify:
   - Clona el repo desde GitHub
   - Construye la imagen Docker (instala PHP deps, compila Vite, etc.)
   - Levanta el contenedor
   - Traefik configura HTTPS automáticamente con Let's Encrypt
3. El `entrypoint.sh` ejecuta al iniciar:
   - Espera conexión a PostgreSQL (hasta 30 intentos)
   - Ejecuta `php artisan migrate --force`
   - Cachea config, rutas, vistas y eventos para máximo rendimiento
   - Inicia supervisord (FrankenPHP + Horizon + Scheduler)

---

## ✅ PASO 8: Verificación Post-Deploy

### En los logs de Coolify, debes ver:
```
=== Glodaxia News - Production Entrypoint ===
[OK] PostgreSQL conectado exitosamente.
Ejecutando migraciones...
Cacheando configuracion de produccion...
[OK] Laravel configurado para produccion.
=== Iniciando Supervisor (FrankenPHP + Horizon + Scheduler) ===
```

### Verificar en el navegador:
- `https://glodaxia.com` — La web carga correctamente
- `https://glodaxia.com/admin` — Panel de administración accesible

### Verificar Horizon (colas de IA):
- En el panel admin → Horizon dashboard debe mostrar workers activos

---

## 🔄 Ciclo de Desarrollo: ¿Qué pasa en cada `git push`?

```
git push origin master
       │
       ▼
GitHub Webhook → Coolify detecta commit
       │
       ▼
Coolify reconstruye imagen Docker (~3-5 min)
       │
       ▼
Contenedor nuevo reemplaza al anterior
       │
       ▼
entrypoint.sh: migrate + cache + supervisord
       │
       ▼
✅ App actualizada en producción
```

**Lo que SE mantiene entre deploys:**
- ✅ Base de datos PostgreSQL (contenedor independiente)
- ✅ Redis (contenedor independiente)
- ✅ Imágenes en `/app/storage/app` (volumen persistente)
- ✅ Dominio y certificado HTTPS

**Lo que SE reconstruye en cada deploy:**
- 🔄 Código PHP
- 🔄 Assets Vite (CSS/JS compilados)
- 🔄 Dependencias Composer
- 🔄 Caches de Laravel (config, rutas, vistas)

---

## 📁 Archivos de Infraestructura Docker (Referencia)

### Estructura de archivos:
```
news/
├── Dockerfile                              ← Imagen de producción
├── docker-compose.prod.yml                 ← Para Coolify (Build Pack: Docker Compose)
├── .dockerignore                           ← Excluye vendor, node_modules, caches
└── docker/
    └── frankenphp/
        ├── entrypoint.sh                   ← Script de inicio (migrate + cache + supervisord)
        ├── php.ini                         ← Configuración PHP de producción
        ├── Caddyfile                       ← Configuración de FrankenPHP/Caddy
        ├── supervisord.conf                ← Configuración principal de Supervisor
        └── conf.d/
            ├── frankenphp.conf             ← Proceso: servidor web
            ├── horizon.conf                ← Proceso: colas de trabajo
            └── scheduler.conf              ← Proceso: cron de Laravel
```

### Buenas prácticas aplicadas en estos archivos:

| Archivo | Buena Práctica |
|---|---|
| `Dockerfile` | `mkdir -p` de todos los directorios ANTES de `chown` (porque `.gitignore` excluye `storage/`) |
| `Dockerfile` | `rm -rf node_modules` después de `npm run build` (ahorra ~200MB en la imagen) |
| `Dockerfile` | `composer install --no-scripts` + `composer dump-autoload --no-scripts` (evita error de SailServiceProvider) |
| `Dockerfile` | `rm -rf bootstrap/cache/*.php` antes de composer (limpia caches de desarrollo) |
| `.dockerignore` | Excluye `/vendor`, `/node_modules`, `/bootstrap/cache/*.php`, `/public/build`, `/storage/app/*` |
| `docker-compose.prod.yml` | Un solo servicio `app` (NO servicio `horizon` separado — supervisord maneja todo) |
| `docker-compose.prod.yml` | Volúmenes para `storage/app` y `storage/logs` |
| `php.ini` | `display_errors = Off` y `log_errors = On` (NUNCA exponer errores en producción) |
| `php.ini` | Sin `session.save_path` hardcodeado (Laravel maneja sesiones via su driver Redis) |
| `php.ini` | OPcache con JIT habilitado (`jit=1255`) para máximo rendimiento |
| `Caddyfile` | Compresión `gzip` + `zstd` |
| `Caddyfile` | Headers de seguridad: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` |
| `Caddyfile` | Cache inmutable para assets de Vite (`/build/*`) |
| `entrypoint.sh` | Health check de PostgreSQL (30 intentos × 2 seg) antes de migrate |
| `entrypoint.sh` | Cacheo de producción: `config:cache`, `route:cache`, `view:cache`, `event:cache` |
| `entrypoint.sh` | `exec supervisord` como PID 1 (señales de Docker llegan correctamente) |

---

## 🧪 GOTCHAS Y LECCIONES APRENDIDAS

### 1. 💥 `chown: /app/storage: No such file or directory` en Docker build
- **Causa**: `.gitignore` tiene `storage/` → Docker no copia ese directorio → `chown` falla
- **Solución**: Agregar `mkdir -p /app/storage/framework/{cache,sessions,testing,views} /app/storage/logs /app/storage/app/public /app/bootstrap/cache` en el Dockerfile ANTES de `chown`
- **Regla**: Siempre crear directorios explícitamente en el Dockerfile, nunca asumir que existen

### 2. 💥 `Class "Laravel\Sail\SailServiceProvider" not found` en `composer dump-autoload`
- **Causa**: `bootstrap/cache/packages.php` del desarrollo local tiene referencia a Sail. Al construir con `--no-dev`, Sail no está en vendor
- **Solución**:
  1. `.dockerignore`: excluir `/bootstrap/cache/*.php`
  2. Dockerfile: `rm -rf /app/bootstrap/cache/*.php` antes de `composer install`
  3. Usar `--no-scripts` en `composer install` y `composer dump-autoload`
- **Regla**: Nunca confiar en archivos de cache de desarrollo en producción

### 3. 💥 Servicio `horizon` duplicado en docker-compose = Horizon corriendo 2 veces
- **Causa**: Servicio `horizon` separado con `supervisord` + servicio `app` con `entrypoint.sh` que también ejecuta supervisord → Horizon corriendo en ambos contenedores
- **Solución**: Un solo servicio `app` con supervisord manejando FrankenPHP + Horizon + Scheduler
- **Regla**: Un contenedor, un supervisord, múltiples procesos

### 4. 💥 YAML roto en docker-compose (líneas pegadas sin salto de línea)
- **Causa**: Líneas de environment pegadas: `- APP_ENV=      - APP_DEBUG=`
- **Solución**: Cada variable en su propia línea con espacio después del guión
- **Regla**: Validar YAML antes de hacer push (`docker compose config`)

### 5. 🔒 `php.ini` con `display_errors = On` en producción
- **Causa**: Copiar php.ini de desarrollo sin cambiar valores
- **Solución**: `display_errors = Off`, `log_errors = On`, `error_log = /app/storage/logs/php-errors.log`
- **Regla**: SIEMPRE tener un php.ini separado para producción

### 6. 🔒 `session.save_path` hardcodeado al host de desarrollo
- **Causa**: `session.save_path = "tcp://redis:6379"` apunta al contenedor `redis` del compose local
- **Solución**: No configurar `session.save_path` en php.ini — Laravel maneja sesiones via su driver Redis internamente
- **Regla**: No duplicar configuración que Laravel ya maneja

### 7. 🌐 `DB_HOST=localhost` o `127.0.0.1` en Coolify
- **Causa**: Los contenedores en Coolify se comunican por red interna Docker, no por localhost
- **Solución**: Usar siempre el **Internal Hostname** del recurso (ej: `dkkc8004sks44w8s8kk4soso`)
- **Regla**: En Coolify, `DB_HOST` y `REDIS_HOST` son siempre el UUID/nombre del contenedor

### 8. 📁 Imágenes de IA perdidas en cada `git push`
- **Causa**: Sin volumen persistente, cada redeploy destruye `/app/storage/app`
- **Solución**: Volumen persistente `glodaxia_storage` montado en `/app/storage/app`
- **Regla**: TODO lo que deba sobrevivir redeploys necesita un volumen

### 9. ⚡ Entrypoint no cachea para producción = rendimiento pobre
- **Causa**: Solo ejecutar `config:clear` sin hacer `config:cache`, `route:cache`, etc.
- **Solución**: Limpiar caches viejos → migrate → cachear todo para producción
- **Regla**: En producción SIEMPRE cachear config, rutas, vistas y eventos

### 10. 📊 Imagen Docker de ~1.5GB por no limpiar node_modules
- **Causa**: `node_modules` (~200MB+) queda en la imagen final después de compilar Vite
- **Solución**: `RUN rm -rf /app/node_modules` después de `npm run build`
- **Regla**: Limpiar todo lo que no se necesita en runtime

---

## 🔧 Comandos Útiles

### Reconstruir y probar localmente antes de push:
```bash
cd /home/luisf/news
docker build -t glodaxia-test-build .
```

### Ver logs de la app en Coolify:
- Pestaña **Logs** del recurso en Coolify

### Conectarse a la base de datos de producción:
```bash
PGPASSWORD="EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM" \
  psql -h IP_DE_TU_VPS -p 3000 -U postgres -d postgres
```

### Backup de la base de datos de producción:
```bash
PGPASSWORD="EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM" \
  pg_dump -h IP_DE_TU_VPS -p 3000 -U postgres postgres > backup_prod_$(date +%Y%m%d).sql
```

### Flujo de deploy rápido:
```bash
cd /home/luisf/news
git add -A
git commit -m "feat: descripción del cambio"
git push origin master
# Coolify despliega automáticamente
```
