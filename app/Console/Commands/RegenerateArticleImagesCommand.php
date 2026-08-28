<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\AI\SiliconFlowImageService;
use Illuminate\Console\Command;

class RegenerateArticleImagesCommand extends Command
{
    protected $signature = 'articles:regenerate-images 
                            {--id= : ID específico de un artículo para regenerar su imagen}
                            {--placeholders-only : Regenerar solo los artículos que tienen imágenes placeholder (fondo azul)}
                            {--all : Regenerar la portada de todos los artículos}
                            {--limit=50 : Límite de artículos a procesar}';

    protected $description = 'Regenera portadas contextuales de alta calidad con IA (SiliconFlow FLUX.1) para artículos';

    public function handle(SiliconFlowImageService $imageService): int
    {
        $id = $this->option('id');
        $placeholdersOnly = $this->option('placeholders-only');
        $all = $this->option('all');
        $limit = (int) $this->option('limit');

        if ($id) {
            $articles = Article::where('id', $id)->get();
        } elseif ($placeholdersOnly || (!$id && !$all)) {
            $articles = Article::where('image_url', 'LIKE', '%placeholder%')
                ->latest('id')
                ->limit($limit)
                ->get();
            $this->info("🔍 Buscando artículos con imágenes placeholder (encontrados: " . $articles->count() . ")...");
        } else {
            $articles = Article::latest('id')
                ->limit($limit)
                ->get();
        }

        if ($articles->isEmpty()) {
            $this->info('✅ No hay artículos pendientes de regeneración de imágenes.');
            return 0;
        }

        $this->info("🎨 Iniciando regeneración de imágenes con SiliconFlow (FLUX.1) para {$articles->count()} artículo(s)...");
        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($articles as $article) {
            $title = $article->getTranslation('title', 'es') ?: $article->getTranslation('title', 'en');
            $this->line("\n[#{$article->id}] {$title}");

            $result = $imageService->regenerateHeroForArticle($article);
            if ($result) {
                $this->info("   ✅ Portada generada y actualizada: {$article->image_url}");
                $success++;
            } else {
                $this->error("   ❌ Falló la generación para el artículo #{$article->id}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("🎉 Proceso finalizado: {$success} exitosas, {$failed} fallidas.");

        return 0;
    }
}
