<x-layouts.app :title="'404 - ' . (app()->getLocale() === 'es' ? 'Página No Encontrada' : 'Page Not Found') . ' | ' . config('app.name')">
    <div class="max-w-2xl mx-auto px-4 py-16 lg:py-24 text-center">
        <!-- 404 Header -->
        <span class="inline-block px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20 mb-4">
            404
        </span>

        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
            {{ app()->getLocale() === 'es' ? 'Página No Encontrada' : 'Page Not Found' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto mb-8 font-normal">
            {{ app()->getLocale() === 'es' 
                ? 'El artículo o página que buscas no existe o ha sido movido a otra dirección.' 
                : 'The article or page you are looking for does not exist or has been moved.' }}
        </p>

        <!-- Primary Button -->
        <div class="mb-12">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#2b7fff] hover:bg-blue-600 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>{{ app()->getLocale() === 'es' ? 'Volver al Inicio' : 'Return to Home' }}</span>
            </a>
        </div>

        <!-- Simple UL LI List of Recent News -->
        @php
            try {
                $latestArticles = \App\Models\Article::published()
                    ->latest('published_at')
                    ->take(5)
                    ->get();
            } catch (\Throwable $e) {
                $latestArticles = collect();
            }
        @endphp

        @if($latestArticles->isNotEmpty())
            <div class="text-left pt-8 border-t border-slate-200 dark:border-slate-800">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-4">
                    {{ app()->getLocale() === 'es' ? 'Últimas Noticias Publicadas' : 'Latest Published News' }}
                </h2>

                <ul class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($latestArticles as $article)
                        <li class="py-3 flex items-center justify-between gap-4">
                            <a href="{{ route('articles.show', ['slug' => $article->slug]) }}" 
                               class="text-sm font-medium text-slate-800 dark:text-slate-200 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors line-clamp-1">
                                {{ $article->title }}
                            </a>
                            <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0 font-medium">
                                {{ $article->published_at ? $article->published_at->format('d/m/Y') : '' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-layouts.app>