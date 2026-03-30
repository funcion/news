<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScraperService
{
    /**
     * Procesa una fuente de tipo Scraping.
     * Lee la URL, extrae el contenido con Jina y crea un RawArticle.
     */
    public function fetchSource(\App\Models\Source $source): int
    {
        $content = $this->scrape($source->url);

        if (!$content) {
            $source->increment('score', -5);
            return 0;
        }

        // Generamos un título temporal si no existe o usamos el nombre de la fuente
        // En una fase posterior, podríamos pedirle a la IA que extraiga el título real del Markdown
        $title = $source->name . " - " . now()->format('Y-m-d H:i');
        
        // Buscamos si hay un título obvio en el Markdown (primera línea con #)
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            $title = trim($matches[1]);
        }

        $hash = hash('sha256', $title . $source->url . substr($content, 0, 100));

        if (\App\Models\RawArticle::where('hash', $hash)->exists()) {
            return 0;
        }

        \App\Models\RawArticle::create([
            'source_id' => $source->id,
            'title' => $title,
            'url' => $source->url,
            'content' => $content,
            'summary' => Str::limit(strip_tags($content), 200),
            'author' => 'Scraper',
            'published_at' => now(),
            'hash' => $hash,
            'status' => 'pending',
            'metadata' => [
                'engine' => 'jina_reader',
                'scraped_at' => now()->toDateTimeString(),
            ],
        ]);

        $source->update(['last_fetched_at' => now()]);
        $source->increment('score', 1);

        return 1;
    }

    /**
     * Extrae el contenido de una URL usando Jina Reader (r.jina.ai).
     * Devuelve el contenido en formato Markdown optimizado para LLMs.
     */
    public function scrape(string $url): ?string
    {
        try {
            $jinaUrl = "https://r.jina.ai/" . $url;
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Return-Format' => 'markdown', // Aseguramos que nos de Markdown
                ])
                ->get($jinaUrl);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error("Error de Scraping con Jina Reader en URL: {$url}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error("Excepción durante el Scraping de URL: {$url}", [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }
}
