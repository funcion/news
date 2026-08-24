<x-layouts.app :title="'500 - ' . (app()->getLocale() === 'es' ? 'Error del Servidor' : 'Server Error') . ' | ' . config('app.name')">
    <div class="max-w-2xl mx-auto px-4 py-16 lg:py-24 text-center">
        <span class="inline-block px-3 py-1 rounded-md bg-rose-500/10 text-rose-700 dark:text-rose-400 text-xs font-black uppercase tracking-widest border border-rose-500/20 mb-4">
            500
        </span>

        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
            {{ app()->getLocale() === 'es' ? 'Error del Servidor' : 'Server Error' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto mb-8 font-normal">
            {{ app()->getLocale() === 'es' 
                ? 'Ocurrió un error inesperado en el servidor. Por favor intenta recargar la página.' 
                : 'An unexpected server error occurred. Please try reloading the page.' }}
        </p>

        <div class="flex items-center justify-center gap-3">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#2b7fff] hover:bg-blue-600 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>{{ app()->getLocale() === 'es' ? 'Recargar' : 'Reload' }}</span>
            </button>
            
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-700 transition-colors">
                <span>{{ app()->getLocale() === 'es' ? 'Volver al Inicio' : 'Return Home' }}</span>
            </a>
        </div>
    </div>
</x-layouts.app>