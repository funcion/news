# 🗞️ Glodaxia — Checklist Maestro de Tareas Pendientes

> **Última actualización**: 3 Junio 2026
> **Stack**: Laravel 13 + Filament v5 + Livewire v4 + FrankenPHP + PostgreSQL + Redis
> **Estado actual**: Fases 1-3 completadas, publicando artículos con IA automáticamente
> **Dominio**: Comprado hace menos de 24 horas — sitio nuevo

---

## ✅ COMPLETADO (Referencia)

- [x] Docker + FrankenPHP + PostgreSQL + pgvector + Redis
- [x] RSS pipeline (scheduler cada 60s, 4 fuentes activas)
- [x] AI Pipeline (clasificación → redacción → imágenes → tags → publish)
- [x] 31+ artículos publicados automáticamente
- [x] Frontend responsive EN/ES con dark mode
- [x] SEO técnico (sitemap index, canonical, OG, hreflang, JSON-LD, news/images sitemaps)
- [x] Custom Code injection via Filament admin
- [x] RSS feed en `/feed.xml`
- [x] Migración Laravel 13 + Filament v5 + Livewire v4
- [x] Editorial workflow (approve/reject/review + email notifications)
- [x] Dashboard widgets (stats + pending review table)
- [x] Sitemap Index con 6 sub-sitemaps + hreflang bilingüe
- [x] IndexNow controller (verificación GET + ping POST)
- [x] Safety filters (age, trust, sensitivity, cross-duplication, strict categories)
- [x] Fuentes RSS con campo `trusted` y `max_age_days`
- [x] flushCache + IndexNow ping al publicar (IA + Filament approve)

---

## 🔴 SEMANA 1 — Despliegue y Fundación SEO

### Deploy en producción

- [ ] Elegir proveedor VPS (recomendado: **Hetzner CX22** ~$5/mes o **DigitalOcean** $6/mes)
- [ ] Comprar/configurar dominio (ya comprado ✅)
- [ ] Apuntar DNS del dominio al VPS (A record → IP del VPS)
- [ ] Instalar Docker + Docker Compose en el VPS
- [ ] Clonar repositorio en el VPS
- [ ] Configurar `.env` de producción:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL=https://{dominio}`
  - [ ] `DB_CONNECTION=pgsql` + credenciales de producción
  - [ ] `OPENROUTER_API_KEY` (el mismo)
  - [ ] `SILICONFLOW_API_KEY` (el mismo)
- [ ] `docker compose up -d` en producción
- [ ] `php artisan migrate --seed`
- [ ] `php artisan shield:generate --all`
- [ ] Verificar que el sitio carga en `https://{dominio}`
- [ ] Verificar HTTPS automático (Caddy genera certificado Let's Encrypt)
- [ ] Verificar que `/admin` funciona
- [ ] Verificar que Horizon está corriendo (`/horizon`)

### IndexNow — Activar

- [ ] Generar INDEXNOW_KEY: `openssl rand -hex 16`
- [ ] Crear archivo de verificación: `public/{key}.txt` con contenido `{key}`
- [ ] Agregar `INDEXNOW_KEY={key}` al `.env` de producción
- [ ] Reiniciar contenedor: `docker compose restart app`
- [ ] Verificar: `curl "https://{dominio}/indexnow?key={key}"` → debe retornar `{key}` (200)
- [ ] Verificar: `curl "https://{dominio}/{key}.txt"` → debe retornar `{key}`
- [ ] Registrar en Bing Webmaster Tools: https://www.bing.com/webmasters
  - [ ] Agregar sitio
  - [ ] Verificar propiedad (DNS TXT record recomendado)
  - [ ] Enviar sitemap: `https://{dominio}/sitemap.xml`
- [ ] Probar ping: publicar un artículo y revisar logs del contenedor

### Google — Primer contacto

- [ ] Crear cuenta en Google Search Console: https://search.google.com/search-console
- [ ] Agregar propiedad: `https://{dominio}`
- [ ] Verificar propiedad (DNS TXT record)
- [ ] Enviar sitemap: `https://{dominio}/sitemap.xml`
- [ ] Crear cuenta en Google Analytics 4: https://analytics.google.com
- [ ] Obtener Measurement ID (G-XXXXXXXXXX)
- [ ] Agregar tag GA4 al layout: `resources/views/components/layouts/app.blade.php`

---

## 🟡 SEMANAS 2-4 — Contenido + Indexación

### Generar masa crítica de contenido

- [ ] Verificar scheduler RSS activo (Horizon + scheduler)
- [ ] Agregar más fuentes RSS (objetivo: 8-10 fuentes activas)
- [ ] Objetivo: **50+ artículos publicados** para fin de mes 1
- [ ] Revisar calidad de artículos (títulos, contenido, imágenes, tags)
- [ ] Ajustar prompt si es necesario

### Monitorear indexación

- [ ] Google Search Console semanalmente (cobertura, sitemaps, errores)
- [ ] Bing Webmaster Tools (IndexNow pings, páginas indexadas)

### SEO on-page — Verificar

- [ ] Canonical URL correcta por artículo
- [ ] Hreflang apunta a versión alternativa
- [ ] JSON-LD válido (https://search.google.com/test/rich-results)
- [ ] Open Graph correcto (imagen + título)
- [ ] Sitemaps válidos (`/sitemap.xml`, `/sitemap-articles-en.xml`, `/sitemap-news.xml`, `/sitemap-images.xml`)

### Fixes de código

- [ ] `PendingReviewTable` widget: agregar `flushCache()` + `IndexNow ping` en approve/reject
- [ ] Google Ping en `IndexNowController::ping()`
- [ ] Commit y push

---

## 🟢 MES 2 — Crecimiento orgánico

### Analytics

- [ ] GA4 semanal: usuarios, páginas vistas, tiempo sesión, rebote, fuentes
- [ ] Evento GA4 `article_read` (scroll 50%+)
- [ ] Evento GA4 `search`

### Dashboard Filament

- [ ] Widget `TrafficOverviewWidget`
- [ ] Widget `TopArticlesWidget`

### Tracking vistas

- [ ] Job `TrackArticleViewJob` (incrementa `views` async)
- [ ] Limitar: 1 vista por IP por artículo por sesión

### Feedback Loop prompts IA

- [ ] Migration `article_edits` table
- [ ] Registrar ediciones manuales en Filament
- [ ] Analizar patrones y refinar prompt

### Distribución automática

- [ ] Telegram Bot (BotFather → canal → job `DispatchToTelegramJob`)
- [ ] Discord Webhook (crear webhook → job `DispatchToDiscordJob`)

---

## 🔵 MES 3 — Google News + Newsletter

### Google News Publisher

> ⚠️ No aplicar hasta tener 50+ artículos y 1 mes de indexación

- [ ] Verificar indexación en Search Console
- [ ] Ir a https://publishercenter.google.com → Agregar publicación
- [ ] Completar info (Glodaxia, URL, país, idioma, Technology)
- [ ] Verificar requisitos técnicos (sitemap-news ✅, canonical ✅, hreflang ✅, JSON-LD ✅)
- [ ] Crear páginas legales: `/privacy`, `/terms`, `/cookies`, `/dmca`
- [ ] Enviar solicitud → esperar 1-4 semanas

### Newsletter

- [ ] Migration `subscribers` + modelo
- [ ] Formulario suscripción en sidebar
- [ ] Endpoint `POST /subscribe` + doble opt-in
- [ ] Mailable `WeeklyNewsletter` + template responsive
- [ ] Job `SendWeeklyNewsletterJob` (scheduler viernes 10:00 AM)
- [ ] Link desuscripción + dashboard Filament

### Páginas legales

- [ ] `/privacy` — Política de privacidad
- [ ] `/terms` — Términos de servicio
- [ ] `/cookies` — Política de cookies + banner GDPR
- [ ] `/dmca` — DMCA policy
- [ ] Links en footer

---

## ⚪ TRANSVERSAL

### Seguridad
- [ ] HTTPS (Caddy automático ✅)
- [ ] Rate limiting rutas públicas
- [ ] Cloudflare free tier (DNS + WAF)
- [ ] Headers seguridad (CSP, X-Frame-Options, HSTS)

### Monitoreo
- [ ] UptimeRobot (gratis)
- [ ] Alertas si sitio cae
- [ ] Revisar Horizon semanalmente

### Backups
- [ ] PostgreSQL diario (`pg_dump` cron)
- [ ] Backup storage/app/public
- [ ] Guardar en S3/R2/disco externo
- [ ] Probar restore una vez

### CI/CD
- [ ] GitHub Actions: push master → deploy VPS
- [ ] Script: `git pull` → `docker compose build` → `up -d` → `migrate`

### Mejoras continuas
- [ ] Más fuentes RSS (objetivo: 10-15 activas)
- [ ] Monitorear calidad artículos semanalmente
- [ ] Campo `is_featured` + grid destacados en homepage
- [ ] Comando `artisan report:weekly` (lunes 8:00 AM)

---

## 📊 MÉTRICAS OBJETIVO

| Métrica | Mes 1 | Mes 3 | Mes 6 |
|---------|-------|-------|-------|
| Artículos publicados | 50+ | 150+ | 300+ |
| Páginas indexadas | 10+ | 50+ | 150+ |
| Visitas diarias | 10+ | 100+ | 500+ |
| Suscriptores newsletter | — | 20+ | 100+ |
| Tasa de rechazo IA | <5% | <5% | <5% |
| PageSpeed Mobile | >90 | >90 | >90 |
| Fuentes RSS activas | 6+ | 10+ | 15+ |

---

## 🔗 REFERENCIA RÁPIDA

| Qué | Dónde |
|-----|-------|
| Pipeline IA | `app/Jobs/ProcessArticleWithAIJob.php` |
| Servicio IA | `app/Services/AI/OpenRouterService.php` |
| Sitemap | `app/Http/Controllers/SitemapController.php` |
| IndexNow | `app/Http/Controllers/IndexNowController.php` |
| RSS Fetch | `app/Jobs/FetchRssFeedJob.php` |
| RSS Service | `app/Services/RssService.php` |
| Workflow admin | `app/Filament/Resources/ArticleResource.php` |
| Dashboard | `app/Filament/Pages/Dashboard.php` |
| Widgets | `app/Filament/Widgets/StatsOverview.php`, `PendingReviewTable.php` |
| Email notif | `app/Mail/ArticleStatusChanged.php` |
| Config IA | `config/global.php` |
| Config IndexNow | `config/services.php` → `indexnow.key` |
| Scheduler | `routes/console.php` |
| Docker | `docker-compose.yml`, `docker/frankenphp/` |

---

## ⏭️ PRÓXIMA ACCIÓN INMEDIATA

1. **Deploy en producción** — PRIORITARIO. Sin dominio público, nada de SEO funciona.
2. **Configurar IndexNow + Google Search Console** — el mismo día del deploy.
3. **Agregar GA4** — tracking desde día 1.
