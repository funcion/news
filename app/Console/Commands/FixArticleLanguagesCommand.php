<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\AI\OpenRouterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixArticleLanguagesCommand extends Command
{
    protected $signature = 'articles:fix-languages {--id= : Fix specific article by ID} {--dry-run : Only detect without modifying}';
    protected $description = 'Detect and fix language mismatches (e.g. Spanish text in content_en or English text in content_es)';

    protected array $esStopwords = [
        'de', 'la', 'que', 'el', 'en', 'y', 'a', 'los', 'del', 'se', 'las', 'por', 'un', 'para', 'con', 'no',
        'una', 'su', 'al', 'lo', 'como', 'más', 'mas', 'pero', 'sus', 'le', 'ya', 'o', 'este', 'sí', 'si',
        'porque', 'esta', 'entre', 'cuando', 'muy', 'sin', 'sobre', 'también', 'tambien', 'me', 'hasta', 'hay',
        'donde', 'quien', 'desde', 'todo', 'nos', 'durante', 'todos', 'uno', 'les', 'ni', 'contra', 'otros',
        'ese', 'eso', 'ante', 'ellos', 'e', 'esto', 'mí', 'mi', 'antes', 'algunos', 'qué', 'unos', 'otro',
        'otras', 'otra', 'él', 'tanto', 'esa', 'estos', 'mucho', 'quienes', 'nada', 'muchos', 'cual', 'sea',
        'poco', 'ella', 'estar', 'haber', 'estas', 'estaba', 'estamos', 'algunas', 'algo', 'nosotros', 'según',
        'además', 'después', 'través', 'año', 'años', 'cuenta', 'forma', 'parte', 'llegó', 'crear', 'nueva',
        'nuevo', 'nuevos', 'nuevas', 'sistema', 'último', 'últimos', 'primer', 'primera', 'momento', 'mayor'
    ];

    protected array $enStopwords = [
        'the', 'and', 'of', 'to', 'a', 'in', 'that', 'is', 'was', 'for', 'on', 'are', 'as', 'with', 'his',
        'they', 'at', 'be', 'this', 'have', 'from', 'or', 'one', 'had', 'by', 'word', 'but', 'not', 'what',
        'all', 'were', 'we', 'when', 'your', 'can', 'said', 'there', 'use', 'an', 'each', 'which', 'she',
        'do', 'how', 'their', 'if', 'will', 'up', 'other', 'about', 'out', 'many', 'then', 'them', 'these',
        'so', 'some', 'her', 'would', 'make', 'like', 'him', 'into', 'time', 'has', 'look', 'two', 'more',
        'write', 'go', 'see', 'number', 'way', 'could', 'people', 'than', 'first', 'water', 'been', 'call',
        'who', 'its', 'now', 'find', 'also', 'new', 'after', 'state', 'only', 'year', 'years', 'over', 'most',
        'such', 'where', 'both', 'between', 'during', 'through', 'under', 'while', 'because', 'should', 'well'
    ];

    public function handle(OpenRouterService $ai): int
    {
        $specificId = $this->option('id');
        $isDryRun = $this->option('dry-run');

        $query = Article::query();
        if ($specificId) {
            $query->where('id', $specificId);
        } else {
            $query->orderBy('id', 'desc');
        }

        $articles = $query->get();
        $this->info("Scanning {$articles->count()} articles for language integrity...");

        $fixedCount = 0;

        foreach ($articles as $article) {
            $contentEn = $article->getTranslation('content', 'en') ?: '';
            $contentEs = $article->getTranslation('content', 'es') ?: '';
            $titleEn   = $article->getTranslation('title', 'en') ?: '';
            $titleEs   = $article->getTranslation('title', 'es') ?: '';

            $needsEnFix = $this->isSpanishContent($contentEn);
            $needsEsFix = $this->isEnglishContent($contentEs);

            if (!$needsEnFix && !$needsEsFix) {
                continue;
            }

            $this->warn("⚠️ Article #{$article->id} ('{$titleEn}') has language mismatch:");
            if ($needsEnFix) {
                $this->line("   - content_en is written in Spanish instead of English!");
            }
            if ($needsEsFix) {
                $this->line("   - content_es is written in English instead of Spanish!");
            }

            if ($isDryRun) {
                continue;
            }

            // Fix English version
            if ($needsEnFix) {
                $this->info("   🔄 Translating and rewriting content_en into English with AI...");
                $sourceText = !empty($contentEs) ? $contentEs : $contentEn;
                $prompt = <<<PROMPT
You are a senior tech journalism editor for Glodaxia.
Translate and rewrite the following Spanish article into 100% native, fluent, and rigorous English journalism.

CRITICAL RULES:
1. The output MUST be 100% in English. Absolutely zero Spanish words.
2. Preserve all HTML tags (<p>, <h2>, <ul>, <li>, <strong>, etc.) and [IMAGE_N] placeholders exactly intact.
3. Keep the same journalistic depth, facts, and structure.
4. Output ONLY the resulting HTML content (no markdown fences, no extra preamble).

SPANISH SOURCE CONTENT:
{$sourceText}
PROMPT;

                try {
                    $englishHtml = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
                    $englishHtml = trim(preg_replace('/^```(?:html)?\s*|\s*```$/i', '', trim($englishHtml)));
                    $article->setTranslation('content', 'en', $englishHtml);
                    $this->info("   ✅ content_en successfully updated with clean English.");
                } catch (\Throwable $e) {
                    $this->error("   ❌ Failed to translate content_en: " . $e->getMessage());
                }
            }

            // Fix Spanish version
            if ($needsEsFix) {
                $this->info("   🔄 Translating and rewriting content_es into Spanish with AI...");
                $sourceText = !empty($contentEn) ? $contentEn : $contentEs;
                $prompt = <<<PROMPT
Eres un editor senior de periodismo tecnológico para Glodaxia.
Traduce y redacta el siguiente artículo en inglés a un español 100% nativo, fluido y riguroso.

REGLAS CRÍTICAS:
1. La salida DEBE estar 100% en Español. Cero palabras en inglés (salvo nombres propios y marcas).
2. Conserva todas las etiquetas HTML (<p>, <h2>, <ul>, <li>, <strong>, etc.) y marcadores de imagen [IMAGE_N] exactamente intactos.
3. Mantén la misma profundidad periodística y estructura.
4. Devuelve ÚNICAMENTE el contenido HTML resultante (sin bloques de código markdown ni texto adicional).

CONTENIDO EN INGLÉS:
{$sourceText}
PROMPT;

                try {
                    $spanishHtml = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
                    $spanishHtml = trim(preg_replace('/^```(?:html)?\s*|\s*```$/i', '', trim($spanishHtml)));
                    $article->setTranslation('content', 'es', $spanishHtml);
                    $this->info("   ✅ content_es successfully updated with clean Spanish.");
                } catch (\Throwable $e) {
                    $this->error("   ❌ Failed to translate content_es: " . $e->getMessage());
                }
            }

            $article->save();
            $fixedCount++;
        }

        $this->info("Language check complete. Total repaired: {$fixedCount} articles.");
        return 0;
    }

    protected function isSpanishContent(string $html): bool
    {
        if (empty(trim($html))) return false;
        $esCount = $this->countStopwords($html, $this->esStopwords);
        $enCount = $this->countStopwords($html, $this->enStopwords);
        return ($esCount > $enCount && $esCount >= 15) || ($esCount >= 20 && $enCount < 10);
    }

    protected function isEnglishContent(string $html): bool
    {
        if (empty(trim($html))) return false;
        $enCount = $this->countStopwords($html, $this->enStopwords);
        $esCount = $this->countStopwords($html, $this->esStopwords);
        return ($enCount > $esCount && $enCount >= 15) || ($enCount >= 20 && $esCount < 10);
    }

    protected function countStopwords(string $text, array $stopwords): int
    {
        $cleaned = mb_strtolower(strip_tags($text));
        $words = preg_split('/[^\p{L}\p{N}]+/u', $cleaned, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($words)) return 0;
        $swMap = array_flip($stopwords);
        $count = 0;
        foreach ($words as $w) {
            if (isset($swMap[$w])) {
                $count++;
            }
        }
        return $count;
    }
}