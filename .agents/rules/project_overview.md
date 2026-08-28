# 🧭 Memoria del Proyecto y Reglas Arquitectónicas: Glodaxia (Noticias Platform)

Este archivo define la identidad, arquitectura técnica, dependencias del stack, patrones de diseño y reglas de negocio inviolables de **Glodaxia**. Toda interacción y desarrollo debe respetar estrictamente estos lineamientos.

---

## 🏛️ 1. Identidad y Propósito del Proyecto

**Glodaxia** es un medio digital y portal de noticias de tecnología automatizado con IA de grado industrial, diseñado para competir con grandes publicaciones (*The Verge*, *TechCrunch*, *Ars Technica*).
- **Core:** Ingesta multicanal RSS/Atom -> Curaduría y deduplicación con IA -> Redacción bilingüe humanizada (EN/ES) -> Generación de imágenes optimizadas -> Publicación programada con rate limiting y jitter temporal -> Distribución en tiempo real vía WebSockets y SEO técnico avanzado.

---

## 🛠️ 2. Stack Tecnológico

| Capa | Tecnología | Detalles / Paquetes Clave |
|---|---|---|
| **Backend Framework** | **Laravel 13** (PHP 8.3 / 8.2+) | Eloquent, Fortify, Socialite, Sanctum, Horizon |
| **Servidor / Runtime** | **FrankenPHP** | Caddy Server integrado, Worker Mode, HTTP/3, SSL automático |
| **Base de Datos** | **PostgreSQL 17** | Extensión pgvector para búsqueda de similitud de embeddings |
| **Cache y Colas** | **Redis 7** | Broker de colas en segundo plano, cache de tags y permisos en RAM |
| **Realtime / WebSockets** | **Ably Realtime** | bly/ably-php, laravel-echo, pusher-js |
| **Monitor de Colas** | **Laravel Horizon** | Dashboard en /horizon, supervisión de colas y fallos |
| **Panel Administrativo** | **Filament v5** | ilament/filament v5, ezhansalleh/filament-shield, spatie-laravel-media-library-plugin |
| **Frontend** | **Blade + Alpine.js + Tailwind CSS** | Vite, @alpinejs/focus, anilla-cookieconsent, Tailwind typography/forms |
| **Gestión de Medios** | **Spatie MediaLibrary** | Soporte para almacenamiento local y Cloudflare R2 (league/flysystem-aws-s3-v3) |
| **Modelos de IA** | **OpenRouter API** | openai-php/client, DeepSeek V4 Pro, Claude, GPT, CircuitBreaker |
| **Generación de Imágenes** | **SiliconFlow (FLUX.1) / Fal.ai** | Fallback visual con GD Library, nombrado SEO ({slug_es}-{num}.webp) |
| **Ingesta & Scraping** | **Feed Reader + Jina Reader** | edmant/laravel-feed-reader, simplepie/simplepie, ScraperService |
| **Internacionalización** | **Bilingüe (EN / ES)** | mcamara/laravel-localization, spatie/laravel-translatable |

---

## 🛡️ 3. Reglas de Negocio y Algoritmos Críticos

### A. Pipeline Anti-Canibalización y Deduplicación (5 Fases)
1. **Fase 1 (URL Canónica):** Si la URL externa exacta ya fue procesada, se omite o actualiza.
2. **Fase 2 (Slug Canónico / event_slug):** La IA extrae un identificador del evento noticioso (ej: pple-m5-ultra-launch). Si existe en una ventana de 36 horas, **se consolida como actualización (ArticleUpdate)** para alimentar la misma URL y ganar frescura SEO sin duplicar.
3. **Fase 3 (Hash de Título MD5):** Comparación exacta normalizada de títulos.
4. **Fase 4 (Similitud Léxica):** TF-IDF y similitud coseno.
5. **Fase 5 (Similitud Semántica):** Búsqueda de vectores de proximidad con pgvector.

### B. Taxonomía de Bucle Cerrado (Closed-Loop Taxonomy)
- **Límite Estricto:** Máximo 3 tags por artículo (rray_slice en TagGeneratorService).
- La IA clasifica **obligatoriamente** dentro del catálogo maestro de tags existentes para evitar *Thin Content* y cuidar el *Crawl Budget*.

### C. Ingesta y Rate Limiting por Fuente
- Máximo **3 artículos diarios** por fuente para evitar monopolización del feed.
- **Excepciones de Bypass:** Artículos con importancia IA >= 8, feeds tipo tom, o ejecución forzada manual desde Filament.
- **Ingestion Max Age:** Omite feeds con fecha más antigua que max_age_days (usando Unix Timestamps).
- **Jina Reader Fallback:** Si un feed RSS trae menos de 300 caracteres de resumen, ScraperService descarga el cuerpo completo usando 
.jina.ai.

### D. Rate Limiting Temporal y Jitter de Publicación
- Publicación distribuida con semilla determinista diaria/horaria (mt_srand).
- Retraso aleatorio (Jitter) de 5 a 60 minutos en estatus draft antes de que el scheduler libere el artículo a published.

### E. Autoría y Regla de Oro de SuperAdmin
- **Autoría:** Los artículos generados por IA o creados en el admin **solo** pueden pertenecer a usuarios con rol 
edactor, dmin o super_admin activos. Los usuarios panel_user (lectores registrados) quedan excluidos.
- **Unicidad de SuperAdmin:** Existe 1 solo super_admin (dmin@glodaxia.com), blindado contra eliminación.
- **Transferencia al eliminar redactor:** Si se elimina un usuario del staff (UserObserver), todos sus artículos se reasignan automáticamente al super_admin.

---

## 🎨 4. Convenciones de Filament v5

- **Schemas unificados:** En Filament v5, los formularios e Infolists usan Filament\Schemas\Schema con el método components([...]) (nunca schema([...])).
- **Layouts con Container Queries:** Uso de breakpoints @md, @xl, etc.
- **Shield:** Roles y permisos gestionados con ezhansalleh/filament-shield v4, cacheados en Redis.

---

## ⚡ 5. Comandos Frecuentes (Vía Docker / WSL)

`ash
# Ingesta manual de RSS
docker compose exec app php artisan rss:fetch [--force]

# Pausar / Reanudar / Cancelar Ingesta
docker compose exec app php artisan ingestion:control pause
docker compose exec app php artisan ingestion:control resume
docker compose exec app php artisan ingestion:cancel-all

# Regenerar permisos Shield
docker compose exec app php artisan shield:generate --all --panel=admin --no-interaction

# Limpieza de caches
docker compose exec app php artisan optimize:clear
`

### Coolify Production Infrastructure (Current Active Resources)
- **PostgreSQL (pgvector:pg17)**:
  - Resource Name: postgresql-database-dkkc8004sks44w8s8kk4soso
  - Internal Host: dkkc8004sks44w8s8kk4soso
  - Internal Port: 5432
  - Database: postgres
  - User: postgres
  - Password: EX8yqyx4jBpds5PkXPacMjAt8h9GkecpKA7ikM91oddwUU6A98tzq0ftFSWSYtkM
- **Redis (redis:7.2)**:
  - Resource Name: 
edis-database-ngso04k040g08ocggg4wsws4
  - Internal Host: 
gso04k040g08ocggg4wsws4
  - Internal Port: 6379
  - Password: uXvERZI3cTnYIHZeM6jlEug0ZIGxZv72Bx7ThrV6HNJG91q9Ix55LIVWzptNIXnu
