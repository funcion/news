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
        Log::info("Processing RawArticle: {$this->rawArticle->id} with AI Gemini 3.");

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

        // Layer 4: Meta Generation (Gemini 3 Flash)
        $metadata = $this->generateMetadata($ai, $redacted);

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
            'slug' => Str::slug($redacted['title'] ?? $this->rawArticle->title),
            'content' => $redacted['content'],
            'excerpt' => $redacted['excerpt'] ?? Str::words(strip_tags($redacted['content']), 30),
            'author_id' => $author->id,
            'category_id' => $categoryId,
            'status' => 'draft',
            'meta_title' => $metadata['meta_title'] ?? null,
            'meta_description' => $metadata['meta_description'] ?? null,
            'meta_keywords' => $metadata['meta_keywords'] ?? null,
            'reading_time' => $this->calculateReadingTime($redacted['content']),
            'ai_metadata' => [
                'facts' => $classification['facts'] ?? [],
                'voice_style' => $author->voice_style,
                'origin_url' => $this->rawArticle->url,
                'model' => OpenRouterService::MODEL_GEMINI_3_FLASH,
                'detected_category' => $classification['category_name'] ?? null,
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

        $prompt = "Actúa como un experto editor periodístico.
        Analiza esta noticia cruda y determina si pertenece a alguna de las siguientes categorías temáticas válidas de nuestro ecosistema informativo.
        
        CATEGORÍAS VÁLIDAS: [{$categoriesList}]
        
        REGLAS:
        - Si la noticia encaja en alguna de las categorías de arriba, márcala como relevante (is_relevant: true) y extrae sus hechos clave.
        - Si la noticia NO pertenece claramente a ninguna de esas categorías, márcala como irrelevante (is_relevant: false) para que sea ignorada.
        
        NOTICIA:
        Título: {$this->rawArticle->title}
        Contenido: {$this->rawArticle->content}
        
        Responde estrictamente en formato JSON:
        {
            \"is_relevant\": bool,
            \"category_name\": \"Nombre exacto de la categoría seleccionada (si es relevante)\",
            \"importance\": int (1-10),
            \"facts\": [array of strings]
        }";

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_3_FLASH);

        return $this->parseJson($response);
    }

    protected function redactContent(OpenRouterService $ai, array $classification, Author $author): ?array
    {
        $voiceInstructions = [
            'El Analista' => "Técnico, basado en datos duros y análisis profundo.",
            'El Divulgador' => "Accesible, claro y pedagógico para el público general.",
            'El Cronista' => "Narrativo, estilo storytelling con impacto social.",
            'El Crítico' => "Opinión fundamentada, honesto y directo sobre errores o aciertos.",
        ];

        $instructions = $voiceInstructions[$author->voice_style] ?? "Neutral e informativo.";
        
        $isSeed = $classification['is_seed'] ?? false;

        if ($isSeed) {
            $prompt = "Actúa como {$author->name}, cuya voz es: {$author->voice_style}.
        Instrucciones de estilo: {$instructions}
        
        Has recibido una 'semilla' o idea para una noticia tecnológica. Tu tarea es usar tu base de conocimientos para investigar sobre este tema y redactar un artículo de noticias SEO-optimizado, original, completo y humanizado.
        
        TEMA / SEMILLA:
        - Título: {$this->rawArticle->title}
        - Notas adicionales: {$this->rawArticle->summary}
        
        Debes expandir la idea, añadir contexto de la industria y generar un artículo con carnita periodística.
        Usa un formato HTML limpio (solo h2, p, strong, li). No uses etiquetas html, head o body.
        Evita sonar robótico. Sé atractivo pero profesional.
        
        Responde estrictamente en formato JSON:
        {
            \"title\": \"Título atractivo y mejorado\",
            \"excerpt\": \"Resumen descriptivo de 2 líneas\",
            \"content\": \"Contenido HTML completo (al menos 3 o 4 párrafos)\"
        }";
        } else {
            $prompt = "Actúa como {$author->name}, cuya voz es: {$author->voice_style}.
        Instrucciones de estilo: {$instructions}
        
        Basado en los siguientes hechos extraídos de una noticia reciente, redacta un artículo de noticias SEO-optimizado y humanizado.
        Usa un formato HTML limpio (solo h2, p, strong, li). No uses etiquetas html, head o body.
        Evita sonar robótico. Sé atractivo pero profesional.
        
        HECHOS:
        - " . implode("\n- ", $classification['facts']) . "
        
        Responde estrictamente en formato JSON:
        {
            \"title\": \"Título atractivo\",
            \"excerpt\": \"Resumen de 2 líneas\",
            \"content\": \"Contenido HTML completo\"
        }";
        }

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_3_FLASH);

        return $this->parseJson($response);
    }

    protected function generateMetadata(OpenRouterService $ai, array $redacted): array
    {
        $prompt = "Basado en este título y contenido, genera metadatos SEO.
        
        Título: {$redacted['title']}
        Contenido: " . Str::limit($redacted['content'], 500) . "
        
        Responde estrictamente en formato JSON:
        {
            \"meta_title\": \"Máximo 60 carac\",
            \"meta_description\": \"Máximo 160 carac\",
            \"meta_keywords\": \"separado, por, comas\"
        }";

        $response = $ai->complete([
            ['role' => 'user', 'content' => $prompt]
        ], OpenRouterService::MODEL_GEMINI_3_FLASH);

        return $this->parseJson($response) ?? [];
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
