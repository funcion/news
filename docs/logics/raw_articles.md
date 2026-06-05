# Referencia Técnica: Lógica de Procesamiento de Noticias Crudas (Raw Articles)

Este documento detalla todas las lógicas y filtros de negocio aplicados en la fase de ingesta y preparación de las noticias crudas. Corresponde al módulo visual de **Noticias Crudas** en el panel administrativo (`/admin/raw-articles`).

---

## 1. Límite de Volumen de Ingesta por Fuente (Source Volume Capping)

### 1.1 Regla de Negocio
Para evitar que una fuente externa muy activa monopolice la portada de la revista digital con demasiados artículos en un solo día:
* Se limita a un máximo de **3 artículos diarios** procesados (draft o publicados) de una misma fuente.

### 1.2 Regla de Bypass (Excepciones)
Se omitirá este tope y se procesará la noticia si se cumple cualquiera de las siguientes condiciones:
1. El artículo es calificado por la IA con una importancia **$\ge 8$**.
2. La fuente cruda original es de tipo `atom` (lanzamientos de frameworks, actualizaciones de seguridad, etc.).
3. El administrador procesa el artículo manualmente haciendo clic en **"Forzar Re-procesamiento"** en la administración.

### 1.3 Implementación Algorítmica
Esta verificación se ejecuta en `ProcessArticleWithAIJob` inmediatamente después de la pre-clasificación para evitar traducciones y redacciones de contenido costosas si el artículo va a ser descartado:
```php
if ($source) {
    $todayCount = Article::whereDate('created_at', today())
        ->whereHas('rawArticle', function ($query) use ($source) {
            $query->where('source_id', $source->id);
        })->count();

    $importance = (int) ($classification['importance'] ?? 5);
    $isHighlyImportant = ($importance >= 8) || ($source->type === 'atom');

    if ($todayCount >= 3 && !$isHighlyImportant && !$this->forceImmediate) {
        $this->rawArticle->update(['status' => 'ignored']);
        Log::info("RawArticle {$this->rawArticle->id} ignored: Source '{$source->name}' has already processed {$todayCount} articles today.");
        return;
    }
}
```

---

## 2. Sanitización y Limpieza de Contenido (Content Sanitization)

### 2.1 Regla de Negocio
Las noticias crudas obtenidas mediante web scraping (por ejemplo, vía Jina AI Reader o parsers RSS) suelen incluir código JavaScript residual, etiquetas CSS, imágenes embebidas, menús de navegación lateral y enlaces repetitivos (como pies de página de redes sociales, políticas de cookies y enlaces del autor). 

Para **ahorrar más del 45% de tokens** y evitar alucinaciones en el procesamiento de la IA, el sistema ejecuta una sanitización agresiva sobre el contenido.

### 2.2 Gatillador Automático (Mutador de Eloquent)
La sanitización se aplica automáticamente al guardar o actualizar cualquier `RawArticle` gracias a un mutador en el modelo (`app/Models/RawArticle.php`):
```php
public function setContentAttribute($value)
{
    $this->attributes['content'] = self::sanitizeContent($value);
}
```

### 2.3 Procesamiento Algorítmico de Limpieza
El método estático `sanitizeContent` ejecuta los siguientes filtros secuenciales:

1. **Eliminar scripts, estilos e iframes HTML:** Previene la inyección de código.
2. **Remover imágenes Markdown e HTML:** Evita enviar URLs de imágenes innecesarias a la IA.
3. **Conversión de enlaces a texto plano (Markdown e HTML):** Convierte enlaces tipo `[Apple](https://techcrunch.com/tag/apple/)` a texto simple `"Apple"`, destruyendo enlaces de monetización o navegación repetitivos.
4. **Filtro de Boilerplate / Contenido Residual:** Segmenta el texto por líneas y descarta aquellas de longitud inferior a 150 caracteres que contengan palabras clave típicas de publicidad o navegación (ej. *"subscribe to"*, *"newsletter"*, *"privacy policy"*, *"share on twitter"*, *"read more"*).
5. **Remoción de divisores y líneas en blanco excesivas:** Compacta el documento.
