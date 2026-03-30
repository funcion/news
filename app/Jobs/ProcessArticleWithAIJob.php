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
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60]; // wait 10s, then 30s, then 60s
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected RawArticle $rawArticle
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OpenRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService): void
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

        if (!($classification['is_relevant'] ?? false) && empty($classification['is_seed'])) {
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
            throw new \RuntimeException("La IA no pudo redactar el contenido. Reintentando Job (Intento {$this->attempts()})");
        }

        // Create the final Article without images
        $slug = $redacted['slug'] ?? Str::slug($redacted['title'] ?? $this->rawArticle->title);
        $content = $redacted['content'];

        // Determine Final Category
        $categoryId = $this->rawArticle->source->category_id ?? 1; // Default
        if (!empty($classification['category_name'])) {
            $matchedCat = \App\Models\Category::whereRaw('LOWER(name) = ?', [strtolower(trim($classification['category_name']))])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        $article = Article::create([
            'raw_article_id' => $this->rawArticle->id,
            'title' => $redacted['title'] ?? $this->rawArticle->title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $redacted['excerpt'] ?? Str::words(strip_tags($content), 30),
            'author_id' => $author->id,
            'category_id' => $categoryId,
            'status' => 'draft',
            'meta_title' => $redacted['title'] ?? null,
            'meta_description' => $redacted['excerpt'] ?? null,
            'meta_keywords' => $redacted['keywords'] ?? [],
            'reading_time' => $this->calculateReadingTime($content),
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

        // --- MÓDULO 3: GENERACIÓN MULTI-IMAGEN CON SILICONFLOW Y SPATIE MEDIA LIBRARY ---
        $imageCount = 0;
        $imageObjectsJsonLd = [];

        if (!empty($redacted['image_prompts']) && is_array($redacted['image_prompts'])) {
            foreach ($redacted['image_prompts'] as $index => $imgData) {
                if ($index >= 5) break; 
                
                $placeholder = $imgData['id'] ?? '';
                $promptEn = $imgData['prompt_en'] ?? '';
                $altEs = $imgData['alt_es'] ?? '';
                $captionEs = $imgData['caption_es'] ?? $altEs;
                $titleEs = $imgData['title_es'] ?? $altEs;
                
                if (empty($placeholder) || empty($promptEn)) continue;

                $path = $imageService->generateAndSave($promptEn, $slug, $index + 1);
                
                if ($path && file_exists($path)) {
                    // Add to Spatie Media Library
                    $media = $article->addMedia($path)
                                     ->withCustomProperties([
                                         'alt' => $altEs,
                                         'caption' => $captionEs,
                                         'title' => $titleEs
                                     ])
                                     ->toMediaCollection('images');
                    
                    $urlOriginal = $media->getUrl();
                    $urlThumb = $media->getUrl('thumb');
                    $urlMedium = $media->getUrl('medium');
                    $urlLarge = $media->getUrl('large');
                    
                    $srcset = "{$urlThumb} 480w, {$urlMedium} 800w, {$urlLarge} 1200w";
                    $sizes = "(max-width: 800px) 100vw, 800px";
                    $imgId = "img-" . ($index + 1) . "-" . Str::random(5);

                    // HTML Semántico (WCAG 2.1 AAA & SEO 10/10) - Responsive Ready
                    $imgTag = "<figure role=\"group\" aria-labelledby=\"caption-{$imgId}\" class=\"article-image my-10 overflow-hidden rounded-xl border border-gray-100 shadow-2xl transition-all duration-500 hover:shadow-cyan-500/20\">
                        <img src=\"{$urlOriginal}\" 
                             srcset=\"{$srcset}\"
                             sizes=\"{$sizes}\"
                             alt=\"{$altEs}\" 
                             title=\"{$titleEs}\"
                             loading=\"lazy\" 
                             decoding=\"async\"
                             width=\"1280\" 
                             height=\"720\"
                             role=\"img\"
                             class=\"w-full h-auto object-cover aspect-video\">
                        <figcaption id=\"caption-{$imgId}\" class=\"text-sm text-gray-500 mt-4 text-center italic leading-relaxed px-4 bg-gray-50/50 py-3 border-t border-gray-100\">
                            {$captionEs}
                        </figcaption>
                    </figure>";

                    $content = str_replace($placeholder, $imgTag, $content);

                    // JSON-LD Metadata collection
                    $imageObjectsJsonLd[] = [
                        "@type" => "ImageObject",
                        "url" => $urlOriginal,
                        "caption" => $captionEs,
                        "description" => $altEs,
                        "width" => 1280,
                        "height" => 720
                    ];

                    // Set first image as featured image
                    if ($imageCount === 0) {
                        $article->update([
                            'image_url' => $urlOriginal,
                            'image_alt' => $altEs
                        ]);
                    }

                    $imageCount++;
                } else {
                    $content = str_replace($placeholder, '', $content);
                }
            }
        }
        
        // Final JSON-LD Update (Inyectamos la galería completa en el esquema)
        $aiMetadata = $article->ai_metadata;
        if (!empty($imageObjectsJsonLd)) {
            $aiMetadata['json_ld']['image'] = $imageObjectsJsonLd;
            $article->update(['ai_metadata' => $aiMetadata]);
        }

        // Update the article with the final injected HTML content
        $article->update(['content' => $content]);
        // --------------------------------------------------------------------------------

        $this->rawArticle->update(['status' => 'processed']);
        Log::info("RawArticle processed successfully. Article created: {$article->id} with {$imageCount} images.");
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
            'news'      => '500 - 800 palabras',
            'blog'      => '1,000 - 2,500 palabras',
            'guide'     => '1,500 - 2,500 palabras',
            'review'    => '1,500 - 3,000 palabras',
            'pillar'    => '2,500 - 5,000+ palabras',
        ];
        $targetStr = $targetMap[$contentType] ?? $targetMap['blog'];

        $prompt = "ROL: Periodista Senior (15 años exp) y Estratega de SEO Semántico.
        FECHA ACTUAL: {$today}
        TIPO DE POST: {$contentType}
        OBJETIVO: Satisfacer la 'INTENCIÓN DE BÚSQUEDA' y cumplimiento de ACCESIBILIDAD (ADA/WCAG).
        
        EXTENSIÓN RECOMENDADA: {$targetStr}
        " . ($isSeed ? "TEMA/SEMILLA: {$this->rawArticle->title}" : "HECHOS CLAVE: " . implode(", ", $classification['facts'])) . "
        
        REGLAS DE ESCRITURA (HUMAN-LIKE, SEO 10/10 & ADA):
        1. ALMA HUMANA (ANTI-CLICHÉ): PROHIBIDO usar: 'cambio de paradigma', 'fuerza innegable', 'vasto campo'.
        2. RITMO (BURSTINESS): Alterna frases de 3-5 palabras con oraciones complejas. Impacto puro.
        3. MICRO-STORYTELLING: Incluye un ejemplo o testimonio corto ficticio o realista.
        4. IMÁGENES AUTOMÁTICAS: Inserta de 1 a 5 placeholders [IMAGE_1], [IMAGE_2] en tu HTML. [IMAGE_1] debe ir después del primer o segundo párrafo (portada).
        5. ACCESIBILIDAD (WCAG): Describe cada imagen pensando en una persona ciega. Sé descriptivo pero conciso.
        6. PROMPTS PARA FLUX (ULTRA-REALISMO): Para cada imagen, crea un prompt en INGLÉS súper descriptivo, fotorrealista, estilo periodístico.
           IMPORTANTE: Si aparecen personas, exige 'hyper-realistic skin textures', 'pores', 'natural lighting', 'realistic human eyes', 'avoid smooth plastic skin'. Estilo cine 8k, DSLR.
        
        Responde estrictamente en formato JSON:
        {
            \"title\": \"Título ClickMagnet, SEO optimizado y corto\",
            \"slug\": \"titulo-corto-seo\",
            \"excerpt\": \"Meta descripción persuasiva (max 155 chars)\",
            \"keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
            \"content\": \"Cuerpo HTML completo con H2, p, <strong>, blockquote y los placeholders [IMAGE_1], [IMAGE_2], etc. intercalados\",
            \"image_prompts\": [
                {
                    \"id\": \"[IMAGE_1]\",
                    \"prompt_en\": \"A hyper-realistic editorial photo of [SUBJECT]... detailed facial features, realistic skin texture, 8k resolution, shot on 35mm lens, cinematic natural lighting, no text.\",
                    \"alt_es\": \"Texto alternativo descriptivo ACCESIBLE (para lectores de pantalla)\",
                    \"caption_es\": \"Leyenda informativa y con contexto para el pie de foto\",
                    \"title_es\": \"Título SEO para el atributo title de la imagen\"
                }
            ],
            \"json_ld\": {
                \"@context\": \"https://schema.org\",
                \"@type\": \"NewsArticle\",
                \"headline\": \"Título\",
                \"datePublished\": \"{$today}\",
                \"author\": {\"@type\": \"Person\", \"name\": \"{$author->name}\"}
            }
        }";

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_LATEST);

        $data = $this->parseJson($response);

        // Safety: Ensure keywords is an array
        if (isset($data['keywords']) && is_string($data['keywords'])) {
            $data['keywords'] = array_map('trim', explode(',', $data['keywords']));
        }

        return $data;
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

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->rawArticle->update(['status' => 'failed']);
        Log::error("ProcessArticleWithAIJob failed after all attempts for RawArticle: {$this->rawArticle->id}. Error: {$exception->getMessage()}");
    }
}
