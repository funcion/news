# 🚀 PLAN DE MEJORA — ProcessArticleWithAIJob Prompt & Pipeline

> **Creado**: 4 Junio 2026  
> **Objetivo**: Llevar el prompt de redacción IA de nivel producción a nivel **élite**  
> **Archivo**: `app/Jobs/ProcessArticleWithAIJob.php`  
> **Impacto esperado**: Mayor adherencia a reglas, menos rollbacks, mejor calidad de output

---

## 📊 ESTADO ACTUAL

| Aspecto | Nota | Detalle |
|---------|------|---------|
| Style DNA (9.2M combos) | 9/10 | Brillante, mantener |
| Reglas anti-AI detection | 8/10 | Muy completas, faltan validaciones |
| Pipeline PHP (filtros + validación) | 8/10 | Robusto, pero gaps importantes |
| Adherencia del modelo a reglas | 5/10 | Demasiadas reglas sin verificar |
| Contradicciones en el prompt | ⚠️ | Problema real |
| Robustez ante fallos (imágenes) | 4/10 | Rollback demasiado agresivo |
| Validación programática | 5/10 | No verifica asimetría, ni español |

---

## 🔴 PRIORIDAD CRÍTICA — Implementar Primero

### TAREA 1: Verificación programática de asimetría de párrafos
**Impacto**: Alto | **Esfuerzo**: Medio | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: El prompt pide "exactly N single-sentence paragraphs", "exactly N long paragraphs", etc. Pero `validateRedactedOutput()` NO verifica ninguno de estos conteos. Son wishful thinking.

**Solución**: Agregar método `validateParagraphAsymmetry()` que:
1. Parse el HTML del content → extraer párrafos (`<p>`, `<h2>`, `<blockquote>`, `<li>`)
2. Contar oraciones por párrafo (split por `. ` + `? ` + `! `)
3. Verificar: no hay 3+ párrafos consecutivos con el mismo conteo de oraciones
4. Verificar: hay al menos 1 párrafo de 1 sola oración
5. Verificar: hay al menos 1 párrafo largo (6+ oraciones)
6. Log warnings (no hard fail) para asimetría sub-óptima

```php
// Pseudocódigo del método esperado
private function validateParagraphAsymmetry(string $html, string $lang): array
{
    $errors = [];
    $paragraphs = $this->extractParagraphs($html);
    $sentenceCounts = array_map(fn($p) => $this->countSentences($p), $paragraphs);
    
    // Check consecutive same-count
    $consecutive = 1;
    for ($i = 1; $i < count($sentenceCounts); $i++) {
        if ($sentenceCounts[$i] === $sentenceCounts[$i - 1] && $sentenceCounts[$i] > 0) {
            $consecutive++;
            if ($consecutive >= 3) {
                $errors[] = "3+ consecutive paragraphs with {$sentenceCounts[$i]} sentences in {$lang}";
            }
        } else {
            $consecutive = 1;
        }
    }
    
    // Check minimum single-sentence paragraphs
    $singleSentence = count(array_filter($sentenceCounts, fn($c) => $c === 1));
    if ($singleSentence === 0) {
        $errors[] = "No single-sentence paragraphs found in {$lang}";
    }
    
    return $errors;
}
```

**Nota**: Los resultados deben ser **warnings** (log), no hard fails. El modelo no siempre puede cumplir perfectamente, pero necesitamos visibilidad.

---

### TAREA 2: Verificación de frases bloqueadas en ESPAÑOL
**Impacto**: Alto | **Esfuerzo**: Bajo | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: `validateRedactedOutput()` solo verifica frases bloqueadas en inglés:
```php
$blockedPhrases = [
    'paradigm shift', 'game-changer', ...
    // ← NO hay español aquí
];
```

Pero el prompt bloquea frases en español:
> ES: "cambio de paradigma", "en conclusión", "sin lugar a dudas", "cabe destacar", "queda por ver", "un arma de doble filo", "marca un antes y un después", "las implicaciones son profundas"

**Solución**: Agregar array `$blockedPhrasesEs` y verificar `contentEs`:

```php
$blockedPhrasesEs = [
    'cambio de paradigma', 'en conclusión', 'sin lugar a dudas',
    'cabe destacar', 'queda por ver', 'un arma de doble filo',
    'marca un antes y un después', 'las implicaciones son profundas',
    'en el mundo de', 'hoy en día', 'sin ir más lejos',
    'como ya hemos mencionado', 'en última instancia',
    'no es otro que', 'a día de hoy',
];
$contentEsLower = strtolower($contentEs);
foreach ($blockedPhrasesEs as $phrase) {
    if (str_contains($contentEsLower, $phrase)) {
        $errors[] = "Blocked AI-fingerprint phrase detected in content_es: '{$phrase}'";
        break;
    }
}
```

---

### TAREA 3: Resolver rollback agresivo por fallo de imágenes
**Impacto**: Alto | **Esfuerzo**: Bajo | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: Si SiliconFlow falla, todo el artículo se descarta:
```php
if ($imageCount === 0) {
    $article->delete(); // ← Artículo perfecto perdido
    throw new \RuntimeException("No images were generated...");
}
```

Un artículo de 1200 palabras perfectamente redactado, con SEO impecable, se pierde por un timeout de API de imágenes externa.

**Solución**: Cambiar a estrategia de "degradación elegante":
1. Si `[IMAGE_1]` (hero) falla → generar imagen placeholder con texto overlay del título
2. Si imágenes interiores fallan → eliminar tokens `[IMAGE_N]` del content y continuar
3. Si NINGUNA imagen se genera → dejar artículo como `draft` en vez de `delete`
4. Agregar flag `needs_images: true` en `ai_metadata` para que admin pueda re-generar desde Filament

```php
// Reemplazar el hard delete por soft handling
if ($imageCount === 0) {
    $article->status = 'draft';
    $article->ai_metadata = array_merge($article->ai_metadata ?? [], [
        'needs_images' => true,
        'image_failure_reason' => 'All image generations failed',
    ]);
    $article->save();
    Log::warning("Article {$article->id} saved as draft — no images generated. Admin review needed.");
    // NO throw, NO delete
}
```

---

### TAREA 4: Resolver contradicciones del prompt
**Impacto**: Alto | **Esfuerzo**: Medio | **Archivo**: `ProcessArticleWithAIJob.php` (sección `redactBilingual`)

**Problemas identificados**:

| # | Contradicción | Impacto |
|---|---------------|---------|
| 1 | "WRITE WITH GENUINE PERSONALITY" vs 20+ frases bloqueadas + 10+ patrones bloqueados | El modelo recibe "sé libre" + "pero no hagas nada de esto" → parálisis |
| 2 | "TAKE A CLEAR STANCE" vs "Include exactly N sentences where you doubt your own argument" | Tensión entre certeza y duda que el modelo resuelve mal |
| 3 | "No more than two consecutive paragraphs without an [IMAGE_N] token" vs "[IMAGE_1] must NOT appear in content" | Confusión sobre si el hero va en el body o no |
| 4 | "self-check ONLY these 5" pero luego hay 50+ reglas más | El modelo se centra en las 5 del self-check e ignora el resto |

**Solución**: Reescribir secciones contradictorias:
1. Cambiar "WRITE WITH GENUINE PERSONALITY" → "WRITE WITH DISCIPLINED PERSONALITY — be yourself within guardrails"
2. Cambiar "TAKE A CLEAR STANCE" + "doubt moments" → "State your thesis clearly, then stress-test it with 1-2 honest caveats" (menos mecánico)
3. Clarificar explícitamente: "IMAGE tokens start at [IMAGE_2] in body. [IMAGE_1] is hero-only, never in body."
4. Eliminar el self-check final (placebo) y mover esas 5 validaciones a PHP donde realmente funcionan

---

## 🟡 PRIORIDAD ALTA — Segundo Lote

### TAREA 5: Mejorar temperatura y jitter
**Impacto**: Medio | **Esfuerzo**: Bajo | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: El jitter actual es ±0.05, que es imperceptible:
```php
$temperature = ($temperatureMap[$styleSeeds[0]] ?? 0.7) + (mt_rand(-5, 5) / 100);
```

**Solución**: Aumentar jitter y agregar variación por content_type:
```php
// Jitter más agresivo: ±0.15
$jitter = mt_rand(-15, 15) / 100;

// Content type afecta temperatura
$ctypeBonus = match($contentType) {
    'news'   => -0.05,  // Más preciso para noticias
    'blog'   => 0.0,    // Neutral
    'guide'  => -0.10,  // Más estructurado
    'review' => 0.05,   // Más expresivo
    'pillar' => 0.0,    // Neutral
    default  => 0.0,
};

$temperature = ($temperatureMap[$styleSeeds[0]] ?? 0.7) + $jitter + $ctypeBonus;
$temperature = max(0.3, min(1.0, $temperature));
```

---

### TAREA 6: Conteo de oraciones robusto (soporte multilingüe)
**Impacto**: Medio | **Esfuerzo**: Medio | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: `str_word_count()` y split por `. ` no funcionan bien para:
- Oraciones que terminan en `!` o `?`
- Abreviaciones (`Dr.`, `EE.UU.`, `U.S.`, `etc.`)
- Oraciones en español con `¿...?` y `¡...!`
- HTML tags dentro del texto

**Solución**: Método `countSentences()` robusto:
```php
private function countSentences(string $text): int
{
    // Strip HTML tags
    $clean = strip_tags($text);
    // Remove common abbreviations that end with period
    $clean = preg_replace('/\b(Dr|Mr|Mrs|Ms|Prof|Sr|Sra|Ing|EE\.UU|U\.S|etc|vs|approx)\.\s*/i', '$1 ', $clean);
    // Split by sentence-ending punctuation
    $sentences = preg_split('/(?<=[.!?])\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
    // Filter out empty/whitespace-only
    return count(array_filter($sentences, fn($s) => mb_strlen(trim($s)) > 5));
}
```

---

### TAREA 7: Validación de longitud de headings H2
**Impacto**: Bajo-Medio | **Esfuerzo**: Bajo | **Archivo**: `ProcessArticleWithAIJob.php`

**Problema**: El prompt dice "never make all H2 headings exactly the same word count (4-6 words each is a tell)" pero no se verifica.

**Solución**: Agregar a validación:
```php
private function validateHeadingVariety(string $html): array
{
    $errors = [];
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $html, $matches);
    $headings = $matches[1] ?? [];
    
    if (count($headings) >= 3) {
        $wordCounts = array_map(fn($h) => str_word_count(strip_tags($h)), $headings);
        $uniqueCounts = array_unique($wordCounts);
        
        // If all headings have same word count ±1 → flag
        if (count($uniqueCounts) <= 2 && count($headings) >= 3) {
            $errors[] = 'H2 headings have suspiciously uniform word counts: ' . implode(', ', $wordCounts);
        }
    }
    
    return $errors;
}
```

---

## 🟢 PRIORIDAD MEDIA — Tercer Lote

### TAREA 8: Extraer el prompt gigante a un archivo de configuración
**Impacto**: Mantenibilidad | **Esfuerzo**: Medio

**Problema**: El prompt está hardcodeado como string HEREDOC dentro de `redactBilingual()`. Esto hace que:
- El método `redactBilingual()` sea de ~300+ líneas
- Difícil de versionar (un cambio en el prompt = todo el diff del archivo)
- Imposible de testear unitariamente

**Solución**: Mover a `resources/prompts/` o `config/prompts.php`:
```
resources/prompts/
├── classify.txt          # Prompt de clasificación
├── redact_v2.txt         # Prompt de redacción (con {{placeholders}})
└── classify_v2.txt        # Prompt de clasificación v2
```

```php
protected function redactBilingual(...): ?array
{
    $template = file_get_contents(resource_path('prompts/redact_v2.txt'));
    $prompt = str_replace([
        '{{today}}', '{{persona}}', '{{rules}}', '{{styleDna}}', ...
    ], [
        $today, $persona, $rules, json_encode($styleDna), ...
    ], $template);
    
    $response = $ai->complete([['role' => 'user', 'content' => $prompt]], ...);
    // ...
}
```

---

### TAREA 9: Validación de tokens [IMAGE_N] sincronizados
**Impacto**: Medio | **Esfuerzo**: Bajo

**Problema**: Se verifica que los tokens en `content_en` tengan matching en `image_prompts`, pero NO se verifica que:
- Los tokens estén en líneas propias (standalone)
- No estén dentro de un `<p>` tag
- El contenido entre `content_en` y `content_es` tenga los tokens en posiciones similares

**Solución**:
```php
private function validateImageTokenPlacement(string $html, string $lang): array
{
    $errors = [];
    $lines = explode("\n", $html);
    
    foreach ($lines as $i => $line) {
        if (preg_match('/\[IMAGE_\d+\]/', $line)) {
            $trimmed = trim($line);
            // Token must be alone on its line
            if ($trimmed !== preg_match('/\[IMAGE_\d+\]/', $trimmed, $m) ? $m[0] : '') {
                // Token is NOT alone on its line
            }
            // Token must NOT be inside a <p> tag
            if (str_contains($line, '<p') && str_contains($line, '</p>')) {
                $errors[] = "IMAGE token found inside <p> tag in {$lang} line " . ($i + 1);
            }
        }
    }
    return $errors;
}
```

---

### TAREA 10: Imagen de fallback/placeholder para hero
**Impacto**: Medio | **Esfuerzo**: Medio

**Problema**: Si `[IMAGE_1]` (hero) falla, el artículo entero se descarta.

**Solución**: Generar imagen placeholder con GD/Intervention:
```php
private function generatePlaceholderHero(string $title, string $slug): string
{
    $img = imagecreatetruecolor(1280, 720);
    $bg = imagecolorallocate($img, 15, 23, 42); // slate-900
    $textColor = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $bg);
    // Add title text centered
    // Save as webp
    $path = storage_path("app/public/placeholder-{$slug}.webp");
    imagewebp($img, $path, 85);
    imagedestroy($img);
    return $path;
}
```

---

### TAREA 11: Métricas y logging del Style DNA
**Impacto**: Bajo-Medio | **Esfuerzo**: Bajo

**Problema**: No sabemos qué combinaciones de Style DNA se usaron por artículo. Imposible analizar patrones.

**Solución**: Guardar el Style DNA completo en `ai_metadata`:
```php
$article->ai_metadata = [
    'origin_url'  => $this->rawArticle->url,
    'today_date'  => $today,
    'json_ld'     => $redacted['json_ld'] ?? null,
    'style_dna'   => $styleDna, // ← NUEVO
    'model_used'  => OpenRouterService::MODEL_ACTIVE, // ← NUEVO
    'temperature' => $temperature, // ← NUEVO
];
```

---

### TAREA 12: Rate limiting por modelo y costo tracking
**Impacto**: Medio | **Esfuerzo**: Alto

**Problema**: No hay tracking de cuánto cuesta cada artículo ni cuántas llamadas se hacen.

**Solución**: Agregar tracking en `ai_metadata`:
```php
'ai_costs' => [
    'classification_tokens' => $classificationUsage ?? 0,
    'redaction_tokens'      => $redactionUsage ?? 0,
    'image_generations'     => $imageCount,
    'estimated_cost_usd'    => $totalCost,
    'model'                 => OpenRouterService::MODEL_ACTIVE,
    'total_duration_ms'     => $totalDuration,
]
```

---

## 🔵 PRIORIDAD BAJA — Nice to Have

### TAREA 13: Cache de clasificaciones repetidas
- Si dos RawArticles tienen títulos similares, reusar la clasificación del primero
- Ahorra 1 llamada API por duplicado parcial

### TAREA 14: Prompt versioning
- Agregar `prompt_version: 'v2.1'` al metadata
- Permite comparar calidad entre versiones del prompt
- Rollback fácil si una versión nueva produce peor output

### TAREA 15: A/B testing de prompts
- Variar el prompt entre artículos y medir: SEO score, reading time, bounce rate
- Requiere analytics implementado primero

### TAREA 16: Validación de JSON-LD contra Schema.org
- Validar que el JSON-LD generado sea válido contra el schema NewsArticle
- Usar https://schema.org/validator o librería PHP

### TAREA 17: Soporte para más idiomas (futuro)
- El Style DNA ya soporta `source_language` multi-idioma
- Agregar traducciones PT/FR/DE como Fase 3

---

## 📋 ORDEN DE IMPLEMENTACIÓN RECOMENDADO

| Orden | Tarea | Dificultad | Impacto | Tiempo Est. |
|-------|-------|-----------|---------|-------------|
| 1 | T2 — Frases bloqueadas ES | ⭐ | 🔴🔴🔴 | 15 min |
| 2 | T3 — Rollback elegante imágenes | ⭐ | 🔴🔴🔴 | 30 min |
| 3 | T4 — Resolver contradicciones | ⭐⭐⭐ | 🔴🔴🔴 | 2-3 horas |
| 4 | T1 — Verificación asimetría | ⭐⭐ | 🔴🔴 | 1-2 horas |
| 5 | T7 — Validación headings | ⭐ | 🟡 | 20 min |
| 6 | T9 — Token placement | ⭐ | 🟡 | 30 min |
| 7 | T6 — Conteo oraciones robusto | ⭐⭐ | 🟡 | 45 min |
| 8 | T5 — Temperatura/jitter | ⭐ | 🟡 | 15 min |
| 9 | T11 — Style DNA logging | ⭐ | 🟡 | 15 min |
| 10 | T8 — Extraer prompts a archivos | ⭐⭐ | 🔵 | 1-2 horas |
| 11 | T10 — Placeholder hero | ⭐⭐ | 🟡 | 1 hora |
| 12 | T12 — Cost tracking | ⭐⭐⭐ | 🟡 | 2-3 horas |

**Tiempo total estimado**: ~8-12 horas de trabajo
### Testing
- Cada tarea debe testearse con al menos 3 artículos reales (no unit tests, integration tests reales)
- Comparar output antes/después de cada cambio
- Guardar samples de artículos generados para comparación

### Rollback
- Cada cambio debe ser incremental y reversible
- Si una validación nueva causa demasiados false positives → convertir a warning (log) en vez de error

### Monitoreo
- Después de implementar, revisar logs diariamente durante 1 semana
- Buscar: "validation failed", "autoFix", "warning" en logs
- Ajustar thresholds según datos reales

---

> **Próximo paso sugerido**: Empezar por las Tareas 2, 3 y 4 (alto impacto, bajo-medio esfuerzo). Son quick wins que mejoran la calidad inmediatamente.
