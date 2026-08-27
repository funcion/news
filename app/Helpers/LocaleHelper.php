<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Request;

class LocaleHelper
{
    /**
     * Resolve the exact, fully-localized URL for the target locale.
     * Maps all static slugs and dynamic entity slugs seamlessly.
     */
    public static function getAlternateUrl(string $targetLocale): string
    {
        $queryString = Request::getQueryString() ? ('?' . Request::getQueryString()) : '';
        $path = trim(Request::path(), '/');

        // 1. Home
        if ($path === '' || $path === 'es') {
            return $targetLocale === 'es' ? url('/es' . $queryString) : url('/' . $queryString);
        }

        // 2. Search (/search in EN <-> /es/buscar in ES)
        if (in_array($path, ['search', 'es/search', 'es/buscar', 'buscar'])) {
            return $targetLocale === 'es' ? url('/es/buscar' . $queryString) : url('/search' . $queryString);
        }

        // 3. Categories Directory (/categories <-> /es/categorias)
        if (in_array($path, ['categories', 'es/categories', 'categorias', 'es/categorias'])) {
            return $targetLocale === 'es' ? url('/es/categorias' . $queryString) : url('/categories' . $queryString);
        }

        // 4. About Us (/about-us <-> /es/nosotros)
        if (in_array($path, ['about-us', 'about', 'es/about-us', 'es/about', 'nosotros', 'es/nosotros'])) {
            return $targetLocale === 'es' ? url('/es/nosotros' . $queryString) : url('/about-us' . $queryString);
        }

        // 5. Contact (/contact <-> /es/contacto)
        if (in_array($path, ['contact', 'es/contact', 'contacto', 'es/contacto'])) {
            return $targetLocale === 'es' ? url('/es/contacto' . $queryString) : url('/contact' . $queryString);
        }

        // 6. Terms & Conditions (/terms-and-conditions <-> /es/terminos-y-condiciones)
        if (in_array($path, ['terms-and-conditions', 'terms', 'es/terms-and-conditions', 'terminos-y-condiciones', 'es/terminos-y-condiciones'])) {
            return $targetLocale === 'es' ? url('/es/terminos-y-condiciones' . $queryString) : url('/terms-and-conditions' . $queryString);
        }

        // 7. Privacy Policy (/privacy-policy <-> /es/politica-de-privacidad)
        if (in_array($path, ['privacy-policy', 'privacy', 'es/privacy-policy', 'politica-de-privacidad', 'es/politica-de-privacidad'])) {
            return $targetLocale === 'es' ? url('/es/politica-de-privacidad' . $queryString) : url('/privacy-policy' . $queryString);
        }

        // 8. Cookie Policy (/cookie-policy <-> /es/politica-de-cookies)
        if (in_array($path, ['cookie-policy', 'cookies', 'es/cookie-policy', 'politica-de-cookies', 'es/politica-de-cookies'])) {
            return $targetLocale === 'es' ? url('/es/politica-de-cookies' . $queryString) : url('/cookie-policy' . $queryString);
        }

        // 9. Editorial Policy (/editorial-policy <-> /es/politica-editorial)
        if (in_array($path, ['editorial-policy', 'editorial', 'es/editorial-policy', 'politica-editorial', 'es/politica-editorial'])) {
            return $targetLocale === 'es' ? url('/es/politica-editorial' . $queryString) : url('/editorial-policy' . $queryString);
        }

        // 10. Profile (/profile <-> /es/perfil)
        if (in_array($path, ['profile', 'es/profile', 'perfil', 'es/perfil'])) {
            return $targetLocale === 'es' ? url('/es/perfil' . $queryString) : url('/profile' . $queryString);
        }

        // 11. Login / Register
        if (in_array($path, ['login', 'es/login'])) {
            return $targetLocale === 'es' ? url('/es/login' . $queryString) : url('/login' . $queryString);
        }
        if (in_array($path, ['register', 'es/register', 'registro', 'es/registro'])) {
            return $targetLocale === 'es' ? url('/es/registro' . $queryString) : url('/register' . $queryString);
        }

        // 12. Tag Show Route (/tag/{slug} <-> /es/tag/{slug})
        if (preg_match('/^(es\/)?tag\/([^\/]+)$/', $path, $matches)) {
            $tagSlug = $matches[2];
            return $targetLocale === 'es' ? url('/es/tag/' . $tagSlug . $queryString) : url('/tag/' . $tagSlug . $queryString);
        }

        // 13. Dynamic Article or Category Slug (/slug <-> /es/slug_es)
        $cleanSlug = preg_replace('/^es\//', '', $path);

        // Check Category first
        $category = Category::where('slug_en', $cleanSlug)
            ->orWhere('slug_es', $cleanSlug)
            ->first();

        if ($category) {
            $targetSlug = $targetLocale === 'es' 
                ? ($category->slug_es ?: $category->slug_en) 
                : ($category->slug_en ?: $category->slug_es);

            return $targetLocale === 'es' 
                ? url('/es/' . $targetSlug . $queryString) 
                : url('/' . $targetSlug . $queryString);
        }

        // Check Article
        $article = Article::where('slug_en', $cleanSlug)
            ->orWhere('slug_es', $cleanSlug)
            ->first();

        if ($article) {
            $targetSlug = $targetLocale === 'es' 
                ? ($article->slug_es ?: $article->slug_en) 
                : ($article->slug_en ?: $article->slug_es);

            return $targetLocale === 'es' 
                ? url('/es/' . $targetSlug . $queryString) 
                : url('/' . $targetSlug . $queryString);
        }

        // Fallback to LaravelLocalization
        try {
            return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($targetLocale, null, [], true);
        } catch (\Throwable $e) {
            return $targetLocale === 'es' ? url('/es/' . $path . $queryString) : url('/' . $cleanSlug . $queryString);
        }
    }
}