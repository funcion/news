<x-layouts.app :title="($query ? ($locale === 'es' ? 'Resultados para: ' : 'Search results for: ') . $query : ($locale === 'es' ? 'Buscador' : 'Search')) . ' | ' . config('app.name')"
               :metaDescription="($query ? ($locale === 'es' ? 'Resultados de búsqueda para: ' : 'Search results for: ') . $query : 'Buscador de noticias de inteligencia artificial, ciberseguridad y tecnología en ' . config('app.name', 'Glodaxia'))">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-8">
            <a href="{{ url($locale === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">{{ $locale === 'es' ? 'Inicio' : 'Home' }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 dark:text-slate-300">{{ $locale === 'es' ? 'Búsqueda' : 'Search' }}</span>
        </nav>

        <!-- Search Input Header -->
        <div class="mb-10">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ $locale === 'es' ? 'Búsqueda de Artículos y Análisis' : 'Search Articles & Tech Analyses' }}
            </h1>

            <form action="{{ url($locale === 'es' ? '/es/search' : '/search') }}" method="GET" class="relative">
                <input type="text" 
                       name="q" 
                       value="{{ $query }}" 
                       placeholder="{{ $locale === 'es' ? 'Escribe palabras clave (ej. OpenAI, Next.js, Cloud)...' : 'Type keywords (e.g. OpenAI, Next.js, Cloud)...' }}" 
                       class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-28 py-4 text-base focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 outline-none text-slate-900 dark:text-white placeholder:text-slate-400 shadow-sm transition-all">
                
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 active:scale-[0.99] text-white text-xs font-bold uppercase tracking-wider transition-all shadow-sm shadow-cyan-500/20">
                    {{ $locale === 'es' ? 'Buscar' : 'Search' }}
                </button>
            </form>
        </div>

        @if($query && $articles->total() > 0)
            <div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <span>{{ $locale === 'es' ? "Mostrando {$articles->total()} resultado(s) para \"{$query}\"" : "Found {$articles->total()} result(s) for \"{$query}\"" }}</span>
            </div>

            <div class="flex flex-col divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($articles as $article)
                    @php
                        $articleTitle = $article->getTranslation('title', $locale) ?: $article->getTranslation('title', 'en');
                        $articleExcerpt = $article->getTranslation('excerpt', $locale) ?: $article->getTranslation('excerpt', 'en');
                        $categoryName = $article->category ? ($article->category->getTranslation('name', $locale) ?: $article->category->getTranslation('name', 'en')) : null;
                        $articleUrl = $locale === 'es' ? url('/es/' . $article->slug) : url('/' . $article->slug);
                    @endphp
                    <article class="py-6 flex flex-col sm:flex-row items-start gap-5 group">
                        @if($article->image)
                            <a href="{{ $articleUrl }}" class="w-full sm:w-40 sm:h-28 aspect-[16/9] sm:aspect-auto shrink-0 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 relative block">
                                <img src="{{ $article->image }}" alt="{{ $articleTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            </a>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 text-[10.5px] font-black uppercase tracking-wider mb-2">
                                @if($categoryName)
                                    <span class="text-cyan-600 dark:text-cyan-400">{{ $categoryName }}</span>
                                    <span class="text-slate-300 dark:text-slate-700">●</span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500 font-medium">{{ $article->published_at ? $article->published_at->format('d M, Y') : '' }}</span>
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2">
                                <a href="{{ $articleUrl }}">{{ $articleTitle }}</a>
                            </h3>

                            @if($articleExcerpt)
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-2 font-normal">
                                    {{ $articleExcerpt }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->appends(['q' => $query])->links() }}
            </div>
        @elseif($query)
            <div class="text-center py-16 px-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-dashed border-slate-200 dark:border-slate-800">
                <span class="text-3xl mb-3 block">🔍</span>
                <h3 class="text-base font-black text-slate-900 dark:text-white mb-2">
                    {{ $locale === 'es' ? "No encontramos resultados para \"{$query}\"" : "No results found for \"{$query}\"" }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                    {{ $locale === 'es' ? 'Prueba buscando con otros términos, conceptos o revisa las noticias más recientes en la portada.' : 'Try searching for alternative keywords, technology concepts, or browse the homepage.' }}
                </p>
            </div>
        @endif
    </div>
</x-layouts.app>