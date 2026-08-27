<x-layouts.app>
    <x-slot:title>{{ $tag->meta_title ?: ('#' . $tag->name . ' | ' . config('app.name')) }}</x-slot>
    <x-slot:metaDescription>{{ $tag->meta_description ?: ($tag->description ?: __('ui.tag_meta_desc', ['tag' => $tag->name])) }}</x-slot>

    <x-slot:head>
        <meta property="og:title" content="#{{ $tag->name }} | {{ config('app.name') }}" />
        <meta property="og:description" content="{{ $tag->description ?: __('ui.tag_meta_desc', ['tag' => $tag->name]) }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="#{{ $tag->name }} | {{ config('app.name') }}" />
        <meta name="twitter:description" content="{{ $tag->description ?: __('ui.tag_meta_desc', ['tag' => $tag->name]) }}" />
        <link rel="canonical" href="{{ url()->current() }}" />

        <!-- JSON-LD Schema: CollectionPage / ItemList -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "#{{ $tag->name }} - {{ config('app.name') }}",
            "description": "{{ $tag->description ?: __('ui.tag_meta_desc', ['tag' => $tag->name]) }}",
            "url": "{{ url()->current() }}",
            "mainEntity": {
                "@type": "ItemList",
                "itemListElement": [
                    @foreach($articles->take(10) as $index => $art)
                    {
                        "@type": "ListItem",
                        "position": {{ $index + 1 }},
                        "url": "{{ route('articles.show', app()->getLocale() === 'es' ? ($art->slug_es ?? $art->slug_en) : ($art->slug_en ?? $art->slug_es)) }}",
                        "name": "{{ addslashes($art->title) }}"
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            }
        }
        </script>
    </x-slot>

    <div class="max-w-4xl">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') ?? 'Breadcrumb' }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url('/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ $tag->name }}</span>
        </nav>

        <!-- Tag Header -->
        <header class="mb-12">
            <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-lg mb-4 leading-none border border-cyan-500/20">
                {{ __('ui.topic') ?? 'TEMA' }}
            </span>
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.08] mb-4">
                #{{ $tag->name }}
            </h1>
            @if($tag->description)
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl">
                    {{ $tag->description }}
                </p>
            @endif
        </header>

        @if($articles->count() > 0)
            <!-- Feed Grid (Strict Heading Hierarchy: h2 for article titles) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16" role="feed" aria-label="Noticias sobre {{ $tag->name }}">
                @foreach($articles as $article)
                    <article class="flex flex-col group focus-within:ring-2 focus-within:ring-cyan-500 rounded-2xl p-2 -m-2 transition-all">
                        <a href="{{ route('articles.show', app()->getLocale() === 'es' ? ($article->slug_es ?? $article->slug_en) : ($article->slug_en ?? $article->slug_es)) }}" 
                           class="block overflow-hidden rounded-xl aspect-[16/9] bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-white/5 mb-4 group-hover:border-cyan-500/30 transition-all focus-visible:outline-none"
                           tabindex="-1"
                           aria-hidden="true">
                            <img src="{{ $article->image_url ?? '/placeholder.webp' }}" 
                                 alt="{{ $article->image_alt ?? $article->title }}" 
                                 width="640" 
                                 height="360" 
                                 loading="lazy" 
                                 decoding="async" 
                                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                        </a>
                        <div class="flex items-center gap-3 text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-2.5">
                            <span class="text-cyan-600 dark:text-cyan-400 font-extrabold">{{ $article->category?->name }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700" aria-hidden="true"></span>
                            <time datetime="{{ $article->published_at?->toIso8601String() }}">{{ $article->published_at?->diffForHumans() }}</time>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-snug mb-3 tracking-tight group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                            <a href="{{ route('articles.show', app()->getLocale() === 'es' ? ($article->slug_es ?? $article->slug_en) : ($article->slug_en ?? $article->slug_es)) }}" class="focus-visible:outline-none focus-visible:underline">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed line-clamp-2 mb-4">{{ $article->excerpt }}</p>
                        <div class="mt-auto flex items-center gap-2 pt-4 border-t border-slate-100 dark:border-white/5">
                            <span class="text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400">{{ $article->user?->name ?? 'Glodaxia' }}</span>
                            <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-auto">{{ $article->reading_time ?? 5 }} min</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 border-t border-slate-200 dark:border-white/5 pt-8" aria-label="{{ __('ui.pagination') ?? 'Paginación' }}">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-slate-50 dark:bg-white/[0.02] rounded-2xl border border-dashed border-slate-200 dark:border-white/10">
                <h2 class="text-sm font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('ui.archives_empty') }}</h2>
                <p class="mt-2 text-xs text-slate-600 dark:text-slate-400">{{ __('ui.tag_empty') }}</p>
            </div>
        @endif
    </div>

    <x-slot:sidebar>
        <aside aria-label="{{ __('ui.trending_topics') ?? 'Temas de Tendencia' }}" class="relative">
            <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400 mb-6 flex items-center gap-2.5">
                <span class="w-1.5 h-1.5 bg-cyan-600 dark:bg-cyan-500 rounded-full" aria-hidden="true"></span>
                {{ __('ui.trending_topics') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach($trendingTags ?? [] as $ttag)
                    <a href="{{ route('tags.show', $ttag->slug) }}" class="px-3.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wide hover:border-cyan-600 hover:text-cyan-600 dark:hover:text-cyan-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 transition-all shadow-xs">
                        #{{ $ttag->name }}
                    </a>
                @endforeach
            </div>
        </aside>
    </x-slot>
</x-layouts.app>