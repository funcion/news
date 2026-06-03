<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Sitemap Index — /sitemap.xml references all sub-sitemaps.
     * Regenerates automatically when cache is flushed after article publish.
     */
    public function index()
    {
        $xml = Cache::remember('sitemap.index', 3600, function () {
            $subSitemaps = [
                ['loc' => url('/sitemap-articles-en.xml'),  'lastmod' => $this->latestArticleDate('en')],
                ['loc' => url('/sitemap-articles-es.xml'),  'lastmod' => $this->latestArticleDate('es')],
                ['loc' => url('/sitemap-categories.xml'),   'lastmod' => now()->toAtomString()],
                ['loc' => url('/sitemap-tags.xml'),         'lastmod' => now()->toAtomString()],
                ['loc' => url('/sitemap-news.xml'),         'lastmod' => now()->toAtomString()],
                ['loc' => url('/sitemap-images.xml'),       'lastmod' => $this->latestArticleDate('en')],
            ];

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($subSitemaps as $s) {
                $xml .= "  <sitemap>\n";
                $xml .= "    <loc>{$s['loc']}</loc>\n";
                $xml .= "    <lastmod>{$s['lastmod']}</lastmod>\n";
                $xml .= "  </sitemap>\n";
            }
            $xml .= '</sitemapindex>';

            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Articles EN — with hreflang alternate links to ES.
     */
    public function articlesEn()
    {
        $xml = Cache::remember('sitemap.articles.en', 3600, function () {
            $articles = Article::where('status', 'published')
                ->whereNotNull('slug_en')
                ->orderByDesc('published_at')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
            $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

            // Home EN
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . url('/') . "</loc>\n";
            $xml .= "    <changefreq>hourly</changefreq>\n";
            $xml .= "    <priority>1.0</priority>\n";
            $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . url('/') . '" />' . "\n";
            $xml .= '    <xhtml:link rel="alternate" hreflang="es" href="' . url('/es') . '" />' . "\n";
            $xml .= "  </url>\n";

            foreach ($articles as $article) {
                $locEn = url('/' . $article->slug_en);
                $locEs = $article->slug_es ? url('/es/' . $article->slug_es) : null;
                $lastmod = $article->updated_at->format('Y-m-d');

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$locEn}</loc>\n";
                $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . $locEn . '" />' . "\n";
                if ($locEs) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="es" href="' . $locEs . '" />' . "\n";
                }
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . $locEn . '" />' . "\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Articles ES — with hreflang alternate links to EN.
     */
    public function articlesEs()
    {
        $xml = Cache::remember('sitemap.articles.es', 3600, function () {
            $articles = Article::where('status', 'published')
                ->whereNotNull('slug_es')
                ->orderByDesc('published_at')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
            $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

            // Home ES
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . url('/es') . "</loc>\n";
            $xml .= "    <changefreq>hourly</changefreq>\n";
            $xml .= "    <priority>1.0</priority>\n";
            $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . url('/') . '" />' . "\n";
            $xml .= '    <xhtml:link rel="alternate" hreflang="es" href="' . url('/es') . '" />' . "\n";
            $xml .= "  </url>\n";

            foreach ($articles as $article) {
                $locEs = url('/es/' . $article->slug_es);
                $locEn = $article->slug_en ? url('/' . $article->slug_en) : null;
                $lastmod = $article->updated_at->format('Y-m-d');

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$locEs}</loc>\n";
                $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= '    <xhtml:link rel="alternate" hreflang="es" href="' . $locEs . '" />' . "\n";
                if ($locEn) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . $locEn . '" />' . "\n";
                }
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . ($locEn ?? $locEs) . '" />' . "\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Categories sitemap.
     */
    public function categories()
    {
        $xml = Cache::remember('sitemap.categories', 3600, function () {
            $categories = Category::where('is_active', true)->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($categories as $cat) {
                $slugEn = $cat->slug_en ?? ($cat->getTranslation('name', 'en') ? Str::slug($cat->getTranslation('name', 'en')) : null);
                $slugEs = $cat->slug_es ?? ($cat->getTranslation('name', 'es') ? Str::slug($cat->getTranslation('name', 'es')) : null);

                if ($slugEn) {
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>" . url('/' . $slugEn) . "</loc>\n";
                    $xml .= "    <changefreq>daily</changefreq>\n";
                    $xml .= "    <priority>0.7</priority>\n";
                    $xml .= "  </url>\n";
                }
                if ($slugEs) {
                    $xml .= "  <url>\n";
                    $xml .= "    <loc>" . url('/es/' . $slugEs) . "</loc>\n";
                    $xml .= "    <changefreq>daily</changefreq>\n";
                    $xml .= "    <priority>0.7</priority>\n";
                    $xml .= "  </url>\n";
                }
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Tags sitemap.
     */
    public function tags()
    {
        $xml = Cache::remember('sitemap.tags', 3600, function () {
            $tags = Tag::where('article_count', '>', 0)
                ->orderByDesc('article_count')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($tags as $tag) {
                if (!$tag->slug) continue;
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . url('/tag/' . $tag->slug) . "</loc>\n";
                $xml .= "    <lastmod>" . $tag->updated_at->format('Y-m-d') . "</lastmod>\n";
                $xml .= "    <changefreq>daily</changefreq>\n";
                $xml .= "    <priority>0.6</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Google News sitemap — only articles from the last 48 hours.
     */
    public function news()
    {
        $xml = Cache::remember('sitemap.news', 1800, function () {
            $articles = Article::where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '>=', now()->subDays(2))
                ->orderByDesc('published_at')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
            $xml .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

            foreach ($articles as $article) {
                $loc = url('/' . $article->slug_en);
                $title = htmlspecialchars($article->getTranslation('title', 'en'), ENT_XML1);
                $pubDate = $article->published_at->format('Y-m-d\TH:i:sP');

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";
                $xml .= "    <news:news>\n";
                $xml .= "      <news:publication>\n";
                $xml .= "        <news:name>Glodaxia</news:name>\n";
                $xml .= "        <news:language>en</news:language>\n";
                $xml .= "      </news:publication>\n";
                $xml .= "      <news:publication_date>{$pubDate}</news:publication_date>\n";
                $xml .= "      <news:title>{$title}</news:title>\n";
                $xml .= "    </news:news>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }

    /**
     * Images sitemap — AI-generated featured images from articles.
     */
    public function images()
    {
        $xml = Cache::remember('sitemap.images', 3600, function () {
            $articles = Article::where('status', 'published')
                ->whereNotNull('image_url')
                ->orderByDesc('published_at')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
            $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

            foreach ($articles as $article) {
                $loc = url('/' . $article->slug_en);
                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";

                $imageUrl = $article->image_url;
                if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = url($imageUrl);
                }
                $imageTitle = htmlspecialchars($article->getTranslation('title', 'en'), ENT_XML1);
                $imageAlt = htmlspecialchars($article->getTranslation('image_alt', 'en') ?: $imageTitle, ENT_XML1);

                if ($imageUrl) {
                    $xml .= "    <image:image>\n";
                    $xml .= "      <image:loc>{$imageUrl}</image:loc>\n";
                    $xml .= "      <image:title>{$imageTitle}</image:title>\n";
                    $xml .= "      <image:caption>{$imageAlt}</image:caption>\n";
                    $xml .= "    </image:image>\n";
                }

                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return Response::make($xml, 200, [
            'Content-Type'  => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Flush all sitemap caches. Called after every article publish/update.
     */
    public static function flushCache(): void
    {
        foreach ([
            'sitemap.index',
            'sitemap.articles.en',
            'sitemap.articles.es',
            'sitemap.categories',
            'sitemap.tags',
            'sitemap.news',
            'sitemap.images',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function latestArticleDate(string $locale): string
    {
        $article = Article::where('status', 'published')
            ->whereNotNull('slug_' . $locale)
            ->orderByDesc('updated_at')
            ->first();

        return $article?->updated_at?->toAtomString() ?? now()->toAtomString();
    }
}
