<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($articles as $article)
    <url>
        <loc>{{ url('/' . ($article->slug_en ?? $article->slug_es)) }}</loc>
        <lastmod>{{ $article->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
