<x-filament-widgets::widget>
    <div class="mt-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
        <div class="border-b border-gray-100 dark:border-gray-800 pb-3 mb-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                Glosario de Fuentes RSS (Lógica de Ingesta)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Guía de referencia sobre los parámetros y funcionamiento de cada columna:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Freq -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        Freq (min)
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Frecuencia de Ingesta:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Minutos entre cada consulta al feed RSS. El programador solo revisa este feed cuando se cumple este intervalo (ej. <strong class="text-gray-800 dark:text-gray-200">60</strong> = 1 hora, <strong class="text-gray-800 dark:text-gray-200">120</strong> = 2 horas).
                    </p>
                </div>
            </div>

            <!-- Score -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                        Score (Salud)
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Índice de Fiabilidad:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Salud técnica del feed. Suma <strong class="text-emerald-600 dark:text-emerald-400">+2 puntos</strong> con noticias nuevas y resta <strong class="text-rose-600 dark:text-rose-400">-5 puntos</strong> si la URL falla o da error de conexión.
                    </p>
                </div>
            </div>

            <!-- Máx. Días -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                        Máx. Días
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Filtro de Antigüedad:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Límite de frescura. Descarta automáticamente cualquier noticia cuya fecha de publicación original sea anterior a X días (ej. <strong class="text-gray-800 dark:text-gray-200">1 día</strong>).
                    </p>
                </div>
            </div>

            <!-- Verificada -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                        Verificada
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Fuente Oficial:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Identifica medios y canales oficiales de alta reputación. Sus artículos tienen prioridad en la cola de procesamiento editorial.
                    </p>
                </div>
            </div>

            <!-- Activa -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300">
                        Activa
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Interruptor de Ingesta:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Pausa o reactiva la sincronización del feed con un solo clic sin necesidad de borrar su configuración.
                    </p>
                </div>
            </div>

            <!-- Última Ingesta -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                        Última Ingesta
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                        Sincronización:
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                        Fecha y hora exacta del último escaneo exitoso realizado en segundo plano por el programador.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>