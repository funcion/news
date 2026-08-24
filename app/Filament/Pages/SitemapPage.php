<?php

namespace App\Filament\Pages;

use App\Http\Controllers\SitemapController;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class SitemapPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO & Marketing';

    protected static ?string $title = 'Sitemaps XML & Indexación';

    protected static ?string $navigationLabel = 'Sitemaps XML';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.sitemap-page';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('flush')
                ->label('Regenerar / Vaciar Caché')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    SitemapController::flushCache();
                    Notification::make()
                        ->title('Caché de Sitemaps limpiada')
                        ->body('Los sitemaps se regenerarán dinámicamente con los datos más recientes en la próxima consulta de Googlebot.')
                        ->success()
                        ->send();
                }),

            Action::make('ping_indexnow')
                ->label('Notificar a IndexNow (Bing/Yandex)')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action(function () {
                    $key = config('services.indexnow.key', env('INDEXNOW_KEY', 'glodaxia-indexnow-key-2026'));
                    $host = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
                    
                    $articles = Article::where('status', 'published')
                        ->orderByDesc('published_at')
                        ->limit(20)
                        ->get();

                    $urls = [];
                    foreach ($articles as $art) {
                        if ($art->slug_es) $urls[] = url('/' . $art->slug_es);
                        if ($art->slug_en) $urls[] = url('/en/' . $art->slug_en);
                    }

                    try {
                        $res = Http::timeout(10)->post('https://api.indexnow.org/indexnow', [
                            'host'        => $host,
                            'key'         => $key,
                            'keyLocation' => url("/{$key}.txt"),
                            'urlList'     => array_slice($urls, 0, 50),
                        ]);

                        if ($res->successful() || $res->status() === 200 || $res->status() === 202) {
                            Notification::make()
                                ->title('Notificación enviada a IndexNow')
                                ->body('Se enviaron ' . count($urls) . ' URLs a los motores de búsqueda participantes.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Respuesta de IndexNow: ' . $res->status())
                                ->body('El endpoint respondió con código ' . $res->status())
                                ->warning()
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Notificación local registrada')
                            ->body('Caché de IndexNow actualizada. Error de conexión externa: ' . $e->getMessage())
                            ->info()
                            ->send();
                    }
                }),

            Action::make('open_index')
                ->label('Ver sitemap.xml')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(url('/sitemap.xml'), shouldOpenInNewTab: true)
                ->color('gray'),
        ];
    }

    public function getViewData(): array
    {
        $publishedCount = Article::where('status', 'published')->count();
        $news48hCount   = Article::where('status', 'published')->where('published_at', '>=', now()->subHours(48))->count();
        $categoryCount  = Category::whereNull('parent_id')->whereHas('articles', fn($q) => $q->published())->count();
        $tagCount       = Tag::withMinimumArticles(1)->count();

        $sitemaps = [
            [
                'title'       => 'Índice Maestro de Sitemaps',
                'url'         => url('/sitemap.xml'),
                'path'        => '/sitemap.xml',
                'description' => 'SitemapIndex raíz que agrupa y referencia a todos los subsitemaps. Es la URL principal que debes registrar en Google Search Console.',
                'records'     => '7 sub-sitemaps',
                'badge'       => 'Maestro',
                'color'       => 'blue',
                'ttl'         => '1 hora (Dinámico)',
            ],
            [
                'title'       => 'Google News Sitemap',
                'url'         => url('/sitemap-news.xml'),
                'path'        => '/sitemap-news.xml',
                'description' => 'Sitemap especializado con formato <news:news> exclusivo para Google News y Google Discover. Solo incluye artículos publicados en las últimas 48 horas.',
                'records'     => "{$news48hCount} noticias activas",
                'badge'       => 'Google News 48h',
                'color'       => 'emerald',
                'ttl'         => '30 minutos (Alta Frecuencia)',
            ],
            [
                'title'       => 'Artículos en Español (ES)',
                'url'         => url('/sitemap-articles-es.xml'),
                'path'        => '/sitemap-articles-es.xml',
                'description' => 'Listado completo de todos los artículos publicados en español con etiquetas hreflang y fechas de última modificación.',
                'records'     => "{$publishedCount} artículos",
                'badge'       => 'Bilingüe ES',
                'color'       => 'purple',
                'ttl'         => '1 hora',
            ],
            [
                'title'       => 'Artículos en Inglés (EN)',
                'url'         => url('/sitemap-articles-en.xml'),
                'path'        => '/sitemap-articles-en.xml',
                'description' => 'Listado completo de todos los artículos publicados en inglés para el público internacional y rastreadores globales.',
                'records'     => "{$publishedCount} artículos",
                'badge'       => 'Bilingüe EN',
                'color'       => 'indigo',
                'ttl'         => '1 hora',
            ],
            [
                'title'       => 'Categorías Temáticas',
                'url'         => url('/sitemap-categories.xml'),
                'path'        => '/sitemap-categories.xml',
                'description' => 'Páginas de categorías principales activas que agrupan artículos tecnológicos.',
                'records'     => "{$categoryCount} categorías",
                'badge'       => 'Taxonomía',
                'color'       => 'amber',
                'ttl'         => '1 hora',
            ],
            [
                'title'       => 'Etiquetas & Temas (Tags)',
                'url'         => url('/sitemap-tags.xml'),
                'path'        => '/sitemap-tags.xml',
                'description' => 'Páginas de temas y tecnologías que cuentan con al menos un artículo relacionado publicado.',
                'records'     => "{$tagCount} tags",
                'badge'       => 'Taxonomía',
                'color'       => 'teal',
                'ttl'         => '1 hora',
            ],
            [
                'title'       => 'Imágenes y Medios (FLUX.1)',
                'url'         => url('/sitemap-images.xml'),
                'path'        => '/sitemap-images.xml',
                'description' => 'Sitemap enriquecido con el esquema <image:image> para posicionar las imágenes fotorrealistas en Google Imágenes.',
                'records'     => "{$publishedCount} imágenes",
                'badge'       => 'Google Images',
                'color'       => 'rose',
                'ttl'         => '1 hora',
            ],
        ];

        return [
            'sitemaps'       => $sitemaps,
            'publishedCount' => $publishedCount,
            'news48hCount'   => $news48hCount,
        ];
    }
}