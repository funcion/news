<x-layouts.app>
    <x-slot:title>{{ $query ? __('ui.search_results') . ': ' . $query : __('ui.search') }} | {{ config('app.name') }}</x-slot>
    <x-slot:metaDescription>{{ __('ui.search_meta_desc') }}</x-slot>

    <div class="max-w-4xl">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-10">
            <a href="{{ url('/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-500 transition-colors">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600 dark:text-slate-400">{{ __('ui.search') }}</span>
        </nav>

        <!-- Search Form -->
        <form action="{{ url('/search') }}" method="GET" class="mb-12">
            <div class="relative">
                <input type="text" name="q" value="{{ $query }}" 
                       placeholder="{{ __('ui.search_placeholder') }}"
                       class="w-full bg-white dark:bg-slate-900 border border-gray-200 dark:border-white/10 rounded-lg px-6 py-4 pr-12 text-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition-all"
                       autofocus>
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-cyan-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>

        @if($query && $articles->count() > 0)
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">{{ __('ui.search_count', ['count' => $articles->total(), 'query' => $query]) }}</p>
            <div class="grid grid-cols-1 gap-8">
                @foreach($articles as $article)
                    <a href="{{ route('articles.show', app()->getLocale() === 'es' ? ($article->slug_es ?? $article->slug_en) : ($article->slug_en ?? $article->slug_es)) }}" 
                       class="group flex gap-5 items-start p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-900 transition-all">
                        <div class="w-24 h-24 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <img src="{{ $article->image_url ?? '/placeholder.webp' }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 text-[9px] font-black uppercase text-slate-500 dark:text-slate-400 mb-2">
                                <span class="text-cyan-600 dark:text-cyan-500">{{ $article->category?->name }}</span>
                                <div class="w-1 h-1 rounded-lg bg-slate-300 dark:bg-slate-700"></div>
                                <span>{{ $article->published_at?->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight mb-2 tracking-tighter group-hover:text-cyan-500 transition-colors line-clamp-2">
                                {{ $article->title }}
                            </h3>
                            <p class="text-slate-600 dark:text-slate-400 text-[14px] leading-relaxed line-clamp-2">{{ $article->excerpt }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-12">{{ $articles->links() }}</div>
        @elseif($query)
            <div class="text-center py-16 bg-gray-50 dark:bg-white/[0.02] rounded-lg border border-dashed border-gray-200 dark:border-white/10">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('ui.search_no_results') }}</h3>
                <p class="mt-2 text-xs text-slate-600 dark:text-slate-500">{{ __('ui.search_try_again') }}</p>
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-slate-500 dark:text-slate-400">{{ __('ui.search_hint') }}</p>
            </div>
        @endif
    </div>

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
