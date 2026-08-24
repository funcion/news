<x-layouts.app :title="'404 - ' . (app()->getLocale() === 'es' ? 'Página No Encontrada' : 'Page Not Found') . ' | ' . config('app.name')">
    <div class="max-w-6xl mx-auto px-4 py-12 lg:py-20 relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-96 h-96 bg-cyan-500/10 dark:bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- 404 Hero Banner -->
        <div class="max-w-2xl mx-auto text-center relative z-10 mb-14">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-50 dark:bg-cyan-950/60 border border-cyan-200 dark:border-cyan-500/40 text-cyan-800 dark:text-cyan-300 text-xs font-black uppercase tracking-widest mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></span>
                <span>{{ app()->getLocale() === 'es' ? 'Error 404 ● Ruta No Encontrada' : 'Error 404 ● Page Not Found' }}</span>
            </div>

            <!-- Big Number -->
            <h1 class="text-8xl sm:text-9xl md:text-[10rem] font-black tracking-tighter mb-4 bg-gradient-to-br from-slate-900 via-cyan-700 to-blue-700 dark:from-white dark:via-cyan-300 dark:to-blue-400 bg-clip-text text-transparent select-none leading-none">
                404
            </h1>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Esta coordenada no existe en nuestro radar' : 'This coordinate is off our radar' }}
            </h2>

            <p class="text-base text-slate-700 dark:text-slate-200 leading-relaxed max-w-lg mx-auto mb-8 font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'El artículo o página que buscas no existe, ha sido trasladado o su URL ha cambiado.' 
                    : 'The article or page you are looking for does not exist, has been moved, or its URL has changed.' }}
            </p>

            <!-- Large Prominent Home Button -->
            <div class="flex items-center justify-center">
                <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
                   class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#2b7fff] hover:bg-blue-600 text-white text-sm md:text-base font-black tracking-wide shadow-xl shadow-blue-500/25 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ app()->getLocale() === 'es' ? 'Volver a la Portada' : 'Return to Homepage' }}</span>
                </a>
            </div>
        </div>

        <!-- Latest / Recent News Section -->
        @php
            try {
                $latestArticles = \App\Models\Article::published()
                    ->with(['category', 'user'])
                    ->latest('published_at')
                    ->take(3)
                    ->get();
            } catch (\Throwable $e) {
                $latestArticles = collect();
            }
        @endphp

        @if($latestArticles->isNotEmpty())
            <div class="pt-12 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-2.5 h-6 bg-cyan-500 rounded-full"></span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ app()->getLocale() === 'es' ? 'Noticias y Análisis Recientes' : 'Latest Tech News & Analyses' }}
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($latestArticles as $article)
                        <a href="{{ route('articles.show', ['slug' => $article->slug]) }}" 
                           class="group flex flex-col rounded-2xl overflow-hidden bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 hover:border-cyan-500/50 dark:hover:border-cyan-500/50 transition-all p-4 shadow-sm hover:shadow-md">
                            <div class="w-full aspect-[16/9] rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-800 relative">
                                @if($article->image)
                                    <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 dark:text-slate-500">
                                        <svg class="w-8 h-8 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                    </div>
                                @endif
                            </div>
                            @if($article->category)
                                <span class="text-[10.5px] font-black uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-1.5">
                                    {{ $article->category->name }}
                                </span>
                            @endif
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors">
                                {{ $article->title }}
                            </h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>