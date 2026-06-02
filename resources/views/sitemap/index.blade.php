<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Home -->
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
    <!-- Metodología -->
    <url>
        <loc>{{ url('/metodologia-editorial') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @foreach($articles as $article)
    <!-- Article: {{ $article->getTranslation('title', 'en') }} -->
    <url>
        <loc>{{ url('/' . $article->slug_en) }}</loc>
        <lastmod>{{ $article->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach($tags as $tag)
    @if($tag->slug)
    <!-- Tag: {{ $tag->name }} -->
    <url>
        <loc>{{ url('/tag/' . $tag->slug) }}</loc>
        <lastmod>{{ $tag->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endif
    @endforeach
</urlset>
