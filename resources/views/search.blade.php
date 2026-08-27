@php
    $appName = config('app.name', 'Glodaxia');
    $isEs = ($locale === 'es');
    
    // Exact 120-150 char meta descriptions
    if ($query) {
        $pageTitle = $isEs ? ("Resultados para \"{$query}\" | {$appName}") : ("Search Results for \"{$query}\" | {$appName}");
        $metaDescription = $isEs 
            ? "Explora todos los artículos, análisis y noticias de tecnología relacionados con \"{$query}\" en la plataforma independiente {$appName}."
            : "Explore all technology journalism articles, research papers, and news updates matching \"{$query}\" on the independent {$appName} magazine.";
    } else {
        $pageTitle = $isEs ? ("Buscador de Noticias y Tecnología | {$appName}") : ("Search Tech News & Articles | {$appName}");
        $metaDescription = $isEs 
            ? "Encuentra noticias, análisis y artículos sobre inteligencia artificial, desarrollo de software y ciberseguridad en el buscador de {$appName}."
            : "Find breaking news, technical analysis, and research on artificial intelligence, software, and cybersecurity on {$appName} search engine.";
    }
@endphp

<x-layouts.app :robots="'noindex, nofollow'">
    <x-slot:title>{{ $pageTitle }}</x-slot>
    <x-slot:metaDescription>{{ $metaDescription }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url($isEs ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ $isEs ? 'Búsqueda' : 'Search' }}</span>
        </nav>

        <!-- Search Input Header -->
        <header class="mb-10">
            <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-lg mb-4 leading-none border border-cyan-500/20">
                {{ $isEs ? 'BUSCADOR GLOBAL' : 'GLOBAL SEARCH' }}
            </span>
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-6">
                {{ $isEs ? 'Búsqueda de Artículos y Análisis' : 'Search Articles & Tech Analyses' }}
            </h1>

            <form action="{{ url($isEs ? '/es/buscar' : '/search') }}" method="GET" role="search" aria-label="{{ $isEs ? 'Formulario de Búsqueda' : 'Search Form' }}" class="relative">
                <label for="search-page-input" class="sr-only">{{ $isEs ? 'Buscar noticias y artículos' : 'Search news and articles' }}</label>
                <input id="search-page-input"
                       type="search" 
                       name="q" 
                       value="{{ $query }}" 
                       placeholder="{{ $isEs ? 'Escribe palabras clave (ej. OpenAI, Next.js, Cloud, Python)...' : 'Type keywords (e.g. OpenAI, Next.js, Cloud, Python)...' }}" 
                       class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-32 py-4 text-sm sm:text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 focus:border-cyan-500 text-slate-900 dark:text-white placeholder:text-slate-400 shadow-sm transition-all">
                
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" 
                        aria-label="{{ $isEs ? 'Ejecutar Búsqueda' : 'Submit Search' }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 active:scale-[0.99] text-white text-xs font-bold uppercase tracking-wider transition-all shadow-sm shadow-cyan-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500">
                    {{ $isEs ? 'Buscar' : 'Search' }}
                </button>
            </form>
        </header>

        @if($query && $articles->total() > 0)
            <div role="status" aria-live="polite" class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>{{ $isEs ? "Mostrando {$articles->total()} resultado(s) para \"{$query}\"" : "Found {$articles->total()} result(s) for \"{$query}\"" }}</span>
            </div>

            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-800" role="feed" aria-label="{{ $isEs ? 'Resultados de búsqueda' : 'Search Results Feed' }}">
                @foreach($articles as $article)
                    @php
                        $articleTitle = $article->getTranslation('title', $locale) ?: $article->getTranslation('title', 'en');
                        $articleExcerpt = $article->getTranslation('excerpt', $locale) ?: $article->getTranslation('excerpt', 'en');
                        $categoryName = $article->category ? ($article->category->getTranslation('name', $locale) ?: $article->category->getTranslation('name', 'en')) : null;
                        $articleUrl = $isEs ? url('/es/' . ($article->slug_es ?? $article->slug_en)) : url('/' . ($article->slug_en ?? $article->slug_es));
                    @endphp
                    <article class="py-6 flex flex-col sm:flex-row items-start gap-5 group focus-within:ring-2 focus-within:ring-cyan-500 rounded-2xl p-2 -m-2 transition-all">
                        @if($article->image_url || $article->image)
                            <a href="{{ $articleUrl }}" 
                               tabindex="-1"
                               aria-hidden="true"
                               class="w-full sm:w-44 sm:h-28 aspect-[16/9] sm:aspect-auto shrink-0 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 relative block border border-slate-200/80 dark:border-white/5">
                                <img src="{{ $article->image_url ?? $article->image }}" 
                                     alt="{{ $article->image_alt ?? $articleTitle }}" 
                                     width="640" 
                                     height="360" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     loading="lazy" 
                                     decoding="async">
                            </a>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 text-[10.5px] font-black uppercase tracking-wider mb-2">
                                @if($categoryName)
                                    <span class="text-cyan-600 dark:text-cyan-400 font-extrabold">{{ $categoryName }}</span>
                                    <span class="text-slate-300 dark:text-slate-700" aria-hidden="true">●</span>
                                @endif
                                <time datetime="{{ $article->published_at?->toIso8601String() }}" class="text-slate-500 dark:text-slate-400 font-medium">{{ $article->published_at ? $article->published_at->format('d M, Y') : '' }}</time>
                            </div>

                            <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2">
                                <a href="{{ $articleUrl }}" class="focus-visible:outline-none focus-visible:underline">{{ $articleTitle }}</a>
                            </h2>

                            @if($articleExcerpt)
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-2 font-normal">
                                    {{ $articleExcerpt }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10" aria-label="{{ __('ui.pagination') }}">
                {{ $articles->appends(['q' => $query])->links() }}
            </div>
        @elseif($query)
            <div class="text-center py-16 px-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-dashed border-slate-200 dark:border-slate-800">
                <span class="text-4xl mb-3 block" aria-hidden="true">🔍</span>
                <h2 class="text-base font-black text-slate-900 dark:text-white mb-2">
                    {{ $isEs ? "No encontramos resultados para \"{$query}\"" : "No results found for \"{$query}\"" }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                    {{ $isEs ? 'Prueba buscando con otros términos, conceptos o revisa las noticias más recientes en la portada.' : 'Try searching for alternative keywords, technology concepts, or browse the homepage.' }}
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>