# 📡 News Sources — Documentación de Fuentes

> **Objetivo:** Detectar noticias frescas de tecnología, lanzamientos, actualizaciones y tendencias antes que la competencia para publicar contenido de alto tráfico y alto interés con ventana de tiempo mínima.

---

## 🗂️ Índice

1. [GitHub: Lanzamientos y Versiones](#1-github-lanzamientos-y-versiones)
2. [APIs con Acceso Directo (Comunidades y Contenido Dev)](#2-apis-con-acceso-directo-comunidades-y-contenido-dev)
3. [RSS / Atom Feeds (Gratis, Sin API Key)](#3-rss--atom-feeds-gratis-sin-api-key)
4. [APIs de Noticias Generales y Agregadores](#4-apis-de-noticias-generales-y-agregadores)
5. [Herramientas de Monitoreo de Cambios y Changelogs](#5-herramientas-de-monitoreo-de-cambios-y-changelogs)
6. [Package Registries: NPM, PyPI, Packagist, etc.](#6-package-registries-npm-pypi-packagist-etc)
7. [Redes Sociales y Comunidades](#7-redes-sociales-y-comunidades)
8. [Fuentes de Tendencias y Búsquedas](#8-fuentes-de-tendencias-y-búsquedas)
9. [Fuentes de Ciberseguridad](#9-fuentes-de-ciberseguridad)
10. [Fuentes de IA y Machine Learning](#10-fuentes-de-ia-y-machine-learning)
11. [Fuentes de Startups y Financiación](#11-fuentes-de-startups-y-financiación)
12. [Canales de Desarrollo Interno y Pre-Lanzamientos](#12-canales-de-desarrollo-interno-y-pre-lanzamientos)
13. [Estrategia de Ingesta Recomendada](#13-estrategia-de-ingesta-recomendada)
14. [Resiliencia y Circuit Breakers](#14-resiliencia-y-circuit-breakers)
15. [Rate Limiting Distribuido](#15-rate-limiting-distribuido)
16. [Validación de Calidad del Contenido](#16-validación-de-calidad-del-contenido)
17. [Seguridad y Sanitización](#17-seguridad-y-sanitización)
18. [Plan de Implementación y Comandos de Monitoreo](#18-plan-de-implementación-y-comandos-de-monitoreo)

---

## 1. GitHub: Lanzamientos y Versiones

### 🔵 GitHub Releases via RSS/Atom (Sin límites de API Key)
- **URL:** `https://github.com/{owner}/{repo}/releases.atom`
- **Costo:** 🆓 100% Gratis, sin límites ni credenciales de API.
- **Por qué usarlo:** La **mina de oro absoluta** para ser pionero. Antes de que una empresa publique en su blog oficial sobre un nuevo release, el tag y changelog se suben aquí. Monitorear los feeds `.atom` permite recibir actualizaciones instantáneas en formato XML sin consumir el rate limit de la API REST de GitHub.

---

### 🔵 GitHub REST API — Releases y Repositorios Trending
- **URL:** https://api.github.com/
- **Costo:** 🆓 Gratis (60 req/hora sin auth, 5000 req/hora con token)
- **Endpoints clave:**
  ```
  GET /repos/{owner}/{repo}/releases/latest     → Último release de un repo
  GET /repos/{owner}/{repo}/releases            → Historial de releases
  GET /search/repositories?q=created:>2024-01-01&sort=stars → Repos trending
  GET /repos/{owner}/{repo}/commits             → Commits recientes
  ```
- **Repositorios críticos a monitorear:**
  - **Lenguajes:** `php/php-src`, `python/cpython`, `golang/go`, `rust-lang/rust`, `nodejs/node`
  - **Frameworks:** `laravel/laravel`, `laravel/framework`, `facebook/react`, `vuejs/core`, `tailwindlabs/tailwindcss`, `filamentphp/filament`, `livewire/livewire`, `inertiajs/inertia`, `vercel/next.js`, `django/django`, `angular/angular`
  - **Herramientas/Runtimes:** `oven-sh/bun`, `denoland/deno`, `kubernetes/kubernetes`, `docker/engine`, `tensorflow/tensorflow`

---

### 🔵 GitHub Webhooks (Monitoreo Activo)
Si controlas los repositorios o configuras webhooks mediante integración/aplicaciones externas para recibir notificaciones en tiempo real en tu endpoint:
```json
// POST /repos/{owner}/{repo}/hooks
{
  "name": "web",
  "events": ["release"],
  "config": {
    "url": "https://your-domain.com/api/webhooks/github-releases",
    "content_type": "json"
  }
}
```

---

### 🔵 Filtro de Relevancia Previo (Evitar Ruido en Releases)
El 95% de los parches menores no ameritan una noticia. Este filtro descarta automáticamente parches rutinarios (ej: v1.2.3) a menos que incluyan términos clave de seguridad o deprecaciones:
```php
$isNoticiable = false;

if (stripos($release->body, 'security') !== false) $isNoticiable = true;
if (stripos($release->body, 'deprecation') !== false) $isNoticiable = true;
if (stripos($release->name, 'RC') !== false) $isNoticiable = true; // Release Candidate
if (preg_match('/v\d+\.\d+\.0/', $release->name)) $isNoticiable = true; // Major o Minor

// Filtrar parches menores (Patch) si no son de seguridad/urgencia
if (preg_match('/v\d+\.\d+\.\d+/', $release->name) && !$isNoticiable) {
    return; // Descartar automáticamente
}
```

---

### 🔵 Monitoreo de "Primer Commit" (Nuevos Proyectos)
A veces, el primer commit público de un repositorio de alto interés (como un fork exitoso de Redis) es noticia antes de su primer tag de release:
- **Endpoint:** `GET /repos/{owner}/{repo}/commits?per_page=1&order=asc`

---

### 🔵 GitHub Trending (Sin API oficial — Scraping/RSS)
- **RSS:** `https://github.com/trending/{language}` (HTML, requiere parsing)
- **Alternativa API:** https://github-trending-api.vercel.app/ (no oficial pero funcional)

---

### 🔵 Herramientas Self-Hosted de Tracking
- **GitHub Release Monitor:** Aplicación auto-hospedada que monitorea releases de tus repos agregados y envía alertas.
- **Releases Tracker:** Automatizaciones ligeras que se suscriben a changelogs y disparan payloads JSON.

---

## 2. APIs con Acceso Directo (Comunidades y Contenido Dev)

### 🔵 Hacker News API (Dos alternativas)

1. **Firebase API (Oficial - Tiempo Real):**
   - **URL:** https://hacker-news.firebaseio.com/v0/
   - **Costo:** 🆓 Gratis, sin API key
   - **Endpoints clave:**
     ```
     GET /v0/newstories.json      → IDs de las últimas ~500 noticias
     GET /v0/topstories.json      → Top stories del momento
     GET /v0/item/{id}.json       → Detalle de una noticia
     ```
   - **Advertencia de Latencia:** Las historias en Hacker News pueden tardar entre **30 y 60 minutos** en aparecer en `/newstories.json`. Utilizar HN principalmente como **validador de relevancia**, no como único detector de tiempo real absoluto.

2. **Algolia API (Búsqueda Avanzada y Keywords):**
   - **URL:** https://hn.algolia.com/api/v1/
   - **Costo:** 🆓 Gratis, sin API key
   - **Endpoints clave:**
     ```
     GET /api/v1/search_by_date?query=python&tags=story  → Historias ordenadas por fecha sobre una tecnología
     GET /api/v1/search?query=AI&tags=story             → Búsqueda por relevancia
     ```

---

### 🔵 DEV.to API
- **URL:** https://dev.to/api/
- **Endpoints clave:**
  ```
  GET /api/articles?top=1&per_page=30    → Top artículos del día
  GET /api/articles?tag=javascript        → Por tag tecnológico
  GET /api/articles?state=fresh          → Artículos recién publicados
  ```

---

### 🔵 Product Hunt API (GraphQL v2)
- **URL:** https://api.producthunt.com/v2/api/graphql
- **Nota de Prioridad:** Mover a **Tier 2 o Tier 3**. PH sirve para lanzamientos de productos de software y SaaS, pero raramente contiene "breaking news" inmediatas.

---

### 🔵 Reddit API (PRAW / REST)
- **URL:** https://www.reddit.com/dev/api/
- **Subreddits tech:** `r/programming`, `r/webdev`, `r/javascript`, `r/Python`, `r/laravel`, `r/MachineLearning`, `r/artificial`, `r/technology`, `r/netsec`, `r/golang`, `r/rust`, `r/node`, `r/devops`, `r/aws`, `r/sysadmin`, `r/Monitors`.

---

## 3. RSS / Atom Feeds (Gratis, Sin API Key)

### Blogs Oficiales de Lenguajes y Frameworks (Críticos)
- **Python:** `https://blog.python.org/feeds/posts/default`
- **Go:** `https://go.dev/blog/feed.xml`
- **Rust:** `https://blog.rust-lang.org/feed.xml`
- **Node.js:** `https://nodejs.org/en/feed/blog.xml`
- **React:** `https://feeds.feedburner.com/reactjs`
- **Angular:** `https://blog.angular.io/feed`
- **Laravel News:** `https://laravel-news.com/feed`
- **Symfony Blog:** `https://symfony.com/blog/feed/atom`

---

### Feeds de Documentación y Guías de Migración
Monitorear cambios en la documentación oficial para detectar la inclusión de nuevas APIs o explicaciones de migración:
- **Laravel Docs:** `https://laravel.com/docs/master/releases.atom`
- **Django Docs Releases:** `https://docs.djangoproject.com/en/stable/releases/`
- **MDN Web Docs (APIs Web):** `https://developer.mozilla.org/en-US/docs/Web/HTTP/Status.atom`

---

### Medios Técnicos Premium
- **TechCrunch:** `https://techcrunch.com/feed/`
- **The Verge:** `https://www.theverge.com/rss/index.xml`
- **Wired:** `https://www.wired.com/feed/rss`
- **Ars Technica:** `https://feeds.arstechnica.com/arstechnica/index`
- **VentureBeat:** `https://venturebeat.com/feed/`

---

### HackerNoon RSS Feeds (Granularidad por Etiquetas y Autores)
- **Feed General:** `https://hackernoon.com/feed`
- **Estructura por Etiqueta (Tag):** `https://hackernoon.com/tagged/{tag}/feed` (ej: `https://hackernoon.com/tagged/blockchain/feed`, donde cualquier etiqueta de tecnología reemplaza a `blockchain`).
- **Estructura por Autor:** `https://hackernoon.com/feed/u/{username}` (ej: `https://hackernoon.com/feed/u/davidsmooke`, donde cualquier usuario reemplaza a `davidsmooke`).
- **Detalles Clave:**
  - Los artículos de HackerNoon tienen un promedio de más de 750 lecturas RSS.
  - Los feeds RSS incluyen las **20 noticias más recientes** (en su versión actual).
  - **Estrategia Recomendada:** En lugar de agregar el feed general para evitar el ruido de contenido masivo, podemos agregar selectivamente feeds de etiquetas clave (ej: `ai`, `programming`, `security`) o autores específicos de renombre. Esto permite curar solo las mejores fuentes de noticias de HackerNoon y no procesar todo su flujo.

---

### Herramientas de Procesamiento de Feeds
- **Feedly:** Agregador visual estructurado.
- **Inoreader:** Soporta filtros complejos y reglas automáticas sobre feeds.
- **RSS.app:** Útil para convertir en feed RSS cualquier sitio que no cuente con uno nativo.

---

## 4. APIs de Noticias Generales y Agregadores

### 🟡 NewsAPI.org
- **URL:** https://newsapi.org/
- **Costo:** Gratis (100 req/día, uso no comercial)

### 🟡 NewsAPI.ai (Event Registry)
- **Costo:** Desde $299/mes para uso comercial.
- **Por qué usarlo:** Agrupa noticias duplicadas o similares en un solo evento lógico y extrae entidades, conceptos y relaciones semánticas.

### 🟡 APITube.io
- **Costo:** Tier gratuito disponible.
- **Por qué usarlo:** Específico para noticias de tecnología. Categoriza automáticamente los artículos en nichos como software, hardware, ciberseguridad, cloud y AI.

### 🟡 NewsData.io
- **Costo:** Tier gratuito disponible (200 créditos/día).
- **Por qué usarlo:** Búsquedas masivas en múltiples idiomas simultáneos con análisis de sentimiento y categorización.

### 🟡 You.com API
- **Costo:** Pago según uso.
- **Por qué usarlo:** Indexación en tiempo real (< 5 minutos). Sirve para comprobar rápidamente si una tendencia recién detectada ya está documentada en los buscadores principales.

---

## 5. Herramientas de Monitoreo de Cambios y Changelogs

### 🔵 breaking.watch
- **URL:** https://breaking.watch/
- **Enfoque:** Monitoreo y alertas inteligentes de changelogs de frameworks, librerías y APIs populares.
- **Utilidad:** Permite enterarse al instante de deprecaciones importantes y problemas de compatibilidad (breaking changes) listos para redactar artículos tipo guía o alerta.

### 🔵 Parallel AI Monitor API
- **Enfoque:** Monitoreo selectivo de páginas web específicas ante cambios reales en su estructura de texto (removiendo cambios de headers, pies de página o anuncios).

---

## 6. Package Registries: NPM, PyPI, Packagist, etc.

- **NPM Registry:** `GET https://registry.npmjs.org/{package}/latest` y feed en `https://www.npmjs.com/feed`
- **PyPI (Python):** `https://pypi.org/rss/updates.xml`
- **Packagist (Composer):** `GET https://packagist.org/packages/{vendor}/{package}.json`
- **crates.io (Rust):** `GET https://crates.io/api/v1/crates/{crate}`

---

## 7. Redes Sociales y Comunidades

- **Twitter / X API:** Monitoreo de hashtags como `#TechNews`, `#AI`, `#DevOps` (Tier básico $100/mes).
- **Mastodon / Fediverse:** Endpoint público `GET https://fosstodon.org/api/v1/timelines/tag/programming` (sin autenticación).
- **Discord:** Integración mediante Webhooks de anuncios oficiales en servidores de proyectos.

---

## 8. Fuentes de Tendencias y Búsquedas

- **Google Trends:** Monitoreo mediante librería microservicio en Python (`pytrends`) ante spikes de búsqueda.
- **Exploding Topics:** Temas técnicos emergentes antes de que exploten en masa.
- **Stack Overflow Trends API:**
  `https://api.stackexchange.com/2.3/questions?order=desc&sort=creation&tagged=laravel&site=stackoverflow`
- **Google Alerts RSS:** Monitorear feeds autogenerados `https://www.google.com/alerts/feeds/...` para búsquedas clave.

---

## 9. Fuentes de Ciberseguridad

- **CVE Details RSS:** `https://www.cvedetails.com/rss/last.xml`
- **NIST NVD JSON:** `https://nvd.nist.gov/feeds/json/cve/1.1/nvdcve-1.1-recent.json.gz`
- **Bleeping Computer Feed:** `https://www.bleepingcomputer.com/feed/`

---

## 10. Fuentes de IA y Machine Learning

- **ArXiv CS.AI / CS.LG:** Feeds de papers diarios de Inteligencia Artificial y Machine Learning.
- **Papers With Code:** `https://paperswithcode.com/latest.rss`
- **Hugging Face Papers:** `https://huggingface.co/papers.rss`

---

## 11. Fuentes de Startups y Financiación

- **TechCrunch Startups:** `https://techcrunch.com/startups/feed/`
- **YC Blog:** `https://www.ycombinator.com/blog/rss`

---

## 12. Canales de Desarrollo Interno y Pre-Lanzamientos

Monitoreo de canales primarios donde se debaten y aprueban características antes de ser escritas en código:
- **PHP RFCs (Votaciones de características):** `https://wiki.php.net/rss/rfc`
- **Mailing Lists Oficiales (Linux Kernel/Drivers):** `https://lists.linux.dev/`
- **Slack/Discord Oficiales de Frameworks:** Integración de notificaciones de canales del core team de desarrollo.

---

## 13. Estrategia de Ingesta Recomendada

### 🎯 Priorización por Velocidad de Detección

```
TIER 1 — Tiempo Real (< 5 minutos):
├── GitHub Releases RSS/Atom & Webhooks
├── Reddit /new /rising (subreddits clave)
└── Blogs oficiales de Lenguajes y Frameworks (RSS)

TIER 2 — Cuasi Tiempo Real (< 30 minutos):
├── Hacker News API ( Firebase + Algolia - Validador de Relevancia)
├── breaking.watch & Changelogs
├── DEV.to /articles?state=fresh
└── Feeds de Documentación oficial (Laravel, Django, MDN)

TIER 3 — Por Horario (cada hora):
├── NewsData.io & APITube.io
├── Product Hunt API (Lanzamientos de SaaS/startups)
└── Package Registries (NPM, PyPI, Packagist)
```

---

### 🏗️ Arquitectura Sugerida en Laravel (Ingesta Asíncrona)

```
Scheduler (app/Console/Kernel.php):
├── $schedule->job(new FetchHackerNewsJob)->everyFiveMinutes()
├── $schedule->job(new FetchGitHubReleasesJob)->everyTenMinutes()
├── $schedule->job(new FetchRssFeedsJob('tier1'))->everyFifteenMinutes()
└── $schedule->job(new FetchNewsApiJob)->hourly()
```

---

### 📊 Servicio de Scoring Ponderado (Laravel/PHP Pseudocódigo)
Filtro inteligente para calcular si una noticia amerita publicación automática basándose en señales de relevancia y competencia:
```php
class NewsScoringService
{
    public function calculateScore(RawArticle $article): float
    {
        $score = 0;
        
        // 1. Engagement (40% del peso)
        $score += $this->getEngagementScore($article) * 0.4;
        
        // 2. Autoridad de la Fuente (25% del peso)
        $score += $this->getSourceAuthorityScore($article) * 0.25;
        
        // 3. Nivel de Tendencia (20% del peso)
        $score += $this->getTrendScore($article) * 0.2;
        
        // 4. Competencia existente (15% del peso)
        $score += $this->getCompetitionScore($article) * 0.15;
        
        return $score;
    }
    
    private function getEngagementScore(RawArticle $article): float
    {
        if ($article->source === 'hackernews') {
            $score = ($article->metadata['points'] / 100) * 0.6 + ($article->metadata['comments'] / 50) * 0.4;
            return min($score, 1.0);
        }
        if ($article->source === 'reddit') {
            $score = ($article->metadata['upvotes'] / 500) * 0.6 + ($article->metadata['comments'] / 100) * 0.4;
            return min($score, 1.0);
        }
        if ($article->source === 'github') {
            $starsPerHour = $article->metadata['stars_24h'] / 24;
            return min($starsPerHour / 50, 1.0);
        }
        return 0.2;
    }
    
    private function getCompetitionScore(RawArticle $article): float
    {
        // Buscar si ya hay publicaciones similares recientes en español
        $existing = Article::where('published_at', '>=', now()->subHours(8))
            ->where('category_id', $article->category_id)
            ->where('meta_keywords', 'LIKE', '%' . $article->primary_keyword . '%')
            ->count();
            
        return max(0, 1 - ($existing / 10)); // Menos artículos en español = mayor puntuación
    }
}
```

---

### 🧠 Detección de Tendencias Emergentes (N-grams & Clusters)
Analizar repetición de palabras clave no catalogadas en las últimas 24 horas frente al histórico de la semana anterior para detectar términos nuevos que van a explotar:
```php
class TrendDetectionService
{
    public function detectEmergingTrends(): array
    {
        // 1. Extraer palabras clave de artículos de las últimas 24h con score > 0.5
        $recent = RawArticle::where('created_at', '>=', now()->subDay())->where('score', '>', 0.5)->get();
        $recentNgrams = $this->extractNgrams($recent);
        
        // 2. Extraer del histórico de los días 2-7
        $past = RawArticle::where('created_at', '>=', now()->subDays(7))->where('created_at', '<', now()->subDay())->get();
        $pastNgrams = $this->extractNgrams($past);
        
        $trends = [];
        foreach ($recentNgrams as $ngram => $count) {
            $pastCount = $pastNgrams[$ngram] ?? 0;
            if ($pastCount > 0) {
                $growth = ($count - $pastCount) / $pastCount;
                if ($growth > 3.0 && $count >= 10) { // Crecimiento > 300%
                    $trends[] = ['keyword' => $ngram, 'growth' => $growth, 'count' => $count];
                }
            }
        }
        return $trends;
    }
}
```

---

### 🌎 Estrategia de Publicación Multilingüe e Interlinking
Para optimizar el posicionamiento SEO, estructurar la publicación con un desfase de tiempo estratégico para indexar correctamente los hreflang:
```php
class MultilingualPublishingService
{
    public function publish(Article $article): void
    {
        // 1. Publicar versión en Inglés primero (Ventana global de indexación)
        $englishArticle = $this->publishInLanguage($article, 'en');
        
        // 2. Retrasar 30 minutos la publicación en Español para validación humana o indexaciónhreflang
        dispatch(function() use ($article, $englishArticle) {
            $spanishArticle = $this->publishInLanguage($article, 'es');
            
            // 3. Enlazar internamente ambas versiones (SEO Hreflang)
            $englishArticle->update(['alternate_version_id' => $spanishArticle->id]);
            $spanishArticle->update(['alternate_version_id' => $englishArticle->id]);
        })->delay(now()->addMinutes(30));
    }
}
```

---

### 🚨 Alertas Inteligentes (Slack / Discord Integration)
Enviar resúmenes a moderadores del canal editorial cuando una noticia tiene un scoring superior a 0.90 antes de su publicación automática:
```php
class AlertService
{
    public function sendCriticalAlert(RawArticle $article): void
    {
        if ($article->score >= 0.9) {
            Http::post(config('services.discord.webhook_url'), [
                'content' => "🚨 **Noticia de Alta Relevancia Detectada (Score: {$article->score})**",
                'embeds' => [[
                    'title' => $article->title,
                    'description' => "Se ha procesado y programado la redacción automática.",
                    'color' => 15158332,
                    'url' => $article->url
                ]]
            ]);
        }
    }
}
```

---

### 🔑 Keywords de Alto Tráfico a Monitorear
- **Releases y Cambios:** `"nueva versión"`, `"release"`, `"lanzamiento"`, `"actualización"`, `"breaking change"`, `"deprecation"`, `"security update"`, `"cve"`.
- **Específicas:** `"Python 3.13"`, `"Rust 1.80"`, `"Node 22"`, `"Laravel 11"`, `"Claude 3.5"`, `"GPT-5"`.

---

### 🎯 Criterios de Selección "Noticia Pionera"
1. **Filtro Temporal:** Publicado hace menos de 2 horas en la fuente de origen.
2. **Urgencia SEO:** Aún sin traducción o cobertura amplia en español (búsqueda rápida mediante APIs de contraste).
3. **Puntajes Mínimos:** Hacker News > 40 puntos, Reddit > 80 upvotes.

---

### ⚠️ Filtro de Idioma y Mercado (Traducciones Evitando Penalización)
1. **Verificar Cobertura:** Solo traducir y generar el artículo si en las primeras dos páginas de Google Search en español no existe cobertura de la noticia (evita pelear por keywords ya saturadas en español).
2. **Potencial Viral:** Validar si el tema cuenta con engagement inicial en las comunidades fuentes en inglés para justificar el costo de traducción y redacción.

---

### 📊 Monitoreo de Métricas de Éxito (KPIs & Lead Time)
Para evaluar la ventaja competitiva, medir periódicamente el *Lead Time* de publicación frente a los competidores del sector:
```php
class NewsPerformanceMetrics
{
    public function calculateLeadTimeHours(Article $article): ?float
    {
        // Buscar el artículo de la competencia más cercano
        $competitor = CompetitorArticle::where('keywords', 'LIKE', "%{$article->primary_keyword}%")
            ->where('published_at', '>', $article->published_at)
            ->orderBy('published_at')
            ->first();
            
        if ($competitor) {
            // Diferencia en horas. Un número positivo indica que publicamos ANTES
            return $competitor->published_at->diffInHours($article->published_at);
        }
        return null;
    }
}
```

---

### 💰 Análisis de Costos Mensuales Estimados y ROI

| Concepto | Tier Startup (Básico) | Tier Profesional | Tier Enterprise |
|---|---|---|---|
| **APIs Ingesta** | $0 (Free Tiers) | $138/mes (NewsData Pro + APITube) | $500+/mes (NewsAPI.ai + Twitter API) |
| **Modelos AI Ingesta** | $10/mes | $80/mes | $300+/mes |
| **Infraestructura** | $15/mes (Hetzner/DigitalOcean) | $80/mes (VPS + Managed Redis) | $300/mes (Clúster escalado) |
| **Total Estimado** | **~$25/mes** | **~$298/mes** | **~$1,100+/mes** |
| **Capacidad Publicación**| ~30-50 artículos/día | ~200-500 artículos/día | ~1,500+ artículos/día |

*ROI Esperado:* Con un CPM promedio de $2-$5 mediante AdSense / redes publicitarias, a partir de **100,000 visitas mensuales** en el nicho tech (alcanzable en 6 meses con indexación rápida), el retorno cubre los costos del Tier Profesional.

---

## 14. Resiliencia y Circuit Breakers

Si una API externa (por ejemplo, GitHub API o NewsData) experimenta caídas, el sistema debe abrir el circuito y desviar el flujo a métodos alternativos locales o RSS estáticos sin colapsar las colas de Horizon.

```php
class ResilientApiFetcher
{
    public function fetchWithFallback(string $source, callable $primary, callable $fallback): array
    {
        $cacheKey = "api_circuit_{$source}";
        
        // Si el circuito está abierto (muchos fallos recientes), usar fallback
        if (Cache::get($cacheKey . '_open', false)) {
            Log::warning("Circuit open for {$source}, using fallback");
            return $fallback();
        }
        
        try {
            $result = $primary();
            
            // Resetear contador de fallos en caso de éxito
            Cache::forget($cacheKey . '_failures');
            Cache::forget($cacheKey . '_open');
            
            return $result;
        } catch (\Exception $e) {
            $failures = Cache::increment($cacheKey . '_failures', 1);
            
            // Abrir circuito después de 5 fallos consecutivos
            if ($failures >= 5) {
                Cache::put($cacheKey . '_open', true, now()->addMinutes(15));
                Log::error("Circuit opened for {$source} after {$failures} failures");
            }
            
            return $fallback();
        }
    }
}

// Uso en un Job de Laravel
class FetchGitHubReleasesJob implements ShouldQueue
{
    public function handle(ResilientApiFetcher $fetcher): void
    {
        $releases = $fetcher->fetchWithFallback(
            'github',
            fn() => $this->fetchFromGitHubAPI(),
            fn() => $this->fetchFromGitHubRSS() // Fallback a feeds RSS Atom sin auth
        );
        
        // Procesar releases...
    }
}
```

---

## 15. Rate Limiting Distribuido

Para prevenir bloqueos de IPs o superación de cuotas de API keys compartidas entre múltiples workers concurrentes de Horizon:

```php
class ApiRateLimiter
{
    private const LIMITS = [
        'github' => ['requests' => 4500, 'window' => 3600], // Margen sobre los 5,000 límites
        'newsdata' => ['requests' => 900, 'window' => 86400], // Margen sobre 1,000 créditos
        'hackernews' => ['requests' => 10000, 'window' => 3600],
    ];
    
    public function canMakeRequest(string $api): bool
    {
        $key = "rate_limit:{$api}:" . date('Y-m-d-H');
        $current = Redis::get($key) ?? 0;
        
        return $current < self::LIMITS[$api]['requests'];
    }
    
    public function incrementUsage(string $api): void
    {
        $key = "rate_limit:{$api}:" . date('Y-m-d-H');
        Redis::incr($key);
        Redis::expire($key, self::LIMITS[$api]['window']);
    }
    
    public function getRemainingRequests(string $api): int
    {
        $key = "rate_limit:{$api}:" . date('Y-m-d-H');
        $current = Redis::get($key) ?? 0;
        return max(0, self::LIMITS[$api]['requests'] - $current);
    }
}
```

---

## 16. Validación de Calidad del Contenido

Pipeline de comprobación programática para verificar si el contenido autogenerado por la IA cumple con los requisitos técnicos de maquetación, SEO y longitud antes de marcar el artículo como listo para publicar:

```php
class ContentQualityValidator
{
    public function validate(Article $article): ValidationResult
    {
        $issues = [];
        $score = 100;
        
        // 1. Longitud mínima (SEO standard)
        $wordCount = str_word_count(strip_tags($article->content));
        if ($wordCount < 300) {
            $issues[] = "Contenido muy corto: {$wordCount} palabras";
            $score -= 30;
        }
        
        // 2. Densidad de keywords (1% - 3%)
        $keywordDensity = $this->calculateKeywordDensity($article);
        if ($keywordDensity < 0.01 || $keywordDensity > 0.03) {
            $issues[] = "Densidad de keywords fuera de rango: " . ($keywordDensity * 100) . "%";
            $score -= 15;
        }
        
        // 3. Estructura HTML de títulos (H2/H3)
        if (!preg_match('/<h[23]>/', $article->content)) {
            $issues[] = "Falta estructura de encabezados (H2/H3)";
            $score -= 20;
        }
        
        // 4. Inclusión de imágenes
        if (!preg_match('/<img[^>]+>/', $article->content)) {
            $issues[] = "No contiene imágenes en el cuerpo";
            $score -= 10;
        }
        
        // 5. Longitud meta description (SEO)
        $metaLen = strlen($article->meta_description);
        if ($metaLen < 120 || $metaLen > 160) {
            $issues[] = "Meta description fuera del rango recomendado (120-160 caracteres)";
            $score -= 10;
        }
        
        // 6. Detección de duplicación interna
        $duplicateScore = $this->checkDuplicateContent($article);
        if ($duplicateScore > 0.7) {
            $issues[] = "Contenido duplicado internamente (Similitud: " . ($duplicateScore * 100) . "%)";
            $score -= 40;
        }
        
        return new ValidationResult(
            passed: $score >= 70,
            score: $score,
            issues: $issues
        );
    }
    
    private function checkDuplicateContent(Article $article): float
    {
        $recentArticles = Article::where('published_at', '>=', now()->subDays(7))
            ->where('id', '!=', $article->id)
            ->pluck('content');
        
        $maxSimilarity = 0;
        foreach ($recentArticles as $content) {
            similar_text($article->content, $content, $percent);
            $maxSimilarity = max($maxSimilarity, $percent / 100);
        }
        
        return $maxSimilarity;
    }
}
```

---

## 17. Seguridad y Sanitización

### 🔒 Sanitización de Ingesta (Anti-XSS)
Evitar inyección de scripts al almacenar información externa estructurada:
```php
// En tu modelo de base de datos
public function setContentAttribute($value): void
{
    // Permitir solo etiquetas HTML seguras necesarias para formato
    $allowedTags = '<p><h1><h2><h3><h4><h5><h6><ul><ol><li><strong><em><a><img><code><pre><blockquote>';
    $this->attributes['content'] = strip_tags($value, $allowedTags);
}
```

### 🔒 Firma de Seguridad en Webhooks
Middleware para validar la firma secreta de webhooks entrantes de GitHub:
```php
class VerifyGitHubWebhook
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, config('services.github.webhook_secret'));
        
        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Firma de webhook de GitHub no válida');
        }
        
        return $next($request);
    }
}
```

---

## 18. Plan de Implementación y Comandos de Monitoreo

### 🚀 Plan de Implementación por Fases

#### **Fase 1: MVP Funcional (Semanas 1-2)**
```bash
# 1. Crear estructura base de base de datos
php artisan make:model RawArticle -m
php artisan make:model Article -m
php artisan make:model Category -m

# 2. Crear los Jobs de ingesta básica
php artisan make:job FetchGitHubReleasesJob
php artisan make:job FetchHackerNewsJob
php artisan make:job FetchRssFeedsJob

# 3. Crear Job de procesamiento con IA
php artisan make:job ProcessArticleWithAIJob

# 4. Servicios básicos de Ingesta y Tradución
php artisan make:service NewsScoringService
php artisan make:service MultilingualPublishingService

# 5. Instalar dependencias requeridas
composer require guzzlehttp/guzzle
composer require predis/predis
composer require laravel/horizon
php artisan horizon:install
```

#### **Fase 2: Resiliencia y Control de Calidad (Semanas 3-4)**
- Creación de servicios de resiliencia y Circuit Breaker (`ResilientApiFetcher`).
- Rate limiter centralizado distribuido en Redis (`ApiRateLimiter`).
- Pipeline de validación de calidad pre-publicación (`ContentQualityValidator`).
- Sistema de alertas externas (`AlertService`).

#### **Fase 3: Escalabilidad y Machine Learning (Mes 2)**
- Configuración de balanceo dinámico de colas en `config/horizon.php` para escalar workers de ingesta y generación AI:
```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default', 'news-ingestion', 'article-processing'],
            'balance' => 'auto',
            'minProcesses' => 2,
            'maxProcesses' => 10,
            'tries' => 3,
        ],
    ],
],
```

---

### 📊 Comandos de Consola (Monitoreo y Debug)

```bash
# Ver el estado y balanceo de las colas de Horizon
php artisan horizon:status

# Obtener métricas de ingesta y tiempos de lead time de la última jornada
php artisan news:metrics --realtime

# Forzar reprocesamiento manual de artículos fallidos del último día
php artisan news:retry-failed --hours=24

# Tarea de limpieza programada para eliminar RawArticles obsoletos
php artisan news:cleanup --days=30

# Ver artículos retenidos por no pasar las validaciones de calidad
php artisan news:review-queue

# Probar la puntuación (Scoring) de un artículo específico en base de datos
php artisan news:test-scoring --article-id=123
```

---

### 📝 Notas de Implementación Críticas

- **Rate Limits:**
  - GitHub REST API: 5,000 requests/hora con token de autenticación.
  - Hacker News API: Sin límites estrictos, pero se sugiere almacenar respuestas en cache mediante Redis.
  - NewsData.io: 1,000 llamadas diarias en el tier gratuito.
- **Deduplicación:** Implementar hashing único de las URLs de origen en la tabla `raw_articles` para evitar reprocesar la misma noticia.
- **Atribución Legal:** Generar backlinks estructurados hacia las fuentes de origen (GitHub commits, hilos de Hacker News o posts originales), garantizando transparencia y mejorando señales E-E-A-T ante el buscador de Google.

---

*Última actualización: 2026-06-04*
*Próximas fuentes a evaluar: Lobsters, Slashdot, Echo JS, Changelog.com*
