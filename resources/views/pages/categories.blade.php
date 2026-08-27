<x-layouts.app :trendingTags="$trendingTags">
    <x-slot:title>
        {{ __('ui.categories_title') }} | {{ config('app.name') }}
    </x-slot>

    <!-- Header Section -->
    <div class="mb-8 pb-4 border-b border-gray-100 dark:border-white/5 relative">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 mb-3">
            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
            <span>{{ __('ui.categories_badge') }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.15]">
            {{ __('ui.categories_title') }}
        </h1>
        <p class="mt-3 text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-3xl leading-relaxed font-normal">
            {{ __('ui.categories_subtitle') }}
        </p>
    </div>

    <!-- 3-Column Master Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6">
        @foreach($categories as $cat)
            @php
                $locale = app()->getLocale();
                $catName = $cat->getTranslation('name', $locale) ?: $cat->getTranslation('name', 'en');
                $catDesc = $cat->getTranslation('description', $locale) ?: $cat->getTranslation('description', 'en');
                $catUrl = $cat->url;
                $coverUrl = $cat->getFirstMediaUrl("images_{$locale}") ?: $cat->getFirstMediaUrl('images_en');
                $articleCount = $cat->articles_count ?? 0;
            @endphp
            <article class="group relative flex flex-col justify-between rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs hover:shadow-lg hover:border-cyan-500/40 dark:hover:border-cyan-500/40 transition-all duration-200">
                
                <div>
                    <!-- Visual Cover / Thumbnail -->
                    <div class="relative w-full aspect-video rounded-lg overflow-hidden mb-4 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 border border-slate-100 dark:border-slate-800">
                        @if($coverUrl)
                            <img src="{{ $coverUrl }}" 
                                 alt="{{ $catName }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-radial from-cyan-500/10 via-transparent to-transparent">
                                <span class="text-2xl font-black text-cyan-500/40 font-mono">0{{ $loop->iteration }}</span>
                            </div>
                        @endif

                        <!-- Article Counter Badge -->
                        <div class="absolute top-2.5 right-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/70 text-white backdrop-blur-md border border-white/10 shadow-xs">
                                @if($articleCount == 1)
                                    {{ __('ui.category_single_article') }}
                                @elseif($articleCount > 1)
                                    {{ __('ui.category_articles_count', ['count' => $articleCount]) }}
                                @else
                                    {{ __('ui.category_no_articles') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Category Title -->
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors mb-2 leading-snug">
                        <a href="{{ $catUrl }}" class="focus:outline-hidden">
                            {{ $catName }}
                        </a>
                    </h2>

                    <!-- Category Description -->
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-3 mb-4 font-normal">
                        {{ $catDesc }}
                    </p>
                </div>

                <!-- Explore Link -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <a href="{{ $catUrl }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 dark:text-cyan-400 group-hover:text-cyan-700 dark:group-hover:text-cyan-300 transition-colors">
                        <span>{{ __('ui.category_explore') }}</span>
                        <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 font-semibold">#0{{ $loop->iteration }}</span>
                </div>
            </article>
        @endforeach
    </div>
</x-layouts.app>