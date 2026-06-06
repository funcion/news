<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $articles = Article::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $trendingTags = Tag::withMinimumArticles(1)
            ->popular(10)
            ->get();

        return view('home', compact('articles', 'trendingTags'));
    }

    public function article(string $slug)
    {
        $article = Article::where('status', 'published')
            ->where(function ($query) use ($slug) {
                $query->where('slug_en', $slug)
                      ->orWhere('slug_es', $slug);
            })
            ->first();

        // If not found as an article, try as a category
        if (!$article) {
            return $this->category($slug);
        }

        $article->increment('views');

        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();
        $relatedArticles = Article::where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        
        $latestArticles = Article::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return view('article.show', compact('article', 'trendingTags', 'relatedArticles', 'latestArticles'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug_en', $slug)
            ->orWhere('slug_es', $slug)
            ->firstOrFail();

        $articles = Article::where('status', 'published')
            ->where('category_id', $category->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();

        return view('home', compact('articles', 'category', 'trendingTags'));
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $articles = $tag->articles()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();

        return view('tag.show', compact('articles', 'tag', 'trendingTags'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $articles = collect();
        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();

        if (strlen($query) >= 2) {
            $articles = Article::where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->whereRaw("title->>'en' ILIKE ?", ["%{$query}%"])
                      ->orWhereRaw("title->>'es' ILIKE ?", ["%{$query}%"])
                      ->orWhereRaw("excerpt->>'en' ILIKE ?", ["%{$query}%"])
                      ->orWhereRaw("excerpt->>'es' ILIKE ?", ["%{$query}%"]);
                })
                ->orderByDesc('published_at')
                ->paginate(20);
        }

        return view('search', compact('articles', 'query', 'trendingTags'));
    }

    public function feed()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '    <title>' . e(config('app.name', 'Glodaxia')) . '</title>' . "\n";
        $xml .= '    <link>' . url('/') . '</link>' . "\n";
        $xml .= '    <description>Tech &amp; News Magazine</description>' . "\n";
        $xml .= '    <language>' . str_replace('_', '-', app()->getLocale()) . '</language>' . "\n";
        $xml .= '    <lastBuildDate>' . now()->toRssString() . '</lastBuildDate>' . "\n";
        $xml .= '    <atom:link href="' . url('/feed.xml') . '" rel="self" type="application/rss+xml"/>' . "\n";

        foreach ($articles as $article) {
            $title = e($article->getTranslation('title', 'en'));
            $link = url('/' . ($article->slug_en ?? $article->slug_es));
            $excerpt = e(strip_tags($article->getTranslation('excerpt', 'en') ?? ''));
            $pubDate = $article->published_at?->toRssString() ?? '';

            $xml .= '    <item>' . "\n";
            $xml .= '        <title>' . $title . '</title>' . "\n";
            $xml .= '        <link>' . $link . '</link>' . "\n";
            $xml .= '        <guid isPermaLink="true">' . $link . '</guid>' . "\n";
            $xml .= '        <description>' . $excerpt . '</description>' . "\n";
            $xml .= '        <pubDate>' . $pubDate . '</pubDate>' . "\n";
            if ($article->user) {
                $xml .= '        <author>' . e($article->user->email ?? 'editorial@glodaxia.com') . '</author>' . "\n";
            }
            foreach ($article->tags as $tag) {
                $xml .= '        <category>' . e($tag->getTranslation('name', 'en') ?? $tag->name) . '</category>' . "\n";
            }
            $xml .= '    </item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
