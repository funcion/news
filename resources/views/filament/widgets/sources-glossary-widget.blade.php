<x-filament-widgets::widget>
    <div x-data="{ open: true }" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Glosario de Fuentes RSS (Lógica de Ingesta)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Guía rápida del funcionamiento de las columnas del sistema</p>
                </div>
            </div>
            <button type="button" class="text-xs font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex items-center gap-1">
                <span x-text="open ? 'Ocultar' : 'Ver Guía'"></span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Frecuencia -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Freq (min)</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Frecuencia de Ingesta</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Minutos que transcurren entre cada consulta al feed RSS. El programador (cron) solo revisa este feed cuando se cumple este intervalo (ej. <strong class="text-gray-700 dark:text-gray-300">60</strong> = 1 hora, <strong class="text-gray-700 dark:text-gray-300">120</strong> = 2 horas).
                </p>
            </div>

            <!-- Score -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">Score (Salud)</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Índice de Fiabilidad</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Puntuación dinámica de salud del feed: Suma <strong class="text-emerald-600 dark:text-emerald-400">+2 puntos</strong> si entrega noticias nuevas, y resta <strong class="text-rose-600 dark:text-rose-400">-5 puntos</strong> si la URL falla o da error de conexión.
                </p>
            </div>

            <!-- Máx. Días -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">Máx. Días</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Filtro de Antigüedad</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Límite de frescura: El sistema descarta automáticamente cualquier noticia cuya fecha de publicación original sea más antigua que este número de días (ej. <strong class="text-gray-700 dark:text-gray-300">1 día</strong> para noticias frescas).
                </p>
            </div>

            <!-- Verificada (Trusted) -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">Verificada</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Fuente Oficial / Prioritaria</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Identifica medios y canales primarios de alta reputación periodística. Sus artículos reciben prioridad en la cola de procesamiento editorial.
                </p>
            </div>

            <!-- Activa (Is Active) -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300">Activa</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Interruptor de Ingesta</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Permite pausar o reactivar la sincronización automática de un feed con un solo clic sin necesidad de borrar la configuración.
                </p>
            </div>

            <!-- Última Ingesta -->
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">Última Ingesta</span>
                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200">Hora de Sincronización</h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Timestamp exacto del último escaneo exitoso realizado por el comando en segundo plano.
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>