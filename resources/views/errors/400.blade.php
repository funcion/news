<x-layouts.app :title="'400 - ' . (app()->getLocale() === 'es' ? 'Solicitud Incorrecta' : 'Bad Request') . ' | ' . config('app.name')">
    <div class="max-w-2xl mx-auto px-4 py-16 lg:py-24 text-center">
        <span class="inline-block px-3 py-1 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-black uppercase tracking-widest border border-amber-500/20 mb-4">
            400
        </span>

        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
            {{ app()->getLocale() === 'es' ? 'Solicitud Incorrecta' : 'Bad Request' }}
        </h1>

        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto mb-8 font-normal">
            {{ app()->getLocale() === 'es' 
                ? 'El servidor no pudo procesar la solicitud debido a un formato no reconocido.' 
                : 'The server could not understand the request due to malformed syntax.' }}
        </p>

        <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#2b7fff] hover:bg-blue-600 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>{{ app()->getLocale() === 'es' ? 'Volver al Inicio' : 'Return to Home' }}</span>
        </a>
    </div>
</x-layouts.app>