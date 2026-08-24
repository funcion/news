<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Article;

class LegalController extends Controller
{
    protected function getCommonData(): array
    {
        $categories = Category::active()->whereHas('articles', fn($q) => $q->published())->get();
        $trendingTags = Tag::withMinimumArticles(1)->orderByDesc('article_count')->limit(8)->get();
        $recentArticles = Article::published()->latest('published_at')->limit(5)->get();

        return compact('categories', 'trendingTags', 'recentArticles');
    }

    public function terms()
    {
        return view('legal.terms', $this->getCommonData());
    }

    public function privacy()
    {
        return view('legal.privacy', $this->getCommonData());
    }

    public function cookies()
    {
        return view('legal.cookies', $this->getCommonData());
    }

    public function editorialPolicy()
    {
        return view('legal.editorial', $this->getCommonData());
    }
}