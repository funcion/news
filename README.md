# 🗞️ Noticias Platform - Plataforma de Noticias Automatizada con IA

Plataforma de noticias automatizada con IA y RSS construida con Laravel 12, FrankenPHP, PostgreSQL, Redis y Filament.

## 🚀 Características Principales

- **Motor de Ingesta RSS**: Sistema automatizado para consultar fuentes RSS
- **Cerebro de IA**: Pipeline de procesamiento con 4 capas de IA
- **Generación de Imágenes**: Creación automática de imágenes únicas
- **Sistema Anti-Duplicados**: 3 niveles de detección de contenido similar
- **Frontend en Tiempo Real**: Actualizaciones con WebSockets (Reverb)
- **SEO Técnico Avanzado**: Optimizado para Google News y Featured Snippets
- **Sistema de Tags Inteligente**: Tags generados automáticamente por IA
- **Panel de Administración**: Filament v3 para gestión completa

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 12
- **Servidor Web**: FrankenPHP (PHP 8.3 + Caddy + HTTP/3)
- **Base de Datos**: PostgreSQL + pgvector
- **Cache/Colas**: Redis
- **Panel Admin**: Filament v3
- **WebSockets**: Laravel Reverb
- **Colas**: Laravel Horizon
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **IA**: OpenRouter (multi-modelo)

## 📦 Instalación

### 1. Requisitos Previos

- Docker y Docker Compose
- Git
- WSL2 (Windows) o Linux/macOS

### 2. Clonar el Repositorio

```bash
git clone <repository-url>
cd noticias
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
# Editar .env con tus configuraciones
```

### 4. Iniciar Contenedores

```bash
docker compose up -d
```

### 5. Instalar Dependencias

```bash
docker compose exec frankenphp composer install
docker compose exec frankenphp php artisan key:generate
```

### 6. Ejecutar Migraciones

```bash
docker compose exec frankenphp php artisan migrate
```

### 7. Iniciar Workers

```bash
# Horizon para colas
docker compose exec frankenphp php artisan horizon

# Reverb para WebSockets
docker compose exec frankenphp php artisan reverb:start
```

## 🏗️ Estructura del Proyecto

```
noticias/
├── app/                    # Código de la aplicación
│   ├── Console/           # Comandos Artisan
│   ├── Http/              # Controladores y middleware
│   ├── Models/            # Modelos Eloquent
│   ├── Providers/         # Service providers
│   └── Services/          # Servicios de negocio
├── config/                # Configuraciones
├── database/              # Migraciones y seeders
├── docker/                # Configuración Docker
│   ├── frankenphp/        # Configuración FrankenPHP
│   └── postgres/          # Configuración PostgreSQL
├── public/                # Assets públicos
├── resources/             # Vistas y assets
├── routes/                # Rutas
├── storage/               # Archivos y logs
└── tests/                 # Tests
```

## 🔧 Comandos Útiles

```bash
# Iniciar todos los servicios
docker compose up -d

# Detener todos los servicios
docker compose down

# Ver logs
docker compose logs -f

# Ejecutar migraciones
docker compose exec frankenphp php artisan migrate

# Ejecutar tests
docker compose exec frankenphp php artisan test

# Generar clave de aplicación
docker compose exec frankenphp php artisan key:generate

# Limpiar cache
docker compose exec frankenphp php artisan optimize:clear
```

## 📊 Servicios Disponibles

| Servicio | URL | Puerto | Descripción |
|----------|-----|--------|-------------|
| **FrankenPHP** | http://localhost | 80 | Aplicación principal |
| **PostgreSQL** | postgres:5432 | 5432 | Base de datos |
| **Redis** | redis:6379 | 6379 | Cache y colas |
| **Horizon** | http://localhost/horizon | 80 | Dashboard de colas |
| **Reverb** | ws://localhost:8080 | 8080 | WebSockets |
| **Mailpit** | http://localhost:8025 | 8025 | Cliente de email |
| **phpMyAdmin** | http://localhost:8081 | 8081 | Admin de DB (opcional) |

## 🚀 Desarrollo

### Configuración de Desarrollo

1. **Variables de entorno de desarrollo** en `.env`
2. **Configuración de FrankenPHP** en `docker/frankenphp/`
3. **Configuración de PostgreSQL** en `docker/postgres/`

### Extensión de VS Code Recomendadas

- PHP Intelephense
- Laravel Idea
- Docker
- PostgreSQL
- Tailwind CSS IntelliSense

## 📈 Producción

### Configuración Recomendada

1. **Actualizar variables de entorno** para producción
2. **Configurar SSL/TLS** en Caddyfile
3. **Configurar backups** automáticos de base de datos
4. **Configurar monitoreo** con Laravel Pulse
5. **Configurar CDN** (Cloudflare recomendado)

### Escalabilidad

- **FrankenPHP workers**: Ajustar según CPU disponible
- **PostgreSQL**: Configurar replicación y connection pooling
- **Redis**: Configurar cluster para alta disponibilidad
- **CDN**: Cloudflare para assets estáticos y edge caching

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama (`git checkout -b feature/amazing-feature`)
3. Commit cambios (`git commit -m 'Add amazing feature'`)
4. Push a la rama (`git push origin feature/amazing-feature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Para soporte, abrir un issue en el repositorio o contactar al equipo de desarrollo.

---

**Desarrollado con ❤️ para revolucionar la industria de noticias con IA**