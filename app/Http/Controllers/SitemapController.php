<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get();

        $tags = Tag::where('article_count', '>', 0)
            ->orderByDesc('article_count')
            ->get();

        return response()
            ->view('sitemap.index', compact('articles', 'tags'))
            ->header('Content-Type', 'application/xml');
    }

    public function articles()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get();

        return response()
            ->view('sitemap.articles', compact('articles'))
            ->header('Content-Type', 'application/xml');
    }

    public function tags()
    {
        $tags = Tag::where('article_count', '>', 0)
            ->orderByDesc('article_count')
            ->get();

        return response()
            ->view('sitemap.tags', compact('tags'))
            ->header('Content-Type', 'application/xml');
    }
}
