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

        return view('article.show', compact('article', 'trendingTags', 'relatedArticles'));
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

        return view('home', compact('articles', 'tag', 'trendingTags'));
    }
}
