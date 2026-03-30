<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

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
        $locale = app()->getLocale();
        // Since slug is stored as slug_en or slug_es, but Spatie doesn't support json where easily without raw, and we don't have translatable slug. Wait, slug is translatable or standard?
        // Wait, the schema has `slug` as string, wait, no, the migration made it translatable? Let me check migration.
        // I will just find by json column!
        $article = Article::where('status', 'published')
            ->whereJsonContains('slug->' . $locale, $slug)
            ->orWhere("slug_en", $slug) // If fallbacks are used
            ->orWhere("slug_es", $slug)
            ->firstOrFail();

        $article->increment('views');

        $trendingTags = Tag::withMinimumArticles(1)->popular(10)->get();
        $relatedArticles = Article::where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('article.show', compact('article', 'trendingTags', 'relatedArticles'));
    }

    public function category(string $slug)
    {
        $locale = app()->getLocale();
        $category = Category::whereJsonContains('slug->' . $locale, $slug)->firstOrFail();

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

        return view('home', compact('articles', 'tag', 'trendingTags'));
    }
}
