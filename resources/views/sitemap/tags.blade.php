<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($tags as $tag)
    @if($tag->slug)
    <url>
        <loc>{{ url('/tag/' . $tag->slug) }}</loc>
        <lastmod>{{ $tag->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endif
    @endforeach
</urlset>
