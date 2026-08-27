<?php

namespace App\Services\AI;

use App\Models\Tag;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TagGeneratorService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate concise, high-density tags using a closed-loop taxonomy catalog.
     * Returns an array of normalized tag names (max 3).
     */
    public function generateTags(string $content): array
    {
        // 1. Fetch the existing Master Tags Catalog from database
        $catalogTags = Tag::where('is_indexable', true)
            ->pluck('name')
            ->map(fn ($n) => is_array($n) ? ($n['en'] ?? reset($n)) : (json_decode($n, true)['en'] ?? $n))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Fallback default catalog if DB is empty
        if (empty($catalogTags)) {
            $catalogTags = [
                'Artificial Intelligence', 'Large Language Models', 'OpenAI', 'Google', 'Meta',
                'Microsoft', 'NVIDIA', 'Apple', 'Anthropic', 'AI Agents', 'Robotics & Automation',
                'Cybersecurity', 'Vulnerabilities & Exploits', 'Ransomware & Malware',
                'Data Privacy & Protection', 'Cloud Computing', 'Hardware & Semiconductors',
                'Software Engineering', 'DevOps & CI/CD', 'Open Source', 'Databases & Storage',
                'Science & Innovation', 'Startups & Venture Capital'
            ];
        }

        $catalogListString = implode(', ', array_slice($catalogTags, 0, 50));

        $prompt = "You are the Senior Lead Taxonomist for a premier tech and AI news publication.\n"
                . "Your goal is to classify the article below by selecting 2 to 3 tags from our Official Master Taxonomy Catalog:\n"
                . "OFFICIAL CATALOG: [" . $catalogListString . "]\n\n"
                . "STRICT TAXONOMY RULES:\n"
                . "1. You MUST prioritize selecting 2 to 3 tags directly from the OFFICIAL CATALOG.\n"
                . "2. You may ONLY propose a new tag if the article is centered around a specific major tech company (e.g. 'DeepSeek', 'Mistral AI', 'TSMC', 'Groq') not in the list.\n"
                . "3. DO NOT invent generic phrases or sentence tags.\n"
                . "4. Output ONLY a comma-separated list of the 2-3 tags in lowercase. No explanation, no quotes, no markdown.\n\n"
                . "Article Content:\n" . Str::limit(strip_tags($content), 3000);

        try {
            $response = $this->ai->complete(
                [['role' => 'user', 'content' => $prompt]],
                config('ai_models.default'),
                ['max_tokens' => 150]
            );

            if (!$response) {
                return $this->fallbackTagsFromContent($content, $catalogTags);
            }

            $extracted = $this->normalizeTags(explode(',', $response));

            return !empty($extracted) ? $extracted : $this->fallbackTagsFromContent($content, $catalogTags);

        } catch (\Exception $e) {
            Log::error('Error in TagGeneratorService: ' . $e->getMessage());
            return $this->fallbackTagsFromContent($content, $catalogTags);
        }
    }

    /**
     * Sync extracted tags to an article with a strict limit of 3 tags.
     */
        /**
     * Master Taxonomy mapping dictionary (EN and ES slugs and names)
     */
    public static array $taxonomyMap = [
        'openai' => ['en' => 'OpenAI', 'es' => 'OpenAI', 'slug_en' => 'openai', 'slug_es' => 'openai'],
        'google' => ['en' => 'Google', 'es' => 'Google', 'slug_en' => 'google', 'slug_es' => 'google'],
        'meta' => ['en' => 'Meta', 'es' => 'Meta', 'slug_en' => 'meta', 'slug_es' => 'meta'],
        'microsoft' => ['en' => 'Microsoft', 'es' => 'Microsoft', 'slug_en' => 'microsoft', 'slug_es' => 'microsoft'],
        'nvidia' => ['en' => 'NVIDIA', 'es' => 'NVIDIA', 'slug_en' => 'nvidia', 'slug_es' => 'nvidia'],
        'apple' => ['en' => 'Apple', 'es' => 'Apple', 'slug_en' => 'apple', 'slug_es' => 'apple'],
        'anthropic' => ['en' => 'Anthropic', 'es' => 'Anthropic', 'slug_en' => 'anthropic', 'slug_es' => 'anthropic'],
        'amazon' => ['en' => 'Amazon', 'es' => 'Amazon', 'slug_en' => 'amazon', 'slug_es' => 'amazon'],
        'tesla' => ['en' => 'Tesla', 'es' => 'Tesla', 'slug_en' => 'tesla', 'slug_es' => 'tesla'],
        'artificial-intelligence' => ['en' => 'Artificial Intelligence', 'es' => 'Inteligencia Artificial', 'slug_en' => 'artificial-intelligence', 'slug_es' => 'inteligencia-artificial'],
        'large-language-models' => ['en' => 'Large Language Models', 'es' => 'Modelos de Lenguaje', 'slug_en' => 'large-language-models', 'slug_es' => 'modelos-de-lenguaje'],
        'ai-agents' => ['en' => 'AI Agents', 'es' => 'Agentes de IA', 'slug_en' => 'ai-agents', 'slug_es' => 'agentes-de-ia'],
        'computer-vision' => ['en' => 'Computer Vision', 'es' => 'Visión Computacional', 'slug_en' => 'computer-vision', 'slug_es' => 'vision-computacional'],
        'robotics-automation' => ['en' => 'Robotics & Automation', 'es' => 'Robótica & Automatización', 'slug_en' => 'robotics-automation', 'slug_es' => 'robotica-automatizacion'],
        'reinforcement-learning' => ['en' => 'Reinforcement Learning', 'es' => 'Aprendizaje por Refuerzo', 'slug_en' => 'reinforcement-learning', 'slug_es' => 'aprendizaje-por-refuerzo'],
        'ai-ethics-safety' => ['en' => 'AI Ethics & Safety', 'es' => 'Ética & Seguridad en IA', 'slug_en' => 'ai-ethics-safety', 'slug_es' => 'etica-seguridad-ia'],
        'ai-regulation-policy' => ['en' => 'AI Regulation & Policy', 'es' => 'Regulación & Políticas de IA', 'slug_en' => 'ai-regulation-policy', 'slug_es' => 'regulacion-politicas-ia'],
        'cybersecurity' => ['en' => 'Cybersecurity', 'es' => 'Ciberseguridad', 'slug_en' => 'cybersecurity', 'slug_es' => 'ciberseguridad'],
        'vulnerabilities-exploits' => ['en' => 'Vulnerabilities & Exploits', 'es' => 'Vulnerabilidades & Exploits', 'slug_en' => 'vulnerabilities-exploits', 'slug_es' => 'vulnerabilidades-exploits'],
        'ransomware-malware' => ['en' => 'Ransomware & Malware', 'es' => 'Ransomware & Malware', 'slug_en' => 'ransomware-malware', 'slug_es' => 'ransomware-malware'],
        'data-privacy-protection' => ['en' => 'Data Privacy & Protection', 'es' => 'Privacidad & Protección de Datos', 'slug_en' => 'data-privacy-protection', 'slug_es' => 'privacidad-proteccion-datos'],
        'cloud-computing' => ['en' => 'Cloud Computing', 'es' => 'Computación en la Nube', 'slug_en' => 'cloud-computing', 'slug_es' => 'computacion-en-la-nube'],
        'digital-infrastructure' => ['en' => 'Digital Infrastructure', 'es' => 'Infraestructura Digital', 'slug_en' => 'digital-infrastructure', 'slug_es' => 'infraestructura-digital'],
        'hardware-semiconductors' => ['en' => 'Hardware & Semiconductors', 'es' => 'Hardware & Semiconductores', 'slug_en' => 'hardware-semiconductors', 'slug_es' => 'hardware-semiconductores'],
        'software-engineering' => ['en' => 'Software Engineering', 'es' => 'Ingeniería de Software', 'slug_en' => 'software-engineering', 'slug_es' => 'ingenieria-de-software'],
        'devops-cicd' => ['en' => 'DevOps & CI/CD', 'es' => 'DevOps & CI/CD', 'slug_en' => 'devops-cicd', 'slug_es' => 'devops-cicd'],
        'open-source' => ['en' => 'Open Source', 'es' => 'Código Abierto', 'slug_en' => 'open-source', 'slug_es' => 'codigo-abierto'],
        'databases-storage' => ['en' => 'Databases & Storage', 'es' => 'Bases de Datos & Almacenamiento', 'slug_en' => 'databases-storage', 'slug_es' => 'bases-de-datos-almacenamiento'],
        'web-development' => ['en' => 'Web Development', 'es' => 'Desarrollo Web', 'slug_en' => 'web-development', 'slug_es' => 'desarrollo-web'],
        'apis-microservices' => ['en' => 'APIs & Microservices', 'es' => 'APIs & Microservicios', 'slug_en' => 'apis-microservices', 'slug_es' => 'apis-microservicios'],
        'science-innovation' => ['en' => 'Science & Innovation', 'es' => 'Ciencia & Innovación', 'slug_en' => 'science-innovation', 'slug_es' => 'ciencia-innovacion'],
        'startups-venture-capital' => ['en' => 'Startups & Venture Capital', 'es' => 'Startups & Capital de Riesgo', 'slug_en' => 'startups-venture-capital', 'slug_es' => 'startups-capital-riesgo'],
        'e-commerce-digital-economy' => ['en' => 'E-Commerce & Digital Economy', 'es' => 'Comercio Electrónico & Economía Digital', 'slug_en' => 'e-commerce-digital-economy', 'slug_es' => 'comercio-electronico-economia-digital'],
    ];

    /**
     * Sync extracted tags to an article with a strict limit of 3 tags.
     */
    public function syncTagsToArticle(Article $article, array $tagNames): void
    {
        if (empty($tagNames)) {
            return;
        }

        $tagNames = array_slice($tagNames, 0, 3);
        $tagIds = [];

        foreach ($tagNames as $name) {
            $normalizedKey = Str::slug($name);
            if (empty($normalizedKey)) {
                continue;
            }

            $meta = self::$taxonomyMap[$normalizedKey] ?? null;

            if ($meta) {
                $tag = Tag::where('slug_en', $meta['slug_en'])
                    ->orWhere('slug_es', $meta['slug_es'])
                    ->orWhere('slug', $normalizedKey)
                    ->first();

                if (!$tag) {
                    $tag = Tag::create([
                        'slug' => $meta['slug_en'],
                        'slug_en' => $meta['slug_en'],
                        'slug_es' => $meta['slug_es'],
                        'name' => ['en' => $meta['en'], 'es' => $meta['es']],
                        'description' => [
                            'en' => "Latest news and analysis on " . $meta['en'],
                            'es' => "Últimas noticias y análisis sobre " . $meta['es'],
                        ],
                        'is_indexable' => true,
                        'is_followable' => true,
                    ]);
                }
            } else {
                $slugEn = $normalizedKey;
                $slugEs = $normalizedKey;
                $tag = Tag::where('slug', $normalizedKey)
                    ->orWhere('slug_en', $slugEn)
                    ->orWhere('slug_es', $slugEs)
                    ->first();

                if (!$tag) {
                    $tag = Tag::create([
                        'slug' => $slugEn,
                        'slug_en' => $slugEn,
                        'slug_es' => $slugEs,
                        'name' => [
                            'en' => ucwords(str_replace('-', ' ', $name)),
                            'es' => ucwords(str_replace('-', ' ', $name)),
                        ],
                        'description' => [
                            'en' => "Latest news and analysis on " . ucwords(str_replace('-', ' ', $name)),
                            'es' => "Últimas noticias y análisis sobre " . ucwords(str_replace('-', ' ', $name)),
                        ],
                        'is_indexable' => true,
                        'is_followable' => true,
                    ]);
                }
            }

            $tagIds[$tag->id] = ['relevance_score' => 100];
        }

        $article->tags()->sync($tagIds);

        // Update tag counts
        foreach (array_keys($tagIds) as $id) {
            $t = Tag::find($id);
            if ($t) {
                $t->article_count = $t->articles()->count();
                $t->saveQuietly();
            }
        }
    }

    private function normalizeTags(array $tags): array
    {
        return collect($tags)
            ->map(function ($tag) {
                $tag = strtolower(trim($tag));
                $tag = preg_replace('/[^a-z0-9\s-]/', '', $tag);
                $tag = preg_replace('/\s+/', ' ', $tag);
                return $tag;
            })
            ->filter(function ($tag) {
                return strlen($tag) >= 2 && strlen($tag) <= 40;
            })
            ->unique()
            ->take(3)
            ->values()
            ->toArray();
    }

    private function fallbackTagsFromContent(string $content, array $catalog): array
    {
        $contentLower = strtolower($content);
        $matched = [];

        foreach ($catalog as $catTag) {
            $tagLower = strtolower($catTag);
            if (str_contains($contentLower, $tagLower)) {
                $matched[] = $tagLower;
                if (count($matched) >= 3) {
                    break;
                }
            }
        }

        return !empty($matched) ? $matched : ['artificial-intelligence'];
    }
}