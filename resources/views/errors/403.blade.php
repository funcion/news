<x-layouts.app :title="'403 - ' . (app()->getLocale() === 'es' ? 'Acceso Restringido' : 'Access Forbidden') . ' | ' . config('app.name')">
    <div class="min-h-[65vh] flex items-center justify-center px-4 py-16 lg:py-24 relative overflow-hidden">
        <div class="max-w-2xl w-full text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-black uppercase tracking-widest mb-6">
                <span>{{ app()->getLocale() === 'es' ? 'Error 403 ● Acceso Denegado' : 'Error 403 ● Access Forbidden' }}</span>
            </div>

            <h1 class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tighter mb-4 bg-gradient-to-br from-slate-900 via-amber-600 to-rose-600 dark:from-white dark:via-amber-400 dark:to-rose-500 bg-clip-text text-transparent select-none leading-none">
                403
            </h1>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Zona de acceso restringido' : 'Restricted access zone' }}
            </h2>

            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-lg mx-auto mb-10 font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'No dispones de las credenciales o permisos requeridos para consultar este recurso.' 
                    : 'You do not have the required permissions to access this administrative resource.' }}
            </p>

            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#2b7fff] hover:bg-blue-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/25 transition-all">
                <span>{{ app()->getLocale() === 'es' ? 'Volver a la Portada' : 'Return to Homepage' }}</span>
            </a>
        </div>
    </div>
</x-layouts.app>