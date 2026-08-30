# 📘 Arquitectura Editorial, Pipeline de IA y Panel de Administración (Glodaxia)

Este documento detalla la arquitectura técnica, las decisiones de diseño, las reglas de negocio y los flujos asíncronos implementados para la gestión editorial, redacción con IA y control administrativo en **Glodaxia**.

---

## 1. Diagnóstico y Causa Raíz (Bug de Truncamiento de Títulos)

### Problema Identificado
En entornos de desarrollo y producción (VPS Coolify / Contabo), varias noticias presentaban titulares entrecortados a mitad de una frase (ejemplo: *"OpenAI lanza nuevo modelo que es de"*).

### Causa Raíz
1. **Recorte Destructivo Hacia Atrás (`autoFixRedactedOutput`)**: En `app/Jobs/ProcessArticleWithAIJob.php`, cuando un título superaba los 120 caracteres, el código aplicaba un retroceso hacia el espacio anterior si estaba por encima de los 80 caracteres. Esto eliminaba complementos circunstanciales y oraciones enteras.
2. **Límites Hardcodeados Dispersos**: Componentes Blade como `article-card.blade.php` tenían valores fijos como `maxTitleLength => 100` con llamadas `substr()` destructivas.
3. **Ausencia de un Single Source of Truth**: Los límites de caracteres no estaban centralizados, lo que provocaba discrepancias entre la IA, las validaciones de guardado y las vistas.

---

## 2. Single Source of Truth: Límites Centralizados

Todos los límites del sistema están centralizados en [`config/global.php`](file:///home/luisf/news/config/global.php). Ningún archivo o componente de la aplicación puede definir límites de longitud de forma hardcodeada.

### Configuración Editorial (`config/global.php`)

```php
'limits' => [
    // Longitud del Titular H1 (Web pública y SEO)
    'title' => [
        'min'       => 50,  // Mínimo de caracteres para evitar títulos vacíos o telegráficos
        'max'       => 130, // Máximo estricto de caracteres en BD y vistas
        'min_words' => 7,   // Mínimo de palabras (sujeto + verbo + impacto)
    ],

    // Extracto / Lead del artículo
    'excerpt' => [
        'min' => 160,
        'max' => 250,
    ],

    // Snippet de Google Search (Meta Title)
    'meta_title' => [
        'min' => 50,
        'max' => 80,  // Optimizado para no ser cortado por Google SERP (~600px)
    ],

    // Meta Description para buscadores
    'meta_description' => [
        'min' => 120,
        'max' => 160,
    ],

    // Accesibilidad y Google Imágenes
    'image_alt' => [
        'max' => 125, // Estándar de lectores de pantalla
    ],
    'image_title' => [
        'max' => 70,  // Pie editorial de imagen
    ],

    // Visualización en Tarjetas (Portada, Búsquedas y Tags)
    'card' => [
        'title_max_words' => 15,  // Corte con "..." en tarjetas de noticias secundarias
        'title_max'       => 130,
        'excerpt_max'     => 160,
    ],

    // Longitud de contenido por tipo de artículo
    'min_words' => [
        'news'   => 800,  // Mínimo estricto de palabras para noticias
        'blog'   => 900,
        'guide'  => 1200,
        'review' => 900,
        'pillar' => 1600,
    ],
],
```

---

## 3. Configuración del Pool de Inteligencia Artificial (`config/ai_models.php`)

El enrutamiento de modelos y límites de tokens está estructurado directamente en código PHP limpio:

* **Modelo Predeterminado**: `deepseek/deepseek-v4-flash`
* **Cadena de Failover (Pool)**:
  1. `deepseek/deepseek-v4-flash` (Prioridad #1: Máxima calidad analítica y redacción bilingüe)
  2. `qwen/qwen3.7-flash` (Prioridad #2: Velocidad y formato JSON estricto)
  3. `deepseek/deepseek-v3.2`
  4. `qwen/qwen3.8-flash`
  5. `meta-llama/llama-4-scout`
  6. `meta-llama/llama-4-maverick`
  7. `bytedance-seed/seed-1.6`
* **Límites de Tokens**:
  * `max_tokens`: 10,000
  * `classification_max_tokens`: 1,500
  * `tag_max_tokens`: 500

---

## 4. Limpieza del Entorno (`.env` y `.env.example`)

Para mantener el principio de **Single Source of Truth** y evitar configuraciones duplicadas:
* Se eliminaron del `.env` las variables de pool de IA y límites de caracteres.
* El `.env` se mantiene exclusivo para:
  * Entorno y URLs (`APP_ENV`, `APP_URL`, `ASSET_URL`).
  * Conexiones de infraestructura (PostgreSQL, Redis, Mailpit).
  * Storage de medios (Cloudflare R2, Cloudflare Purge).
  * Credenciales secretas (`OPENROUTER_API_KEY`, `SILICONFLOW_API_KEY`, `JINA_API_KEY`, `INDEXNOW_KEY`, `SUPER_ADMIN_EMAILS`).

---

## 5. Pipeline de Redacción e IA (`ProcessArticleWithAIJob.php`)

### A. Validación Estricta de Integridad de Idioma (Zero Tolerance)
Para evitar que un modelo de IA entregue contenido en español dentro de los campos en inglés (`content_en`, `title_en`, `excerpt_en`) o viceversa:
1. **Regla Absoluta en el Prompt**: Sección `0. ABSOLUTE LANGUAGE SEPARATION RULE` que prohíbe explícitamente el cruce de idiomas.
2. **Clasificador Determinístico de Stopwords (`validateLanguageIntegrity`)**:
   - Analiza la densidad de palabras de función exclusivas del inglés y del español.
   - Si `content_en` contiene stopwords en español (o `content_es` en inglés), la respuesta es **rechazada automáticamente como error fatal**.
   - El pipeline descarta el intento y pasa inmediatamente al siguiente modelo del pool de IA (`failover chain`) hasta obtener redacción 100% nativa y pura en cada idioma.
3. **Validación en Titulares y Extractos**: Detección de conectores y marcadores lingüísticos en `title_en` / `title_es`.

### B. Mecanismo de Reprocesamiento Forzado (`forceReprocess`)
Cuando el usuario solicita **"Reprocesar Noticia Completa"** desde el panel:
- Se despacha `ProcessArticleWithAIJob($rawArticle, forceReprocess: true)`.
- **Bypass del Detector de Duplicados**: El verificador de duplicados se omite intencionalmente para no abortar el trabajo, permitiendo regenerar el 100% del contenido y las traducciones.
- **Preservación de Estado**: Si la noticia ya estaba publicada (`published`), mantiene su estado y su fecha original de publicación sin enviarse a borrador.
- **Validación Lingüística Activa**: El nuevo clasificador de stopwords garantiza que `content_en` sea 100% inglés y `content_es` sea 100% español sin importar el idioma de la fuente original.

### C. Inyección Dinámica y Target Calibrado

### Inyección Dinámica y Target Calibrado
El prompt del redactor de IA consume dinámicamente los valores de `config/global.php`:
* **Calibración de Longitud**: Se instruye a la IA a apuntar a un rango de **70 a 115 caracteres** (máximo absoluto de 130), garantizando que el titular resultante caiga en la zona verde óptima sin excederse.
* **Extensión Mínima de Contenido**: Exige un mínimo de **800 palabras** de redacción bilingüe enriquecida.

### Algoritmo de Saneamiento y Clamping (`autoFixRedactedOutput`)
Si por alguna razón la IA devuelve un titular de longitud superior a 130 caracteres:
1. Se limpia el texto de etiquetas HTML y comillas envolventes (`«`, `“`, `"`).
2. Se acota a 130 caracteres **en el límite de la última palabra completa**.
3. Se eliminan conectores o preposiciones colgantes al final de la frase (*de, del, con, para, por, en, a, y, e, o, u, que, sobre, tras, etc.*).
4. Se eliminan signos de puntuación residuales (*, ; : - – — / \*).

---

## 6. Lógica de Presentación en Vistas Blade

| Vista / Componente | Regla de Visualización | Implementación |
| :--- | :--- | :--- |
| **Noticia Destacada (Hero en `home.blade.php`)** | Titular **100% íntegro** sin ningún recorte. | `{{ $featured->title }}` |
| **Grid Secundario (`home.blade.php`)** | Truncado a **15 palabras** con puntos suspensivos. | `Str::words($article->title, config('global.editorial.limits.card.title_max_words', 15), '...')` |
| **Búsqueda (`search.blade.php`)** | Truncado a **15 palabras**. | `Str::words($articleTitle, config('global.editorial.limits.card.title_max_words', 15), '...')` |
| **Etiquetas (`tag/show.blade.php`)** | Truncado a **15 palabras**. | `Str::words($article->title, config('global.editorial.limits.card.title_max_words', 15), '...')` |
| **Componente (`article-card.blade.php`)** | Lee `card.title_max_words` de configuración. | `Str::words($rawTitle, $config['titleMaxWords'], '...')` |

---

## 7. Panel de Administración Filament (`ArticleResource.php`)

### A. Consolidación de Columnas (6 Columnas Balanceadas)
1. **Portada**: Miniatura WebP con bordes redondeados (`rounded-lg`).
2. **Titular Enriquecido**:
   * Titular en tipografía destacada.
   * **Badge dinámico de estado**:
     * 🟢 `85 caracteres • 12 palabras (Óptimo)` *(entre 50 y 130 caracteres)*
     * 🟡 `42 caracteres • 5 palabras (Corto / Incompleto)` *(< 50 chars o < 7 palabras)*
     * 🔴 `145 caracteres (Excedido)` *(> 130 chars)*
     * ⏳ `<span class="animate-pulse">Regenerando titular con IA...</span>` *(En procesamiento)*
   * **Sublínea de metadatos en tono mate `#909090`**: `👤 Autor • 📁 Categoría`.
3. **Modelo IA**: Badge con el color del modelo registrado (`DeepSeek V4 Flash`, `Qwen 3.7`, etc.).
4. **Estado**: Badge con estado del artículo (*Publicado*, *Borrador*, *Programado*, *En Revisión*, *Rechazado*).
5. **Fecha**: Formato `d/m/Y H:i`.
6. **Vistas**: Contador numérico.
7. **Acciones**: Botonera de acceso rápido.

### B. Acciones Rápidas por Fila (Botonera `[ 🪄 ] [ 🖼️ ] [ 🔄 ] [ ⋯ ]`)
Las 3 acciones rápidas cuentan con **estados de carga persistentes (`animate-spin`)** en segundo plano:

* **`[ 🪄 ]` Regenerar Título IA (1 Clic Asíncrono)**:
  * Despacha `RegenerateArticleTitleJob` en **5 milisegundos**.
  * Activa la clave de Redis `article_title_processing_{id}`.
  * Cambia al instante a un **icono de carga giratorio ámbar `[ 🔄 ]` (`animate-spin`)**.
  * El titular muestra el pulso: `⏳ Regenerando titular con IA en segundo plano...`
* **`[ 🖼️ ]` Regenerar Portada IA (FLUX.1)**:
  * Abre modal para personalizar el prompt visual y despacha en segundo plano `RegenerateArticleHeroImageJob`.
  * Activa la clave de Redis `article_image_processing_{id}`.
  * Cambia al instante a un **icono de carga giratorio ámbar `[ 🔄 ]` (`animate-spin`)**.
  * El titular muestra el pulso: `⏳ Generando nueva portada con IA (FLUX.1)...`
* **`[ 🔄 ]` Reprocesar Noticia Completa (1 Clic Asíncrono)**:
  * Despacha en segundo plano `ProcessArticleWithAIJob`.
  * Activa la clave de Redis `article_full_reprocessing_{id}`.
  * Cambia al instante a un **icono de carga giratorio ámbar `[ 🔄 ]` (`animate-spin`)**.
  * El titular muestra el pulso: `⏳ Reescribiendo noticia e imágenes completas con IA...`
* **`[ ⋯ ]` Menú Desplegable**: Agrupa acciones administrativas secundarias (*Ver en la web, Cambiar autor, Editar, Publicar ahora, Aprobar, Rechazar, Enviar a revisión*).

### C. Procesamiento Asíncrono y Estado Persistente

```
[Clic en 🪄 o Bulk Action]
       │
       ├─▶ 1. Guarda clave en Redis: Cache::put("article_title_processing_{id}", true, 3 min)
       ├─▶ 2. Despacha Job: RegenerateArticleTitleJob::dispatch($article)
       └─▶ 3. Retorna a Livewire en 5ms (UI desbloqueada + Spinner giratorio activo)
              │
              ▼
    [Worker de Horizon en background]
       ├─▶ Lee contexto real del artículo (excerpt + content)
       ├─▶ Llama a OpenRouter (Prompt calibrado 70-115 chars)
       ├─▶ Aplica saneamiento estricto (sanitizeTitle <= 130 chars)
       ├─▶ Guarda title_es y title_en en Base de Datos
       └─▶ finally: Cache::forget("article_title_processing_{id}")
              │
              ▼
    [Table Polling en Filament: poll('5s')]
       └─▶ Detecta fin del Job, retira spinner, restaura [ 🪄 ] y muestra badge 🟢
```

* **Persistencia**: Si el usuario recarga la página (`F5`), el spinner continúa girando porque el estado se consulta directamente de Redis.
* **Acción Masiva (Bulk Action)**: `🪄 Regenerar Títulos Seleccionados` permite marcar decenas de noticias y procesarlas en paralelo sin bloquear el navegador.

---

## 8. Nuevo Job Creado: `app/Jobs/RegenerateArticleTitleJob.php`

Job encolable dedicado a la reparación y optimización de titulares:
* Implementa `ShouldQueue`, `Queueable`, `SerializesModels`.
* Configurado con 3 reintentos (`$tries = 3`) y timeout de 60 segundos.
* Garantiza la liberación del bloqueo de Redis en su bloque `finally`.

---

## 9. Historial de Commits en Git

```bash
master -> origin/master

24ab724 feat: centralize editorial limits, fix title truncation pipeline, and improve Filament table UI
2bb039c chore: update environment configuration (.env)
0f9f77f tune: adjust editorial limits to 50-130 title chars, 7 min words, and 800 min article words
b91f09f refactor: clean .env/.env.example and establish config files as single source of truth
49363b3 chore: sync SUPER_ADMIN_EMAILS and INDEXNOW_KEY in .env and .env.example
8e4976d feat: add direct 1-click quick action icons (title, cover, reprocess) and non-blocking background queue
106aef3 feat: make bulk title regeneration asynchronous via Queue Jobs without blocking UI
cce0fe7 fix: enforce strict title clamping <= 130 chars and strip trailing connectors
dff3e38 perf: make individual title regeneration asynchronous via Queue Jobs and add table polling
af978e2 feat: add persistent spinning loading indicator for background title generation across page reloads
```