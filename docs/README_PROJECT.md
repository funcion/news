# Glodaxia — Plataforma de Noticias Automatizadas con IA

> **Stack:** Laravel 13 + PostgreSQL + Redis + Horizon + Reverb + FrankenPHP  
> **Dominio:** IA y Automatización (Tech News)  
> **Idiomas:** EN (primario) + ES | Pathless default → `/`, Spanish → `/es/`

---

## 🚀 Quick Start

```bash
# 1. Entrar al directorio
cd ~/news

# 2. Levantar todo Docker
docker compose up -d

# 3. Entrar al container
docker compose exec app bash

# 4. Migrar y sembrar
php artisan migrate --seed
php artisan shield:generate --all

# 5. Build frontend (si es necesario)
npm run build

# 6. Storage symlink
php artisan storage:link

# 7. URLs
# Frontend → http://localhost:8000
# Admin    → http://localhost:8000/admin
# Horizon  → http://localhost:8000/horizon

# 8. Configurar R2 (producción — ver docs/task/CLOUDFLARE_R2_SETUP.md)
# En .env: R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET, R2_ENDPOINT, R2_PUBLIC_URL
# php artisan media:migrate-to-r2 --dry-run  (verificar antes de migrar)
# php artisan media:migrate-to-r2            (migrar imágenes a R2)
```

---

## 📦 Stack Técnico

| Capa | Tech | Version |
|------|------|---------|
| PHP | FrankenPHP | 8.3 |
| Framework | Laravel | 13 |
| Admin | Filament | 5.x |
| Livewire | Livewire | 4.x |
| DB | PostgreSQL + pgvector | 17 |
| Cache/Queue | Redis | 7 |
| Queue Workers | Laravel Horizon | 5.x |
| WebSocket | Laravel Reverb | 1.x |
| Translations | spatie/laravel-translatable | 6.x |
| Media | spatie/laravel-medialibrary | 11.x |
| Frontend | Blade + Alpine.js + Tailwind | Vite 6 |
| Image Storage | Cloudflare R2 | S3-compatible, $0 egress |

---

## 🤖 Sistema IA (Pipeline de Redacción)

### Arquitectura del Pipeline
```
RawArticle (created)
  → RawArticleObserver
  → ProcessArticleWithAIJob
  → AI Classification (OpenRouter)
  → AI Redacción Bilingüe (OpenRouter)
  → Image Generation (SiliconFlow FLUX.1)
  → Spatie MediaLibrary → R2 or Local (MEDIA_DISK)
  → Article Published
  → ArticlePublished Event (Reverb)
```

### Configuración de Modelo

Archivo: `app/Services/AI/OpenRouterService.php`

```php
// Para cambiar modelo, edita MODEL_ACTIVE:
public const MODEL_ACTIVE = 'deepseek/deepseek-v4-pro';
```

**Modelos disponibles:**
| Modelo | ID en OpenRouter | Velocidad |
|--------|------------------|-----------|
| Google Gemini 2.5 Flash | `google/gemini-2.5-flash` | Rápido, barato |
| Google Gemini 2.5 Pro | `google/gemini-2.5-pro` | Más inteligente |
| DeepSeek V4 Flash | `deepseek/deepseek-v4-flash` | Rápido |
| DeepSeek V4 Pro | `deepseek/deepseek-v4-pro` | Razonamiento |
| Qwen3-Plus | `qwen/qwen3.6-plus` | Alibaba |
| MiniMax M2.7 | `minimax/minimax-m2.7` | |

**API Keys en `.env`:**
```env
OPENROUTER_API_KEY=sk-or-v1-...
SILICONFLOW_API_KEY=sk-...
```

---

## 🎯 Optimizaciones del Prompt (v10/10)

### Correcciones aplicadas (9 en total)

| # | Corrección | Estado |
|---|-----------|--------|
| 1 | Validación de entrada (3 facts mínimos) | ✅ |
| 2 | Variables con defaults | ✅ |
| 3 | Primera persona obligatoria | ✅ |
| 4 | Anécdota humana forzada | ✅ |
| 5 | Bloqueo de fechas futuras | ✅ |
| 6 | Validación de meta/slug en PHP | ✅ |
| 7 | Target length con verificación | ✅ |
| 8 | JSON robusto (escape, fences, trailing commas) | ✅ |
| 9 | Etiquetas HTML whitelist estricta | ✅ |

### parseJson robusto
```php
// Maneja:
// 1. <think> blocks de razonamiento
// 2. Markdown fences ```json
// 3. Texto antes/después del JSON
// 4. Trailing commas de reparación automática
```

---

## 🗄️ Base de Datos

### Migraciones Principales
- `users` — Usuarios (Filament admin)
- `sources` — Fuentes RSS (feeds de IA)
- `raw_articles` — Artículos crudos del scraper
- `authors` — Autores IA con bio/traducciones
- `categories` — Categorías multilingüe (translatable)
- `articles` — Artículos publicados (bilingüe)
- `tags` — Tags con traducciones
- `article_tag` — Pivot table
- `article_updates` — Updates de artículos

### Semillas
```bash
php artisan migrate:fresh --seed
# Seed: admin user, categorías AI, tags, fuentes RSS
```

---

## 🖥️ Frontend

### Estructura
```
resources/
├── views/
│   ├── components/layouts/app.blade.php  # Layout principal
│   ├── components/ui/article-card.blade.php
│   ├── home.blade.php                    # Homepage
│   └── article/show.blade.php            # Detalle artículo
├── css/
│   ├── app.css                           # Tailwind + componentes
│   └── components/theme-compatibility.css
└── js/
    ├── app.js                            # Alpine.js + Echo
    └── bootstrap.js                      # Axios, Echo, Pusher
```

### Características
- Dark/Light mode toggle (localStorage)
- Banderas CSS (no imágenes externas)
- Menú mobile responsive
- Sticky header con scroll effect
- Sociales share (FB, X, WhatsApp, Telegram, Pinterest, Email)
- Newsletter sidebar
- Trending tags widget

---

## 🔧 Comandos Útiles

```bash
# Refrescar caché
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear

# Crear usuario admin
docker compose exec app php artisan make:filament-user

# Ejecutar ingesta de RSS (Manual / Forzado)
docker compose exec app php artisan rss:fetch
docker compose exec app php artisan rss:fetch --force

# Limpiar tags huérfanos
docker compose exec app php artisan tags:cleanup

# Reintentar artículo fallido
docker compose exec app php artisan tinker
>>> App\Models\RawArticle::where('id', X)->update(['status' => 'pending']);
>>> App\Jobs\ProcessArticleWithAIJob::dispatch(App\Models\RawArticle::find(X));
```

---

## 📁 Estructura Modular del Proyecto

```
app/
├── Console/Commands/
│   ├── RssFetchCommand.php           # Fetch RSS command
│   └── TagsCleanupCommand.php        # Cleanup tags
├── Events/
│   └── ArticlePublished.php          # Broadcats via Reverb
├── Filament/
│   ├── Pages/Dashboard.php
│   └── Resources/
│       ├── ArticleResource.php       # CRUD Artículos
│       ├── AuthorResource.php        # CRUD Autores
│       ├── CategoryResource.php      # CRUD Categorías
│       ├── RawArticleResource.php    # Artículos crudos
│       ├── SourceResource.php        # Fuentes RSS
│       └── TagResource.php           # Tags
├── Http/Controllers/
│   └── FrontendController.php        # Rutas públicas
├── Jobs/
│   ├── FetchRssFeedJob.php           # Fetch RSS asincrónico
│   └── ProcessArticleWithAIJob.php   # Pipeline IA completo
├── Models/
│   ├── Article.php (translatable)    # EN/ES content
│   ├── Author.php (translatable)
│   ├── Category.php (translatable)
│   ├── RawArticle.php
│   ├── Source.php
│   ├── Tag.php (translatable)
│   └── ArticleUpdate.php
├── Observers/
│   └── RawArticleObserver.php        # Auto-dispatch AI job
├── Providers/
│   ├── AppServiceProvider.php
│   └── Filament/AdminPanelProvider.php
└── Services/AI/
    ├── OpenRouterService.php          # API OpenRouter (multi-model)
    ├── SiliconFlowImageService.php     # Imágenes FLUX.1
    ├── TagGeneratorService.php         # Tags via IA
    └── DuplicateCheckerService.php     # Anti-duplicados
```

---

## ⚠️ Problemas Conocidos (Resueltos)

| Problema | Causa | Fix |
|----------|-------|-----|
| `preg_replace` crash | Delimitador `/` en regex | `~` delimiter |
| Timeout 60s → 120s | Reasoning models lentos | `timeout(420)` en HTTP |
| Job timeout 300s → 600s | DeepSeek V4 3-5min | `$timeout = 600` |
| Storage 403 | Symlink roto | Entrypoint auto-repair |
| GitHub push blocked | `.env` con API keys | `.env.example` + `.gitignore` |
| `self::` en PHP 8.3 | Constants syntax error | Direct string constant |
| `failed()` reset status | Race condition con retries | Solo marca `failed` al agotar intentos |

---

## 🔜 Roadmap

### Fase 1 (Completada ✅)
- [x] Ingesta RSS manual
- [x] Pipeline IA: clasificación → redacción → publicación
- [x] Imágenes BI con SiliconFlow FLUX
- [x] Admin Filament completo
- [x] Frontend responsive EN/ES
- [x] Dark/Light mode

### Fase 2 (En desarrollo)
- [ ] Scheduler RSS automático (`everyFiveMinutes`)
- [ ] Reverb WebSocket funcionando (broadcast en vivo)
- [ ] Fuentes RSS reales configuradas (20+ feeds)
- [ ] Embeddings pgvector para artículos relacionados
- [ ] SEO XML Sitemap automático

### Fase 3 (Futuro)
- [ ] Newsletter (subscribers + email)
- [ ] Monetización (afiliados, ads, sponsorships)
- [ ] Multi-nicho (Ciberseguridad como nicho complementario)
- [ ] CDN (Cloudflare) para assets
- [ ] Analytics dashboard (Laravel Pulse)
