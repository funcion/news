<x-layouts.app :title="'404 - ' . (app()->getLocale() === 'es' ? 'Página No Encontrada' : 'Page Not Found') . ' | ' . config('app.name')">
    <div class="min-h-[65vh] flex items-center justify-center px-4 py-16 lg:py-24 relative overflow-hidden">
        <!-- Ambient Glow Elements -->
        <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-96 h-96 bg-cyan-500/10 dark:bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-10 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-2xl w-full text-center relative z-10">
            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-cyan-500/10 dark:bg-cyan-500/20 border border-cyan-500/30 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest mb-6">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></span>
                <span>{{ app()->getLocale() === 'es' ? 'Error 404 ● Ruta No Encontrada' : 'Error 404 ● Page Not Found' }}</span>
            </div>

            <!-- Big Glowing 404 Number -->
            <h1 class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tighter mb-4 bg-gradient-to-br from-slate-900 via-cyan-600 to-blue-600 dark:from-white dark:via-cyan-400 dark:to-blue-500 bg-clip-text text-transparent select-none leading-none">
                404
            </h1>

            <!-- Headline and Explanation -->
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Esta coordenada no existe en nuestro radar' : 'This coordinate is off our radar' }}
            </h2>

            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-lg mx-auto mb-10 font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'El artículo, recurso o enlace que intentas consultar no existe, fue reestructurado o su dirección URL ha cambiado.' 
                    : 'The article, resource, or dispatch you are looking for does not exist, has been re-indexed, or was moved.' }}
            </p>

            <!-- Search Form on 404 -->
            <form action="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" method="GET" class="max-w-md mx-auto mb-10 relative">
                <div class="relative flex items-center">
                    <input type="text" 
                           name="q" 
                           placeholder="{{ app()->getLocale() === 'es' ? 'Buscar en Glodaxia...' : 'Search Glodaxia...' }}" 
                           class="w-full bg-slate-100 dark:bg-slate-900/90 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-4 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 transition-all placeholder:text-slate-400 shadow-sm">
                    <div class="absolute left-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="absolute right-2 px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-black uppercase tracking-wider transition-all shadow-md">
                        {{ app()->getLocale() === 'es' ? 'Buscar' : 'Search' }}
                    </button>
                </div>
            </form>

            <!-- Navigation Actions -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#2b7fff] hover:bg-blue-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ app()->getLocale() === 'es' ? 'Volver a la Portada' : 'Return to Homepage' }}</span>
                </a>
                
                <a href="{{ url(app()->getLocale() === 'es' ? '/es/editorial-policy' : '/editorial-policy') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-black uppercase tracking-widest border border-slate-200 dark:border-slate-700 transition-all">
                    <span>{{ app()->getLocale() === 'es' ? 'Política Editorial & IA' : 'Editorial & AI' }}</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>