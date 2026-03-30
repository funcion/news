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
        return [10, 30, 60]; 
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

        // Layer 1 & 2: Classify and Extract
        $classification = $this->classifyAndExtract($ai);
        
        if ($classification === null) {
            throw new \Exception("La IA no respondió o el JSON es inválido.");
        }

        if (!($classification['is_relevant'] ?? false) && empty($classification['is_seed'])) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::info("RawArticle {$this->rawArticle->id} ignorada por la IA.");
            return;
        }

        // Layer 3: Redact
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
            throw new \RuntimeException("La IA no pudo redactar el contenido.");
        }

        $slug = $redacted['slug'] ?? Str::slug($redacted['title'] ?? $this->rawArticle->title);
        $content = $redacted['content'];

        // --- LIMPIEZA PROFUNDA DE ALUCINACIONES (REGLA DE ORO) ---
        // Eliminamos pedazos de tags que Gemini suele inventar: " alt="..." o [IMAGE_X_ALT]
        $content = preg_replace('/\s*\"\s*alt=\"\[IMAGE_\d+_ALT\]\"\s*title=\"\[IMAGE_\d+_TITLE\]\">\s*/i', '', $content);
        $content = preg_replace('/\[IMAGE_\d+_(ALT|TITLE|CAPTION|PROMPT)\]/i', '', $content);
        $content = preg_replace('/\s*\"\s*alt=\"[^\"]*\"\s*title=\"[^\"]*\">\s*/i', '', $content);
        $content = preg_replace('/\s*\"\s+alt=\"[^\"]*\"\s+title=\"[^\"]*\">\s*/i', '', $content);
        // ---

        // Determine Final Category
        $categoryId = $this->rawArticle->source->category_id ?? 1;
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
            'content' => '', // Lo guardamos vacío para inyectar imágenes después
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
                'today_date' => $today,
                'json_ld' => $redacted['json_ld'] ?? null,
            ],
        ]);

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

                    $imageObjectsJsonLd[] = [
                        "@type" => "ImageObject",
                        "url" => $urlOriginal,
                        "caption" => $captionEs,
                        "description" => $altEs,
                        "width" => 1280,
                        "height" => 720
                    ];

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
        
        $aiMetadata = $article->ai_metadata;
        if (!empty($imageObjectsJsonLd)) {
            $aiMetadata['json_ld']['image'] = $imageObjectsJsonLd;
            $article->update(['ai_metadata' => $aiMetadata]);
        }

        $article->update(['content' => $content]);
        $this->rawArticle->update(['status' => 'processed']);
        Log::info("Article created: {$article->id} with {$imageCount} images.");
    }

    protected function classifyAndExtract(OpenRouterService $ai): ?array
    {
        $content = trim(strip_tags($this->rawArticle->content ?? ''));
        $categories = \App\Models\Category::active()->pluck('name')->toArray();
        $categoriesList = empty($categories) ? 'Noticias Generales' : implode(', ', $categories);
        
        $today = now()->format('l, F j, Y');
        $prompt = "ROL: Editor Jefe de Redacción Digital Nivel 10/10.
        FECHA: {$today}
        OBJETIVO: Clasificar y extraer hechos.
        NOTICIA: Título: {$this->rawArticle->title} Contenido: {$this->rawArticle->content}
        Responde JSON: {\"is_relevant\": bool, \"category_name\": string, \"content_type\": string, \"importance\": int, \"facts\": array}";

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
        return $this->parseJson($response);
    }

    protected function redactContent(OpenRouterService $ai, array $classification, Author $author): ?array
    {
        $today = now()->format('l, F j, Y');
        $contentType = $classification['content_type'] ?? 'blog';
        
        $prompt = "ROL: Periodista Senior y Estratega SEO.
        OBJETIVO: Redactar un artículo de alto impacto tipo {$contentType}.
        HECHOS: " . implode(", ", $classification['facts']) . "
        
        REGLAS CRÍTICAS:
        1. IMÁGENES: Usa SOLO el placeholder [IMAGE_1], [IMAGE_2], etc.
        2. NO incluyas atributos como alt=\"...\" o title=\"...\" dentro del texto del contenido.
        3. NO incluyas etiquetas <img>. El sistema las inyectará automáticamente.
        4. Solo pon el placeholder en una línea nueva.
        
        Responde JSON Estricto:
        {
            \"title\": \"Título\",
            \"slug\": \"slug\",
            \"excerpt\": \"resumen\",
            \"keywords\": [\"k1\"],
            \"content\": \"HTML con placeholders [IMAGE_1]\",
            \"image_prompts\": [
                {
                    \"id\": \"[IMAGE_1]\",
                    \"prompt_en\": \"Photorealistic style prompt... no text.\",
                    \"alt_es\": \"Texto accesible\",
                    \"caption_es\": \"Leyenda\",
                    \"title_es\": \"SEO Title\"
                }
            ],
            \"json_ld\": {\"@context\": \"https://schema.org\", \"@type\": \"NewsArticle\"}
        }";

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
        $data = $this->parseJson($response);
        if (isset($data['keywords']) && is_string($data['keywords'])) {
            $data['keywords'] = array_map('trim', explode(',', $data['keywords']));
        }
        return $data;
    }

    protected function parseJson(?string $json): ?array
    {
        if (!$json) return null;
        $clean = preg_replace('/```json|```/', '', $json);
        return json_decode(trim($clean), true);
    }

    protected function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }

    public function failed(\Throwable $exception): void
    {
        $this->rawArticle->update(['status' => 'failed']);
        Log::error("Job failed for RawArticle: {$this->rawArticle->id}. Error: {$exception->getMessage()}");
    }
}
