<x-layouts.app>
    <x-slot:title>
        {{ $article->meta_title ?? $article->title }} | {{ config('app.name') }}
    </x-slot>

    <x-slot:metaDescription>{{ $article->meta_description ?? $article->excerpt }}</x-slot>

    @if($article->meta_keywords)
        <x-slot:metaKeywords>{{ is_array($article->meta_keywords) ? implode(', ', $article->meta_keywords) : $article->meta_keywords }}</x-slot>
    @endif

    <x-slot:head>
        <!-- JSON-LD Structured Data -->
        @if(isset($article->ai_metadata['json_ld']))
            <script type="application/ld+json">
                {!! json_encode($article->ai_metadata['json_ld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
        @endif

        <!-- Open Graph -->
        <meta property="og:title" content="{{ $article->meta_title ?? $article->title }}" />
        <meta property="og:description" content="{{ $article->excerpt }}" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ url()->current() }}" />
        @if($article->image_url)
            <meta property="og:image" content="{{ $article->image_url }}" />
        @endif
    </x-slot>

    <article class="max-w-4xl">
        <!-- Breadcrumbs (Minimalist) -->
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-10">
            <a href="{{ url('/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-500 transition-colors">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-30 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($article->category)
                <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? ($article->category->slug_es ?? $article->category->slug) : ($article->category->slug_en ?? $article->category->slug)) }}" class="hover:text-cyan-600 dark:hover:text-cyan-500 transition-colors">{{ $article->category->name }}</a>
                <svg class="w-3 h-3 opacity-30 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-slate-600 dark:text-slate-400 truncate max-w-[150px]">{{ __('ui.current_post') }}</span>
        </nav>

        <!-- Header Section (Impactful) -->
        <header class="mb-12">
            @if($article->category)
                <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-lg mb-6 leading-none">
                    {{ $article->category->name }}
                </span>
            @endif
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.05] mb-8">
                {{ $article->title }}
            </h1>
            
            <div class="flex items-center gap-6 border-y border-gray-200 dark:border-white/5 py-4">
                <div class="flex items-center gap-3">
                    <img src="{{ $article->author?->avatar_url ?? 'https://ui-avatars.com/api/?name=AI&background=0284c7&color=fff' }}" class="h-8 w-8 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm">
                    <div class="flex flex-col">
                         <span class="text-[11px] font-black uppercase tracking-widest text-slate-900 dark:text-white leading-none mb-1">{{ $article->author?->name ?? __('ui.reporter') }}</span>
                    </div>
                </div>
                <div class="h-6 w-px bg-gray-200 dark:bg-white/5 hidden sm:block"></div>
                <div class="flex items-center gap-4">
                    <time datetime="{{ $article->published_at?->toIso8601String() }}" class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400">
                        {{ $article->published_at?->format('M d, Y') }}
                    </time>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ __('ui.min_read', ['count' => $article->reading_time ?? 5]) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-cyan-600 dark:text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                    <span class="font-bold text-gray-900 dark:text-gray-200">{{ __('ui.views_count', ['count' => number_format($article->views ?? 0)]) }}</span>
                </div>
            </div>
        </header>

        @php
            $locale = app()->getLocale();
            $featuredMedia = $article->getFirstMedia("images_{$locale}");
            $currentUrl = urlencode(url()->current());
            $pageTitle = urlencode($article->title);
        @endphp

        <!-- Main Featured Image -->
        @if($featuredMedia)
            <figure class="mb-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-800 ring-8 ring-gray-50 dark:ring-gray-900/50 group relative">
                <img src="{{ $featuredMedia->getUrl('large') }}" 
                     srcset="{{ $featuredMedia->getSrcset('large') ?? ($featuredMedia->getUrl('thumb') . ' 480w, ' . $featuredMedia->getUrl('medium') . ' 800w, ' . $featuredMedia->getUrl('large') . ' 1200w') }}"
                     sizes="(max-width: 600px) 100vw, (max-width: 1200px) 800px, 1200px"
                     alt="{{ $article->image_alt ?? $article->title }}" 
                     title="{{ $article->title }}"
                     class="w-full h-auto object-cover aspect-video group-hover:scale-105 transition-transform duration-700"
                     loading="eager">
                @if(config('global.features.show_ai_disclaimers'))
                <div class="absolute bottom-4 right-4 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    <span class="text-[9px] font-black uppercase tracking-widest text-white/90 flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('ui.ai_generated_image') }}
                    </span>
                </div>
                @endif
            </figure>
        @elseif($article->image_url)
            <figure class="mb-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-800 ring-8 ring-gray-50 dark:ring-gray-900/50 group relative">
                <img src="{{ $article->image_url }}" alt="{{ $article->image_alt ?? $article->title }}" class="w-full h-auto object-cover aspect-video group-hover:scale-105 transition-transform duration-700" loading="eager">
                @if(config('global.features.show_ai_disclaimers'))
                <div class="absolute bottom-4 right-4 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                    <span class="text-[9px] font-black uppercase tracking-widest text-white/90 flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('ui.ai_generated_image') }}
                    </span>
                </div>
                @endif
            </figure>
        @endif

        <!-- Article Body -->
        <div class="prose prose-xl prose-cyan dark:prose-dark max-w-none prose-headings:font-black prose-img:rounded-lg prose-a:text-cyan-600 hover:prose-a:text-cyan-700 dark:prose-a:text-cyan-400 dark:hover:prose-a:text-cyan-300 leading-relaxed text-gray-700 dark:text-gray-300">
            {!! $article->content !!}
        </div>

        <!-- Social Share Bar (Moved to End) -->
        <div class="mt-20 mb-10 bg-gray-50 dark:bg-white/5 backdrop-blur-sm rounded-lg p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-gray-200 dark:border-white/10 overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 to-blue-500/5 pointer-events-none"></div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white relative z-10">{{ __('ui.share_post') }}</h3>
            <div class="flex flex-wrap items-center justify-center gap-3 relative z-10">
                @php
                    $platforms = [
                        ['id' => 'fb', 'bg' => 'bg-[#1877F2]', 'url' => "https://www.facebook.com/sharer/sharer.php?u={$currentUrl}", 'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
                        ['id' => 'x', 'bg' => 'bg-black', 'url' => "https://twitter.com/intent/tweet?url={$currentUrl}&text={$pageTitle}", 'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
                        ['id' => 'wa', 'bg' => 'bg-[#25D366]', 'url' => "https://api.whatsapp.com/send?text={$pageTitle}%20{$currentUrl}", 'icon' => '<path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.399 1.676zm6.208-3.904c1.602.952 3.513 1.455 5.461 1.456 5.487 0 9.954-4.467 9.957-9.953.003-5.487-4.464-9.953-9.952-9.953-2.658 0-5.155 1.035-7.033 2.913-1.878 1.878-2.91 4.373-2.912 7.031-.001 2.052.539 4.05 1.564 5.792l-1.026 3.748 3.841-1.003z"/>'],
                        ['id' => 'tg', 'bg' => 'bg-[#0088cc]', 'url' => "https://t.me/share/url?url={$currentUrl}&text={$pageTitle}", 'icon' => '<path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.462 15.584c0 0-.211.234-.51.215-.298-.019-1.282-.55-1.282-.55s-1.127-.723-1.894-1.246c0 0-.411-.321-.183-.694.227-.373 1.601-1.45 1.601-1.45s1.283-1.164 1.745-1.565c0 0 .227-.234.183-.377-.044-.143-.376-.044-.376-.044s-1.896.486-2.667.669c0 0-.15.034-.332-.01-.182-.044-.666-.217-1.077-.353 0 0-.263-.105-.21-.301s.42-.317.42-.317 3.593-1.43 4.819-1.921c0 0 .399-.174.577.01s.211.396.166.577c-.113.435-.853 3.585-1.18 5.152l-.198 1.08z"/>'],
                        ['id' => 'pt', 'bg' => 'bg-[#E60023]', 'url' => "https://pinterest.com/pin/create/button/?url={$currentUrl}&media=" . urlencode($article->image_url) . "&description={$pageTitle}", 'icon' => '<path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.966 1.406-5.966s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.259 7.929-7.259 4.164 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.607 0 11.985-5.365 11.985-11.987C24.02 5.367 18.632 0 12.017 0z"/>'],
                        ['id' => 'xi', 'bg' => 'bg-[#005a5c]', 'url' => "https://www.xing.com/spi/shares/new?url={$currentUrl}", 'icon' => '<path d="M18.847 1.977c-.456 0-.817.202-1.026.586l-6.216 10.923c.091.13.196.28.299.417l7.73 13.916c.3.56.778.895 1.341.895h4.162l-7.796-14.364 6.84-12.373h-4.34zm-13.418 5.67c-.443 0-.825.212-1.04.606l-2.072 3.655l4.316 7.625h4.218l-4.223-7.701 2.502-4.185h-4.301"/>'],
                        ['id' => 'em', 'bg' => 'bg-gray-900 dark:bg-black', 'url' => "mailto:?subject={$pageTitle}&body={$currentUrl}", 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
                    ];
                @endphp

                @foreach($platforms as $p)
                    <a href="{{ $p['url'] }}" target="_blank" 
                       class="{{ $p['bg'] }} h-12 w-12 flex items-center justify-center rounded-lg text-white hover:scale-110 hover:-translate-y-1 transition-all duration-300 shadow-lg shadow-black/10 group" 
                       title="{{ $p['id'] }}">
                        <svg class="h-6 w-6 {{ $p['id'] === 'em' ? '' : 'fill-current' }} group-hover:drop-shadow-md" {!! $p['id'] === 'em' ? 'fill="none" stroke="currentColor"' : '' !!} viewBox="0 0 24 24">
                            {!! $p['icon'] !!}
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-12 pt-10 border-t border-gray-200 dark:border-gray-800/50">
            <div class="bg-white dark:bg-white/[0.02] rounded-lg p-6 md:p-8 flex flex-col md:flex-row items-center md:items-start gap-8 border border-gray-200 dark:border-white/5 relative overflow-hidden group">
                <!-- Avatar Container -->
                <div class="relative shrink-0">
                    <img src="{{ $article->author?->avatar_url ?? 'https://ui-avatars.com/api/?name=AI&background=0284c7&color=fff' }}" 
                         class="relative h-20 w-20 rounded-lg border-2 border-white dark:border-gray-800 object-cover shadow-xl">
                </div>

                <div class="text-left flex-1 relative z-10 pt-1">
                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-[9px] font-black text-cyan-700 dark:text-cyan-500 uppercase tracking-widest mb-3 inline-block">{{ __('ui.verified_author') }}</span>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">
                        {{ $article->author?->name ?? 'AI Reporter' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-6 max-w-2xl">
                        {{ $article->author?->bio ?? 'Analizando y curando las noticias tecnológicas más relevantes del mundo.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Article Tags Section -->
        @if($article->tags && $article->tags->count() > 0)
            <div class="mt-12 flex flex-wrap items-center gap-3 justify-start">
                @foreach($article->tags as $atag)
                    <a href="{{ route('tags.show', $atag->slug) }}" 
                       class="px-5 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest hover:bg-cyan-600 hover:text-white dark:hover:bg-cyan-500 dark:hover:text-white transition-all duration-300 border border-gray-200 dark:border-transparent">
                        #{{ $atag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Source & AI Disclosure -->
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-white/5">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 italic mb-6 leading-relaxed max-w-2xl">
                {{ __('ui.content_disclaimer') }}
            </p>
            
            @if(!empty($article->ai_metadata['origin_url']))
                <a href="{{ $article->ai_metadata['origin_url'] }}" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-3 bg-slate-100 dark:bg-white/5 hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-500 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 group shadow-sm">
                    <svg class="w-4 h-4 text-cyan-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    {{ __('ui.read_original_source') }}
                </a>
            @endif
        </div>
    </article>

    <x-slot:sidebar>
        @if($relatedArticles->count() > 0)
            <div class="flex flex-col gap-8">
                <div class="flex items-center gap-4 mb-2">
                    <span class="w-8 h-1 bg-cyan-600 dark:bg-cyan-500 rounded-lg"></span>
                    <h3 class="text-xs font-black tracking-widest uppercase text-gray-600 dark:text-gray-400">
                        {{ __('ui.recommended') }}
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 gap-8">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $related->slug_es : $related->slug_en) }}" 
                           class="group flex gap-5 items-center">
                            <div class="w-20 h-20 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm relative">
                                <img src="{{ $related->image_url ?? '/placeholder.webp' }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug group-hover:text-cyan-600 dark:group-hover:text-cyan-500 transition-colors">
                                    {{ $related->title }}
                                </h4>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ $related->published_at?->format('M d') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </x-slot>
</x-layouts.app>
