<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\RawArticle;
use App\Services\AI\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessArticleWithAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 240;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected RawArticle $rawArticle
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OpenRouterService $ai): void
    {
        $today = now()->format('l, F j, Y');
        Log::info("Processing RawArticle: {$this->rawArticle->id} with AI Gemini 2.0 Flash at {$today}.");

        if ($this->rawArticle->status !== 'pending') {
            Log::warning("RawArticle {$this->rawArticle->id} is already processed or in error.");
            return;
        }

        // Layer 1 & 2: Classify and Extract (Gemini 3 Flash)
        $classification = $this->classifyAndExtract($ai);
        
        if ($classification === null) {
            throw new \Exception("La IA no respondió o el JSON es inválido. Reintentando Job...");
        }

        if (!($classification['is_relevant'] ?? false)) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::info("RawArticle {$this->rawArticle->id} ignorada por la IA (no relevante).");
            return;
        }

        // Layer 3: Redact (Gemini 3 Flash)
        $author = Author::ai()->active()->inRandomOrder()->first();
        
        if (!$author) {
            Log::warning("No active AI Author found. Creating a generic one.");
            $author = Author::create([
                'name' => 'IA Redactor',
                'slug' => 'ia-redactor',
                'type' => 'ai',
                'is_active' => true,
                'voice_style' => 'El Divulgador',
                'bio' => 'IA optimizada para redacción de noticias tecnológicas.',
            ]);
        }

        $redacted = $this->redactContent($ai, $classification, $author);

        if (!$redacted) {
            $this->rawArticle->update(['status' => 'failed']);
            return;
        }

        // Determine Final Category
        $categoryId = $this->rawArticle->source->category_id ?? 1; // Default
        if (!empty($classification['category_name'])) {
            $matchedCat = \App\Models\Category::whereRaw('LOWER(name) = ?', [strtolower(trim($classification['category_name']))])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        // Create the final Article
        $article = Article::create([
            'raw_article_id' => $this->rawArticle->id,
            'title' => $redacted['title'] ?? $this->rawArticle->title,
            'slug' => $redacted['slug'] ?? Str::slug($redacted['title'] ?? $this->rawArticle->title),
            'content' => $redacted['content'],
            'excerpt' => $redacted['excerpt'] ?? Str::words(strip_tags($redacted['content']), 30),
            'author_id' => $author->id,
            'category_id' => $categoryId,
            'status' => 'draft',
            'meta_title' => $redacted['title'] ?? null,
            'meta_description' => $redacted['excerpt'] ?? null,
            'meta_keywords' => $redacted['keywords'] ?? [],
            'reading_time' => $this->calculateReadingTime($redacted['content']),
            'ai_metadata' => [
                'facts' => $classification['facts'] ?? [],
                'voice_style' => $author->voice_style,
                'origin_url' => $this->rawArticle->url,
                'model' => OpenRouterService::MODEL_GEMINI_LATEST,
                'detected_category' => $classification['category_name'] ?? null,
                'json_ld' => $redacted['json_ld'] ?? null,
                'faq_json_ld' => $redacted['faq_json_ld'] ?? null,
                'today_date' => $today,
            ],
        ]);

        $this->rawArticle->update(['status' => 'processed']);
        Log::info("RawArticle processed successfully. Article created: {$article->id}.");
    }

    protected function classifyAndExtract(OpenRouterService $ai): ?array
    {
        $content = trim(strip_tags($this->rawArticle->content ?? ''));
        
        $categories = \App\Models\Category::active()->pluck('name')->toArray();
        $categoriesList = empty($categories) ? 'Noticias Generales' : implode(', ', $categories);
        
        if (empty($content)) {
            Log::info("RawArticle {$this->rawArticle->id} has no content. Treating as a 'Seed Idea'.");
            return [
                'is_relevant' => true,
                'importance' => 8,
                'is_seed' => true,
                'category_name' => $categories[0] ?? 'General',
                'facts' => [
                    $this->rawArticle->title,
                    $this->rawArticle->summary ?? 'Genera el contenido basado en este tema.'
                ]
            ];
        }

        $today = now()->format('l, F j, Y');
        $prompt = "ROL: Actúa como un experto Editor Jefe de Redacción Digital y Especialista en SEO Semántico de nivel 10/10.
        FECHA ACTUAL: {$today}
        
        OBJETIVO: Analiza esta noticia cruda y decide si merece ser redactada como un artículo de alta calidad.
        
        CATEGORÍAS VÁLIDAS: [{$categoriesList}]
        
        REGLAS DE SEGURIDAD (IMPORTANTE):
        1. RECHAZO ESTRICTO: Di 'is_relevant: false' si el contenido es:
           - Publicidad encubierta o spam.
           - Listas de empleos o eventos locales menores.
           - Noticia muy genérica o sin 'Información Ganancial' (poca carnita).
           - Noticia desactualizada o que contradiga hechos a día de hoy ({$today}).
        2. ACEPTE SÓLO SI: Aporta valor real al usuario, tiene potencial viral/SEO o es una noticia de impacto en el sector.
        
        Ahorra mis créditos: Este es el paso de seguridad. Sólo dí 'true' si estás 100% seguro de que merece un artículo de +1500 palabras.
        
        NOTICIA:
        Título: {$this->rawArticle->title}
        Contenido: {$this->rawArticle->content}
        
        Responde estrictamente en formato JSON:
        {
            \"is_relevant\": bool,
            \"category_name\": \"Nombre de categoría\",
            \"content_type\": \"news | blog | guide | pillar | review\",
            \"importance\": int (1-10),
            \"facts\": [\"hechos\", \"verificados\"]
        }";

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_LATEST);

        return $this->parseJson($response);
    }

    protected function redactContent(OpenRouterService $ai, array $classification, Author $author): ?array
    {
        $today = now()->format('l, F j, Y');
        $isSeed = $classification['is_seed'] ?? false;
        $contentType = $classification['content_type'] ?? 'blog';
        
        // Extended dynamic targets based on Search Intent (Refined 2026 Standards)
        $targetMap = [
            'news'      => '500 - 800 palabras (Rapidez, frescura e impacto inmediato).',
            'blog'      => '1,000 - 2,500 palabras (Equilibrio de profundidad y retención).',
            'guide'     => '1,500 - 2,500 palabras (Tutorial detallado, pasos y solución de problemas).',
            'review'    => '1,500 - 3,000 palabras (Análisis de pros/contras y comparativas).',
            'pillar'    => '2,500 - 5,000+ palabras (La autoridad definitiva pilar del dominio).',
        ];
        $targetStr = $targetMap[$contentType] ?? $targetMap['blog'];

        $prompt = "ROL: Periodista Senior (15 años exp) y Estratega de SEO Semántico.
        FECHA ACTUAL: {$today}
        TIPO DE POST: {$contentType}
        OBJETIVO: Satisfacer la 'INTENCIÓN DE BÚSQUEDA'. Prioriza la 'SATISFACCIÓN DEL USUARIO' sobre el conteo de palabras.
        
        EXTENSIÓN RECOMENDADA: {$targetStr}
        " . ($isSeed ? "TEMA/SEMILLA: {$this->rawArticle->title}" : "HECHOS CLAVE: " . implode(", ", $classification['facts'])) . "
        
        REGLAS DE ESCRITURA (HUMAN-LIKE & SEO 10/10):
        1. ALMA HUMANA (ANTI-CLICHÉ): PROHIBIDO usar: 'cambio de paradigma', 'fuerza innegable', 'vasto campo', 'en el mundo de hoy', 'salto cualitativo'.
        2. RITMO (BURSTINESS): Alterna frases de 3-5 palabras con oraciones complejas. Impacto puro.
        3. GANCHO (LEAD): Empieza con un dato agresivo o anécdota. Olvida la introducción institucional.
        4. MICRO-STORYTELLING: Incluye un ejemplo o testimonio corto de una persona ficticia pero realista para humanizar el tema.
        5. VERACIDAD E-E-A-T: Usa cifras específicas (%, fechas). Si no hay fuente real, usa 'Reportes del sector'. NO INVENTES EXPERTOS.
        6. FORMATEO: 5-10 líneas por párrafo, <strong> para LSI keywords, [IMAGEN: descripción] con ALT cada 500 palabras.
        7. CIERRE (CTA): Conclusión punzante y Llamado a la Acción (CTA) claro.
        8. FAQ: Al final, añade 3 FAQs con Schema para fragmentos destacados.
        
        Responde estrictamente en formato JSON:
        {
            \"title\": \"Título ClickMagnet\",
            \"slug\": \"slug-url-optimizado\",
            \"excerpt\": \"Meta descripción persuasiva (120-155 chars)\",
            \"content\": \"Cuerpo HTML completo (H2, p, <strong>, blockquote, FAQ, marcadores imagen)\",
            \"json_ld\": {
                \"@context\": \"https://schema.org\",
                \"@type\": \"NewsArticle\",
                \"headline\": \"Título\",
                \"datePublished\": \"{$today}\",
                \"author\": {\"@type\": \"Person\", \"name\": \"{$author->name}\"}
            },
            \"faq_json_ld\": {
                \"@context\": \"https://schema.org\",
                \"@type\": \"FAQPage\",
                \"mainEntity\": [array of microdata for 3 FAQs]
            }
        }";

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_LATEST);

        return $this->parseJson($response);
    }



    protected function parseJson(?string $json): ?array
    {
        if (!$json) return null;
        
        // Remove markdown blocks if any
        $clean = preg_replace('/```json|```/', '', $json);
        
        return json_decode(trim($clean), true);
    }

    protected function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }
}
