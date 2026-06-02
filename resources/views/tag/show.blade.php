<x-layouts.app>
    <x-slot:title>#{{ $tag->name }} | {{ config('app.name') }}</x-slot>
    <x-slot:metaDescription>{{ __('ui.tag_meta_desc', ['tag' => $tag->name]) }}</x-slot>

    <x-slot:head>
        <meta property="og:title" content="#{{ $tag->name }} | {{ config('app.name') }}" />
        <meta property="og:description" content="{{ __('ui.tag_meta_desc', ['tag' => $tag->name]) }}" />
        <meta property="og:type" content="website" />
        <link rel="canonical" href="{{ url()->current() }}" />
    </x-slot>

    <article class="max-w-4xl">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-10">
            <a href="{{ url('/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-500 transition-colors">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600 dark:text-slate-400 truncate">{{ $tag->name }}</span>
        </nav>

        <!-- Tag Header -->
        <header class="mb-12">
            <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-lg mb-6 leading-none">
                {{ __('ui.topic') }}
            </span>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.05] mb-8">
                #{{ $tag->name }}
            </h1>
        </header>

        @if($articles->count() > 0)
            <!-- Feed Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                @foreach($articles as $article)
                    <article class="flex flex-col group">
                        <a href="{{ route('articles.show', app()->getLocale() === 'es' ? ($article->slug_es ?? $article->slug_en) : ($article->slug_en ?? $article->slug_es)) }}" 
                           class="block overflow-hidden rounded-lg aspect-[16/9] bg-gray-100 dark:bg-slate-900 border border-gray-100 dark:border-white/5 mb-4 group-hover:border-cyan-500/30 transition-all">
                            <img src="{{ $article->image_url ?? '/placeholder.webp' }}" alt="{{ $article->image_alt ?? $article->title }}" class="w-full h-full object-cover">
                        </a>
                        <div class="flex items-center gap-3 text-[9px] font-black uppercase text-slate-500 dark:text-slate-400 mb-3">
                            <span class="text-cyan-600 dark:text-cyan-500">{{ $article->category?->name }}</span>
                            <div class="w-1 h-1 rounded-lg bg-slate-300 dark:bg-slate-700"></div>
                            <span>{{ $article->published_at?->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight mb-3 tracking-tighter group-hover:text-cyan-500 transition-colors">
                            <a href="{{ route('articles.show', app()->getLocale() === 'es' ? ($article->slug_es ?? $article->slug_en) : ($article->slug_en ?? $article->slug_es)) }}">{{ $article->title }}</a>
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-[12px] leading-relaxed line-clamp-2 mb-4">{{ $article->excerpt }}</p>
                        <div class="mt-auto flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-white/5">
                            <span class="text-[9px] font-bold uppercase text-slate-500 dark:text-slate-400">{{ $article->user?->name ?? 'Glodaxia' }}</span>
                            <span class="text-[9px] font-black text-slate-600 dark:text-slate-500 uppercase tracking-widest ml-auto">{{ $article->reading_time ?? 5 }} min</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-20 border-t border-gray-100 dark:border-white/5 pt-8">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-gray-50 dark:bg-white/[0.02] rounded-lg border border-dashed border-gray-200 dark:border-white/10">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('ui.archives_empty') }}</h3>
                <p class="mt-2 text-xs text-slate-600 dark:text-slate-500">{{ __('ui.tag_empty') }}</p>
            </div>
        @endif
    </article>

    <x-slot:sidebar>
        <div class="relative">
            <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400 mb-8 flex items-center gap-3">
                <span class="w-1.5 h-1.5 bg-cyan-600 dark:bg-cyan-500 rounded-lg"></span>
                {{ __('ui.trending_topics') }}
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($trendingTags ?? [] as $ttag)
                    <a href="{{ route('tags.show', $ttag->slug) }}" class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/5 rounded-lg text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest hover:border-cyan-600 hover:text-cyan-600 dark:hover:text-cyan-500 transition-all">
                        #{{ $ttag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>
</x-layouts.app>
