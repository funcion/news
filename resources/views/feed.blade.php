<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ config('app.name', 'Glodaxia') }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ __('Tech & News Magazine') }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ url('/feed.xml') }}" rel="self" type="application/rss+xml"/>
        @foreach($articles as $article)
        <item>
            <title>{{ $article->title }}</title>
            <link>{{ url('/' . $article->slug_en) }}</link>
            <guid isPermaLink="true">{{ url('/' . $article->slug_en) }}</guid>
            <description>{{ strip_tags($article->excerpt) }}</description>
            <pubDate>{{ $article->published_at?->toRssString() }}</pubDate>
            @if($article->user)
            <author>{{ $article->user?->email ?? 'editorial@glodaxia.com' }} ({{ $article->user?->name ?? 'Glodaxia' }})</author>
            @endif
            @foreach($article->tags as $tag)
            <category>{{ $tag->name }}</category>
            @endforeach
        </item>
        @endforeach
    </channel>
</rss>