# 📚 Manual de Arquitectura Editorial, Límites SEO y Pipeline de IA

Este documento detalla la arquitectura técnica, límites de caracteres, pipeline de redacción con Inteligencia Artificial, sistema de auto-curación lingüística y la botonera de acciones rápidas de Filament.

---

## 1. Single Source of Truth: Límites Editoriales y SEO (`config/global.php`)

Todos los límites del sistema están centralizados en `config/global.php` y sincronizados con los estándares internacionales de indexación en Google:

| Variable / Campo | Mínimo | Máximo | Unidad | Descripción y Propósito |
| :--- | :---: | :---: | :---: | :--- |
| **`title`** | `50` | `130` | Caracteres | Titulares periodísticos completos (Sujeto + Acción + Impacto). |
| **`title.min_words`** | `7` | — | Palabras | Mínimo de palabras para evitar titulares truncados o telegráficos. |
| **`excerpt`** | `160` | `250` | Caracteres | Resumen informativo y gancho editorial para feeds y portadas. |
| **`meta_title`** | `50` | `70` | Caracteres | **Google SERP**: Título optimizado para motores de búsqueda. |
| **`meta_description`** | `120` | `160` | Caracteres | **Google Snippet**: Descripción concisa para resultados de búsqueda. |
| **`image_alt`** | — | `125` | Caracteres | Texto alternativo descriptivo y accesible (WCAG 2.1). |
| **`image_title`** | — | `70` | Caracteres | Título semántico de la imagen. |
| **`min_words.news`** | `800` | — | Palabras | Mínimo estricto de palabras por idioma para artículos de Glodaxia. |

---

## 2. Los 3 Candados del Reprocesamiento: ¿Por qué fallaba antes?

Anteriormente, al hacer clic en **"Reprocesar Noticia Completa"**, el Job terminaba en 0.01 segundos y el contenido no cambiaba debido a 3 bloqueos en el código:

1. **El Guard de Estado Publicado (`ProcessArticleWithAIJob.php` L74)**:
   * Si el artículo ya tenía `status = 'published'` y `content` no estaba vacío, el Job ejecutaba un `return;` inmediato asumiendo que ya estaba procesado.
2. **El Detector de Duplicados (`DuplicateCheckerService.php`)**:
   * Al reevaluar la URL y el titular ya existentes en la base de datos, el detector marcaba el artículo como "duplicado" y abortaba el trabajo.
3. **El Filtro de Antigüedad**:
   * Si la noticia original tenía más de 7 días, era descartada como obsoleta.

### 🔓 Solución: Modo Forzado Incondicional (`forceReprocess = true`)
Al invocar `ProcessArticleWithAIJob($rawArticle, forceReprocess: true)`:
* Se ignoran todos los candados anteriores.
* Se ejecuta la IA de forma obligatoria e incondicional.
* Se actualiza el artículo existente en la base de datos conservando su ID, su URL y su fecha de publicación original.

---

## 3. Blindaje de Idioma y Auto-Curación en Vuelo (Zero Tolerance)

Para garantizar que **nunca se mezcle contenido en español dentro de la edición en inglés ni contenido en inglés dentro de la edición en español**:

```
[Entrada: Noticia en cualquier idioma (Chino / Español / Inglés / Francés)]
                               │
                               ▼
            [1. Prompt con Directiva Bilingüe Estricta]
                               │
                               ▼
            [2. Auto-Curación en Memoria (In-Flight)]
      ┌────────────────────────────────────────────────────────────┐
      │ • Analiza densidad de Stopwords en tiempo real.            │
      │ • Si content_en contiene español ➔ Auto-traduce a inglés.  │
      │ • Si content_es contiene inglés ➔ Auto-traduce a español.  │
      │ • Sanea meta_titles y meta_descriptions a límites SERP.    │
      └────────────────────────────────────────────────────────────┘
                               │
                               ▼
            [3. Desactivación de Fallback en Spatie]
      ┌────────────────────────────────────────────────────────────┐
      │ En Article.php: public bool $useFallbackLocale = false;    │
      │ Impide que si un campo en inglés está vacío devuelva texto │
      │ en español de forma silenciosa.                            │
      └────────────────────────────────────────────────────────────┘
                               │
                               ▼
            [4. Persistencia Atómica en Base de Datos]
```

---

## 4. Botonera de Acciones Rápidas en Filament (`ArticleResource.php`)

En la tabla del panel de administración (`/admin/articles`):

| Botón | Icono | Acción | Comportamiento |
| :--- | :---: | :--- | :--- |
| **Regenerar Título** | `[ 🪄 ]` | `regenerate_title` | Despacha `RegenerateArticleTitleJob`. Calibra titular en EN/ES a 70–115 caracteres. |
| **Regenerar Portada** | `[ 🖼️ ]` | `regenerate_image` | Despacha `RegenerateArticleHeroImageJob`. Genera nueva imagen con FLUX.1 (SiliconFlow). |
| **Reprocesar Completo** | `[ 🔄 ]` | `reprocess_article` | Despacha `ProcessArticleWithAIJob($rawArticle, forceReprocess: true)`. Reescribe 100% de la noticia. |

### Características de la Interfaz:
* **Spinners Continuos**: Al hacer clic, el botón oculta su icono y muestra un anillo giratorio continuo limpio (`.is-processing-bg`).
* **Persistencia ante Recargas (`F5`)**: El estado se consulta directamente de Redis (`article_title_processing_{id}`, `article_full_reprocessing_{id}`).
* **Auto-Recuperación**: La tabla consulta cada 5 segundos (`poll('5s')`) y restaura los iconos automáticamente al terminar la IA.

---

## 5. Guardado Seguro en Formulario de Edición (`EditArticle.php`)

* Si el redactor edita manualmente un artículo y el `meta_title` o `meta_description` supera ligeramente los caracteres de Google SERP, `mutateFormDataBeforeSave()` aplica **auto-clamping** transparente:
  * `meta_title` se recorta a máximo 70 caracteres.
  * `meta_description` se recorta a máximo 160 caracteres.
* **Resultado**: El redactor nunca se queda bloqueado por errores de validación al guardar.