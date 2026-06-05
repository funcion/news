# 📋 Checklist de Integración de Fuentes v4.0 (Hub & Spoke)

Este documento sirve como registro interactivo y plan de acción para integrar la estrategia de ingesta "Hub & Spoke" en el pipeline de Laravel.

---

## 🛠️ Plan de Acción Principal

- [ ] **Paso 1: Sincronizador Automático de Markdown**
  - [ ] Crear el comando `php artisan sources:sync-markdown` que lea este archivo y cargue/sincronice los *Spokes* (fuentes directas) marcados en la base de datos automáticamente.
- [ ] **Paso 2: Ingesta de Super-Agregadores (Hubs)**
  - [ ] Implementar `FetchHackerNewsJob` (Firebase & Algolia API) con lógica de tracción temprana (`score > 15` en < 30 mins).
  - [ ] Implementar `FetchRedditJob` para subreddits técnicos clave usando cabeceras `User-Agent`.
  - [ ] Implementar `FetchGitHubTrendingJob` usando la API oficial de búsqueda con fechas relativas.
- [ ] **Paso 3: Optimización de Frecuencias (Tiers)**
  - [ ] Configurar frecuencias de monitoreo agresivas (5-15 mins) para los Hubs.
  - [ ] Configurar frecuencias moderadas (1-4 horas) para las fuentes directas de software/CVEs.
- [ ] **Paso 4: Procesamiento de Contenido Completo**
  - [ ] Habilitar llamada automática a `ScraperService` (Jina Reader) cuando el contenido del feed RSS sea un fragmento o esté recortado.

---

# 🔗 Registro de Fuentes (Checklist de Integración)

## 📡 1. Super-Agregadores (Hubs de Detección)
*Los hubs capturan noticias generales y derivan las URLs al Scraper central.*

- [ ] **Hacker News API** (Top Stories & New Stories)
- [ ] **Hacker News Algolia** (Búsquedas temáticas por API)
- [ ] **Reddit JSON Endpoints** (programming, technology, webdev, MachineLearning, netsec)
- [ ] **GitHub Search API** (Tendencias globales y búsquedas por topic/stars)
- [ ] **DEV.to API** (Artículos técnicos populares y fresh)

---

## 🔑 2. News APIs Comerciales (Hubs con API Key)
*Agregadores de prensa tradicionales. Límite de cuota gratuito diario.*

- [ ] **NewsData.io** (API Key: `pub_36c901cfc123441696891d12f699f55d`)
- [ ] **NewsAPI.org** (API Key: `1c6f27aa78884425859a0187f84076b0`)
- [ ] **APITube.io** (Pendiente configurar Key)

---

## 📦 3. Fuentes Directas (Spokes - Releases y Software)
*Monitoreo directo del código fuente y paquetes. Prioridad máxima.*

### 🔵 GitHub Releases (Atom Feeds)
- [x] `https://github.com/php/php-src/releases.atom` (PHP)
- [x] `https://github.com/python/cpython/releases.atom` (Python)
- [ ] `https://github.com/golang/go/releases.atom` (Go)
- [x] `https://github.com/rust-lang/rust/releases.atom` (Rust)
- [x] `https://github.com/nodejs/node/releases.atom` (Node.js)
- [ ] `https://github.com/laravel/laravel/releases.atom` (Laravel Skeleton)
- [x] `https://github.com/laravel/framework/releases.atom` (Laravel Core)
- [x] `https://github.com/facebook/react/releases.atom` (React)
- [x] `https://github.com/vuejs/core/releases.atom` (Vue)
- [ ] `https://github.com/angular/angular/releases.atom` (Angular)
- [ ] `https://github.com/django/django/releases.atom` (Django)
- [ ] `https://github.com/symfony/symfony/releases.atom` (Symfony)
- [x] `https://github.com/vercel/next.js/releases.atom` (Next.js)
- [ ] `https://github.com/nuxt/nuxt/releases.atom` (Nuxt)
- [ ] `https://github.com/sveltejs/svelte/releases.atom` (Svelte)
- [x] `https://github.com/tailwindlabs/tailwindcss/releases.atom` (Tailwind CSS)
- [ ] `https://github.com/filamentphp/filament/releases.atom` (Filament)
- [ ] `https://github.com/livewire/livewire/releases.atom` (Livewire)
- [ ] `https://github.com/inertiajs/inertia/releases.atom` (Inertia)
- [ ] `https://github.com/oven-sh/bun/releases.atom` (Bun Runtime)
- [ ] `https://github.com/denoland/deno/releases.atom` (Deno Runtime)
- [x] `https://github.com/docker/engine/releases.atom` (Docker)
- [ ] `https://github.com/kubernetes/kubernetes/releases.atom` (Kubernetes)
- [ ] `https://github.com/tensorflow/tensorflow/releases.atom` (TensorFlow)
- [ ] `https://github.com/pytorch/pytorch/releases.atom` (PyTorch)
- [ ] `https://github.com/microsoft/vscode/releases.atom` (VS Code)

### 🔵 Package Registries (Novedades de Paquetes)
- [ ] `https://www.npmjs.com/feed` (NPM Updates)
- [ ] `https://pypi.org/rss/updates.xml` (PyPI Updates)
- [ ] `https://pypi.org/rss/projects.xml` (Nuevos Proyectos PyPI)
- [ ] `https://crates.io/api/v1/crates?page=1&per_page=50&sort=recent-updates` (Rust Crates)
- [ ] `https://rubygems.org/api/v1/activity/just_updated.json` (RubyGems Updates)

---

## 📰 4. Fuentes Directas (Spokes - Blogs y Medios Especializados)

### 🔵 Blogs Oficiales (RSS)
- [ ] `https://blog.python.org/feeds/posts/default` (Python Blog)
- [ ] `https://planetpython.org/rss20.xml` (Planet Python)
- [ ] `https://go.dev/blog/feed.xml` (Go Blog)
- [ ] `https://blog.rust-lang.org/feed.xml` (Rust Blog)
- [ ] `https://this-week-in-rust.org/rss.xml` (This Week in Rust)
- [ ] `https://nodejs.org/en/feed/blog.xml` (Node.js Blog)
- [ ] `https://nodejs.org/en/feed/release.xml` (Node.js Releases Blog)
- [x] `https://laravel-news.com/feed` (Laravel News)
- [ ] `https://blog.angular.io/feed` (Angular Blog)
- [ ] `https://svelte.dev/blog/rss.xml` (Svelte Blog)
- [ ] `https://devblogs.microsoft.com/typescript/feed/` (TypeScript Blog)
- [ ] `https://blogs.oracle.com/java/feed` (Java Blog)
- [ ] `https://inside.java/feed.xml` (Inside Java)
- [ ] `https://www.ruby-lang.org/en/feeds/news.rss` (Ruby Blog)
- [ ] `https://rubyonrails.org/feed.xml` (Ruby on Rails Blog)
- [ ] `https://blog.jetbrains.com/kotlin/feed/` (Kotlin Blog)
- [ ] `https://devblogs.microsoft.com/dotnet/feed/` (.NET Blog)
- [ ] `https://learn.microsoft.com/en-us/dotnet/core/compatibility/feed.xml` (.NET Compatibility)
- [ ] `https://laravel.com/docs/master/releases.atom` (Documentación Laravel)

### 🔵 Inteligencia Artificial (Research y API)
- [ ] `https://rss.arxiv.org/rss/cs.AI` (Papers ArXiv AI)
- [ ] `https://rss.arxiv.org/rss/cs.LG` (Papers ArXiv ML)
- [ ] `https://paperswithcode.com/latest.rss` (Papers with Code)
- [x] `https://huggingface.co/papers.rss` (Hugging Face Papers)
- [x] `https://openai.com/blog/rss.xml` (OpenAI Blog)
- [x] `https://research.google/blog/feed/` (Google Research)
- [ ] `https://deepmind.com/blog/feed/basic/` (Google DeepMind)
- [x] `https://ai.meta.com/blog/rss/` (Meta AI)
- [ ] `https://huggingface.co/api/models?sort=downloads&direction=-1&limit=20` (Modelos HF populares)
### 🔵 SEO & Marketing (Posicionamiento y Tráfico)
- [x] `https://searchengineland.com/feed` (Search Engine Land)
- [x] `https://www.searchenginejournal.com/feed/` (Search Engine Journal)
- [x] `https://ahrefs.com/blog/feed/` (Ahrefs Blog)

### 🔵 Ciberseguridad (CVEs y Blogs)
- [x] `https://www.cvedetails.com/rss/last.xml` (CVE Details)
- [ ] `https://nvd.nist.gov/feeds/json/cve/1.1/nvdcve-1.1-recent.json.gz` (NVD Recent Gzipped)
- [x] `https://www.bleepingcomputer.com/feed/` (Bleeping Computer)
- [x] `https://krebsonsecurity.com/feed/` (Krebs on Security)
- [ ] `https://feeds.feedburner.com/TheHackersNews` (The Hacker News)
- [ ] `https://nakedsecurity.sophos.com/feed/` (Naked Security)
- [ ] `https://www.schneier.com/feed/` (Schneier on Security)

### 🔵 Medios de Prensa Premium (RSS ya sembrados o por integrar)
- [x] `https://techcrunch.com/category/artificial-intelligence/feed/` (TechCrunch AI - Sembrado)
- [x] `https://openai.com/blog/feed.xml` (OpenAI Feed - Sembrado)
- [x] `https://www.theverge.com/ai-artificial-intelligence/rss/index.xml` (The Verge AI - Sembrado)
- [x] `https://www.technologyreview.com/topic/artificial-intelligence/feed/` (MIT Review AI - Sembrado)
- [x] `https://techcrunch.com/feed/` (TechCrunch General)
- [x] `https://techcrunch.com/category/startups/feed/` (TechCrunch Startups)
- [x] `https://techcrunch.com/category/security/feed/` (TechCrunch Security)
- [x] `https://www.theverge.com/rss/index.xml` (The Verge General)
- [x] `https://www.wired.com/feed/rss` (Wired General)
- [x] `https://feeds.arstechnica.com/arstechnica/index` (Ars Technica)
- [x] `https://venturebeat.com/feed/` (VentureBeat)
- [x] `https://www.theregister.com/headlines.atom` (The Register)
- [x] `https://www.zdnet.com/topic/developer/rss.xml` (ZDNet Dev)
- [x] `https://www.infoworld.com/feed` (InfoWorld)
- [x] `https://hackernoon.com/feed` (Hacker Noon)
- [x] `https://www.smashingmagazine.com/feed/` (Smashing Magazine)
- [x] `https://css-tricks.com/feed/` (CSS Tricks)
- [x] `https://alistapart.com/main/feed/` (A List Apart)
