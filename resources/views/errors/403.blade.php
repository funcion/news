<x-layouts.app :title="'403 - ' . (app()->getLocale() === 'es' ? 'Acceso Restringido' : 'Access Forbidden') . ' | ' . config('app.name')">
    <div class="min-h-[65vh] flex items-center justify-center px-4 py-16 lg:py-24 relative overflow-hidden">
        <div class="max-w-2xl w-full text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-500/40 text-amber-800 dark:text-amber-300 text-xs font-black uppercase tracking-widest mb-6 shadow-sm">
                <span>{{ app()->getLocale() === 'es' ? 'Error 403 ● Acceso Denegado' : 'Error 403 ● Access Forbidden' }}</span>
            </div>

            <h1 class="text-8xl sm:text-9xl md:text-[10rem] font-black tracking-tighter mb-4 bg-gradient-to-br from-slate-900 via-amber-600 to-rose-600 dark:from-white dark:via-amber-400 dark:to-rose-500 bg-clip-text text-transparent select-none leading-none">
                403
            </h1>

            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Zona de acceso restringido' : 'Restricted access zone' }}
            </h2>

            <p class="text-base text-slate-700 dark:text-slate-200 leading-relaxed max-w-lg mx-auto mb-10 font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'No dispones de las credenciales o permisos requeridos para consultar este recurso.' 
                    : 'You do not have the required permissions to access this administrative resource.' }}
            </p>

            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" 
               class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#2b7fff] hover:bg-blue-600 text-white text-sm md:text-base font-black tracking-wide shadow-xl shadow-blue-500/25 transition-all">
                <span>{{ app()->getLocale() === 'es' ? 'Volver a la Portada' : 'Return to Homepage' }}</span>
            </a>
        </div>
    </div>
</x-layouts.app>