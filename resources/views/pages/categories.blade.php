<x-layouts.app :trendingTags="$trendingTags">
    <x-slot:title>{{ __('ui.categories_title') }} | {{ config('app.name') }}</x-slot>
    <x-slot:metaDescription>{{ __('ui.categories_meta_desc') ?? 'Explora todas las categorías de tecnología, inteligencia artificial y ciberseguridad en ' . config('app.name', 'Glodaxia') }}</x-slot>

    <!-- Header Section (Centered & Full-width Container) -->
    <div class="mb-12 pb-8 border-b border-gray-100 dark:border-white/5 text-center w-full max-w-5xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 mb-4">
            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
            <span>{{ __('ui.categories_badge') }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.15] mb-4">
            {{ __('ui.categories_title') }}
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal max-w-3xl mx-auto">
            {{ __('ui.categories_subtitle') }}
        </p>
    </div>

    <!-- 2-Column Master Categories Grid (Centered & Full Bleed Image Cards) -->
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 justify-center">
            @foreach($categories as $cat)
                @php
                    $locale = app()->getLocale();
                    $catName = $cat->getTranslation('name', $locale) ?: $cat->getTranslation('name', 'en');
                    $catDesc = $cat->getTranslation('description', $locale) ?: $cat->getTranslation('description', 'en');
                    $catUrl = $cat->url;
                    $coverUrl = $cat->getFirstMediaUrl("images_{$locale}") ?: $cat->getFirstMediaUrl('images_en');
                    $articleCount = $cat->articles_count ?? 0;
                @endphp
                <article class="group relative flex flex-col justify-between rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-white/5 overflow-hidden shadow-xs hover:shadow-xl hover:border-cyan-500/30 dark:hover:border-cyan-500/30 transition-all duration-300">
                    
                    <!-- 1. Full Width Cover Image (Bleeds to top & sides) -->
                    <div class="relative w-full aspect-video overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900">
                        @if($coverUrl)
                            <img src="{{ $coverUrl }}" 
                                 alt="{{ $catName }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-radial from-cyan-500/10 via-transparent to-transparent">
                                <span class="text-3xl font-black text-cyan-500/40 font-mono">0{{ $loop->iteration }}</span>
                            </div>
                        @endif

                        <!-- Article Counter Floating Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-black/70 text-white backdrop-blur-md border border-white/10 shadow-xs">
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

                    <!-- 2. Text Content & Action (Inside dedicated padding container) -->
                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Category Title -->
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors mb-2.5 leading-snug">
                                <a href="{{ $catUrl }}" class="focus:outline-hidden">
                                    {{ $catName }}
                                </a>
                            </h2>

                            <!-- Category Description -->
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal mb-5">
                                {{ $catDesc }}
                            </p>
                        </div>

                        <!-- Explore Link (Seamless without heavy border line) -->
                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ $catUrl }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 dark:text-cyan-400 group-hover:text-cyan-700 dark:group-hover:text-cyan-300 transition-colors">
                                <span>{{ __('ui.category_explore') }}</span>
                                <svg aria-hidden="true"  class="w-3.5 h-3.5 transform group-hover:translate-x-1.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                            <span class="text-[11px] font-mono text-slate-400 dark:text-slate-500 font-semibold">#0{{ $loop->iteration }}</span>
                        </div>
                    </div>

                </article>
            @endforeach
        </div>
    </div>
</x-layouts.app>