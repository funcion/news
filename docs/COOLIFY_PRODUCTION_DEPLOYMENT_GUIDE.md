# 🚀 Guía Maestra de Despliegue en Producción con Coolify (Contabo VPS)

Esta guía detalla paso a paso las mejores prácticas de la industria para desplegar la **Plataforma de Noticias Automatizada con IA** en un VPS nuevo de **Contabo** utilizando **Coolify v4** como PaaS auto-hospedado, configurando una arquitectura de **microservicios desacoplados** y **CI/CD continuo** mediante GitHub App.

---

## 🏗️ Arquitectura de Microservicios en Coolify

En lugar de un contenedor monolítico, Coolify orquesta cada servicio de forma aislada e intercomunicada a través de su red interna privada Docker:

```mermaid
graph TD
    User([🌐 Tráfico Web HTTPS]) -->|Puerto 443 / SSL Let's Encrypt| Traefik[Coolify Reverse Proxy / Traefik]
    Traefik -->|HTTP / Puerto 80| App[🚀 Laravel 13 + FrankenPHP<br/>Web Application]
    
    subgraph "Microservicios en Red Interna Privada"
        App <-->|DNS: postgresql:5432| DB[(🐘 PostgreSQL 17 + pgvector)]
        App <-->|DNS: redis:6379| Redis[(⚡ Redis 7 Cache & Queues)]
        Horizon[👷 Horizon Workers & Scheduler] <--> DB
        Horizon <--> Redis
    end

    App <-->|WebSockets| Ably([☁️ Ably Realtime Cloud])
    Horizon <-->|Generación & Redacción| OpenRouter([🧠 OpenRouter Multi-Model AI])
    Horizon <-->|Imágenes de Portada| Fal([🎨 Fal.ai / Imagen 3])
```

---

## 📋 Requisitos Previos

1. **VPS en Contabo:**
   * Recomendado: **Cloud VPS M o L** (mínimo 4-6 vCPU, 8-16 GB RAM, disco NVMe).
   * Sistema Operativo: **Ubuntu 24.04 LTS** o **Ubuntu 22.04 LTS** limpio.
2. **Dominio Apuntado al VPS:**
   * Registro `A` en tu proveedor DNS (Cloudflare, Namecheap, etc.):
     * `noticias.com` ➔ `<IP_PUBLICA_CONTABO>`
     * `*.noticias.com` ➔ `<IP_PUBLICA_CONTABO>` (opcional si usas subdominios)
3. **Repositorio Privado en GitHub:**
   * Repositorio con el código del proyecto (`usuario/news`).

---

## 🛠️ PASO 1: Preparar el VPS Contabo e Instalar Coolify

Conéctate por SSH a tu servidor Contabo:

```bash
ssh root@<IP_PUBLICA_CONTABO>
```

### 1.1 Actualizar el Sistema Operativo
```bash
apt update && apt upgrade -y
apt install -y curl wget git ufw htop
```

### 1.2 Configurar Firewall (UFW)
Abre únicamente los puertos estrictamente necesarios:
```bash
ufw allow 22/tcp     # SSH
ufw allow 80/tcp     # HTTP (Let's Encrypt / Traefik)
ufw allow 443/tcp    # HTTPS (Traefik)
ufw allow 8000/tcp   # Panel Administrativo de Coolify
ufw enable
```

### 1.3 Instalar Coolify Oficial
Ejecuta el script oficial de instalación desatendida:
```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```
Una vez finalizada la instalación, accede a tu panel:
👉 **`http://<IP_PUBLICA_CONTABO>:8000`**

Crea tu cuenta de administrador principal.

---

## 🐙 PASO 2: Conectar GitHub Privado con Coolify (GitHub App)

Para que cada `git push origin master` despliegue automáticamente:

1. En el menú lateral de Coolify, ve a **Keys & Tokens** ➔ **Git Sources**.
2. Haz clic en **+ Add Source** y selecciona **GitHub App**.
3. Sigue el asistente de Coolify para instalar la GitHub App en tu cuenta de GitHub y dale permisos sobre el repositorio privado del proyecto (`news`).
4. **Resultado:** Coolify creará automáticamente los Webhooks para CI/CD continuo sin que tengas que gestionar claves SSH manualmente.

---

## 🗄️ PASO 3: Crear el Microservicio de Base de Datos (PostgreSQL + pgvector)

1. En Coolify, ve a **Projects** ➔ **Default** ➔ **Production** (o crea un proyecto nuevo llamado `Noticias Platform`).
2. Haz clic en **+ New Resource** ➔ **Database** ➔ **PostgreSQL**.
3. **Configuración del Recurso:**
   * **Resource Name:** `noticias-postgres`
   * **Image:** `pgvector/pgvector:pg17` *(Crucial para soporte de vectores de búsqueda semántica y anti-duplicados)*.
   * **Database Name:** `noticias`
   * **Username:** `noticias_user`
   * **Password:** *(Genera una contraseña segura)*
4. En la pestaña **Configuration** ➔ **PostgreSQL Settings** o **Docker Run Options**:
   * Agrega en comandos/argumentos de inicio:
     ```text
     -c shared_preload_libraries=vector -c max_connections=200
     ```
5. Haz clic en **Deploy**.
6. Copia el **Internal URL** generado por Coolify (ejemplo: `postgresql://noticias_user:PASSWORD@noticias-postgres:5432/noticias`).

---

## ⚡ PASO 4: Crear el Microservicio de Cache y Colas (Redis 7)

1. En el mismo proyecto, haz clic en **+ New Resource** ➔ **Database** ➔ **Redis**.
2. **Configuración del Recurso:**
   * **Resource Name:** `noticias-redis`
   * **Image:** `redis:7-alpine`
   * **Password:** *(Opcional o define una contraseña)*
3. En **Commands / Arguments**:
   ```text
   redis-server --appendonly yes
   ```
4. Haz clic en **Deploy**.
5. Copia el **Internal Hostname** (ejemplo: `noticias-redis`).

---

## 🚀 PASO 5: Desplegar la Aplicación Web (Laravel 13 + FrankenPHP)

1. En el mismo proyecto, haz clic en **+ New Resource** ➔ **Public / Private Git Repository**.
2. Selecciona la **GitHub App** configurada en el Paso 2 y elige tu repositorio privado (`usuario/news`), rama: `master`.
3. **Build Pack:** Selecciona **Dockerfile** (usará el archivo `Dockerfile` optimizado en la raíz del proyecto).
4. **General Settings:**
   * **Resource Name:** `noticias-app`
   * **Domains:** `https://tudominio.com` *(Coolify solicitará el certificado SSL Let's Encrypt automáticamente)*.
   * **Exposed Port:** `80`
5. **Variables de Entorno (Environment Variables):**
   Agrega las variables de producción en la pestaña **Environment Variables**:

```dotenv
APP_NAME=Glodaxia
APP_ENV=production
APP_KEY=base64:GENERA_CON_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Conexión a Microservicio PostgreSQL (Nombre del contenedor en red interna)
DB_CONNECTION=pgsql
DB_HOST=noticias-postgres
DB_PORT=5432
DB_DATABASE=noticias
DB_USERNAME=noticias_user
DB_PASSWORD=TU_PASSWORD_POSTGRES

# Conexión a Microservicio Redis (Nombre del contenedor en red interna)
REDIS_HOST=noticias-redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
CACHE_PREFIX=noticias_cache
SESSION_DRIVER=redis
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis

# WebSockets / Realtime (Ably)
BROADCAST_CONNECTION=ably
ABLY_KEY=tu-api-key-de-ably
VITE_ABLY_PUBLIC_KEY=tu-api-key-publica-ably

# IA & Modelos (OpenRouter & Generación de Imágenes)
OPENROUTER_API_KEY=sk-or-v1-tu-api-key
FAL_KEY=tu-api-key-fal-ai

# Mail (Configuración de tu proveedor transaccional: Resend, Mailgun o Postmark)
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_tu_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@tudominio.com"
MAIL_FROM_NAME="Glodaxia Magazine"

# Cadencia de Publicación Escalonada
PUBLISH_CADENCE_HOURS=2
```

6. **Post-Deployment Commands (En Coolify -> Settings -> Post-deployment command):**
   ```bash
   php artisan migrate --force && php artisan optimize && php artisan horizon:terminate
   ```
7. Haz clic en **Deploy**.

---

## 👷 PASO 6: Configurar Horizon (Workers de IA) y Cron Scheduler

Para que la ingesta de noticias RSS y la redacción con IA funcionen de forma ininterrumpida:

### 6.1 Worker de Horizon en Segundo Plano
1. En Coolify, dentro de tu aplicación `noticias-app` ➔ pestaña **Background Workers** (o crea un segundo recurso desde el mismo repo llamado `noticias-horizon`):
   * **Command:** `php artisan horizon`
   * **Restart Policy:** `Always`

### 6.2 Cron / Scheduled Tasks (Ingesta RSS cada minuto)
1. En tu aplicación `noticias-app` ➔ pestaña **Scheduled Tasks**:
   * **Name:** `Laravel Scheduler`
   * **Cron Expression:** `* * * * *` (cada minuto)
   * **Command:** `php artisan schedule:run`

---

## 🛡️ PASO 7: Inicialización y Primer Usuario Administrador

Una vez que la aplicación esté desplegada y la base de datos migrada:

1. En Coolify, entra al recurso `noticias-app` ➔ pestaña **Terminal** / **Execute Command**:
2. Ejecuta el seeder para crear las 8 categorías maestras, los 33 tags y el usuario administrador:
   ```bash
   php artisan db:seed
   ```
3. O crea un administrador directamente con:
   ```bash
   php artisan make:filament-user
   ```

---

## ✅ Checklist de Verificación de Producción

| Punto de Control | Verificación | Estado |
| :--- | :--- | :---: |
| **Certificado SSL / HTTPS** | Acceder a `https://tudominio.com` y verificar candado verde SSL | 🟢 |
| **Panel Filament v5** | Iniciar sesión en `https://tudominio.com/admin` | 🟢 |
| **Microservicio Postgres** | Extensión `pgvector` activa y tablas creadas | 🟢 |
| **Microservicio Redis** | Dashboard de Horizon en `https://tudominio.com/horizon` muestra estado **Active** | 🟢 |
| **CI/CD Automático** | Hacer un `git push origin master` y comprobar que Coolify despliega el commit | 🟢 |
| **WebSockets (Ably)** | Comprobar que las noticias publicadas emiten evento al frontend | 🟢 |
| **SEO & Slugs** | Verificar `/es/buscar`, `/search`, `/es/categorias`, `/categories` con 301 | 🟢 |