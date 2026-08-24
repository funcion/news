<x-layouts.app :title="'500 - ' . (app()->getLocale() === 'es' ? 'Error del Servidor' : 'Server Error') . ' | ' . config('app.name')">
    <div class="min-h-[65vh] flex items-center justify-center px-4 py-16 lg:py-24 relative overflow-hidden">
        <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-96 h-96 bg-rose-500/10 dark:bg-rose-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-2xl w-full text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-rose-500/10 dark:bg-rose-500/20 border border-rose-500/30 text-rose-700 dark:text-rose-400 text-xs font-black uppercase tracking-widest mb-6">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                <span>{{ app()->getLocale() === 'es' ? 'Error 500 ● Servidor en Recuperación' : 'Error 500 ● Server Incident' }}</span>
            </div>

            <h1 class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tighter mb-4 bg-gradient-to-br from-slate-900 via-rose-600 to-amber-600 dark:from-white dark:via-rose-400 dark:to-amber-500 bg-clip-text text-transparent select-none leading-none">
                500
            </h1>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Interrupción temporal de servicio' : 'Temporary service interruption' }}
            </h2>

            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-lg mx-auto mb-10 font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Hemos experimentado una anomalía inesperada. Nuestro equipo de infraestructura ha sido alertado y está trabajando en su resolución.' 
                    : 'We experienced an unexpected anomaly. Our engineering desk has been automatically alerted and is working on a fix.' }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-cyan-500/25 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>{{ app()->getLocale() === 'es' ? 'Reintentar Carga' : 'Retry Request' }}</span>
                </button>
                
                <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-black uppercase tracking-widest border border-slate-200 dark:border-slate-700 transition-all">
                    <span>{{ app()->getLocale() === 'es' ? 'Volver a la Portada' : 'Return to Homepage' }}</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>