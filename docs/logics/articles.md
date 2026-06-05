# Referencia Técnica: Lógica de Negocio de Artículos (Articles)

Este documento detalla todas las reglas lógicas que rigen el procesamiento, la programación, la publicación y la eliminación de los artículos terminados en el portal. Corresponde al módulo visual de **Artículos** en el panel administrativo (`/admin/articles`).

---

## 1. Límites Dinámicos de Publicación (Rate Limiting)

### 1.1 Regla de Negocio
Para evitar que los buscadores web (como Googlebot) detecten un patrón de publicación robótico o artificial, el sistema distribuye dinámicamente las noticias usando límites variables configurados en el panel de Filament (`/admin/settings-page`):
* **Límite Diario:** Rango de artículos permitidos por día (ej. `7,20`).
* **Límite Horario:** Rango por hora para evitar ráfagas (ej. `2,7`).
* **Límite por Categoría:** Rango diario por categoría para evitar monopolio temático (ej. `1,5`).

### 1.2 Implementación de Semilla (mt_srand)
Para mantener la coherencia matemática a lo largo de cada hora o día, los límites aleatorios se calculan inicializando el generador con semillas basadas en marcas de tiempo:
```php
// Semilla Diaria (único para todo el día)
mt_srand((int) date('Ymd'));
$maxPerDay = mt_rand($dayMin, $dayMax);

// Semilla Horaria (cambia cada hora en punto)
mt_srand((int) date('YmdH'));
$maxPerHour = mt_rand($hourMin, $hourMax);

// Semilla de Categoría (basada en el día + ID de categoría)
mt_srand((int) date('Ymd') + (int) $article->category_id);
$catLimit = mt_rand($catMin, $catMax);

mt_srand(); // Reiniciar semilla siempre
```

---

## 2. Jitter Temporal de Publicación

### 2.1 Regla de Negocio
Al crear un artículo regular, el sistema no lo publica inmediatamente. En su lugar, calcula un tiempo de retraso aleatorio (Jitter) de entre **5 y 60 minutos** y guarda el artículo con estatus `draft` (borrador) con una fecha de publicación futura (`published_at`).

### 2.2 Liberación por Scheduler
El comando programador `PublishRateLimitedDrafts` corre cada minuto y libera gradualmente los borradores que ya han cumplido su periodo de jitter:
```php
$drafts = Article::where('status', 'draft')
    ->whereNotNull('published_at')
    ->where('published_at', '<=', now()) // ¿Pasó el Jitter?
    ->limit($slotsAvailable)
    ->get();
```

---

## 3. Bypass de Prioridad Absoluta (Breaking News & Forced)

Existen tres escenarios donde se omiten los límites diarios, horarios y el Jitter temporal, publicando la noticia de forma **inmediata** (`status = 'published'`, `published_at = now()`):

1. **Relevancia Crítica por IA:** Si la clasificación de la IA califica el artículo con una importancia **$\ge 9$**.
2. **Releases de Frameworks (Atom feeds):** Si la fuente original es un feed de tipo `atom` (repositorios de GitHub, parches de Docker, etc.).
3. **Forzar Publicación Manual (Panel Admin):** Si el administrador presiona el botón **"Forzar Re-procesamiento"** en Filament. Este botón despacha el Job con la bandera `forceImmediate = true`.

---

## 4. Almacenamiento en Cloudflare R2 y Purga de Caché CDN

### 4.1 Exclusividad de Cloudflare R2 (Sin Fallback)
El sistema está configurado para usar exclusivamente **Cloudflare R2** como el disco de medios (`MEDIA_DISK=r2`). 
* El fallback automático al disco local `public` ha sido desactivado permanentemente en `config/media-library.php` para asegurar que todo el contenido (imágenes originales y conversiones de tamaño) se suba a la nube.
* Tanto las imágenes principales como sus tres conversiones asociadas (`thumb`, `medium`, `large`) se guardan directamente en R2 con el campo `disk` y `conversions_disk` configurados en `'r2'`.
* El servicio de imágenes se realiza 100% mediante el CDN de Cloudflare usando el dominio personalizado `https://media.glodaxia.com`.

### 4.2 Borrado Físico de Archivos
Cuando un artículo se elimina de la base de datos, el evento `deleted` de Eloquent gatilla la limpieza física de sus fotos directamente en Cloudflare R2:
```php
$article->clearMediaCollection('images_en');
$article->clearMediaCollection('images_es');
$article->clearMediaCollection('default');
```
Esto envía peticiones S3 `DELETE` al endpoint de Cloudflare R2 para borrar el archivo original y todas sus miniaturas generadas (`thumb`, `medium`, `large`).

### 4.3 Purga de Caché de Cloudflare (CDN)
Inmediatamente después del borrado físico, se despacha el Job `PurgeR2CacheJob` pasándole las URLs de las fotos. Este Job envía un POST asíncrono a la API de Cloudflare para limpiar los servidores perimetrales (edge servers) en segundos:
```php
$response = Http::withHeaders([
    'Authorization' => "Bearer " . env('CLOUDFLARE_API_TOKEN'),
])->post("https://api.cloudflare.com/client/v4/zones/" . env('CLOUDFLARE_ZONE_ID') . "/purge_cache", [
    'files' => $urlsToPurge,
]);
```
