<x-filament-widgets::widget>
    <div class="mt-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 md:p-8 shadow-sm">
        <div class="border-b border-gray-200 dark:border-gray-800 pb-4 mb-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Glosario de Fuentes RSS (Lógica de Ingesta)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Guía de referencia sobre los parámetros y funcionamiento de cada columna:
            </p>
        </div>

        <div class="space-y-6 divide-y divide-gray-100 dark:divide-gray-800/60">
            <!-- Freq (min) -->
            <div class="pt-4 first:pt-0">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 mb-2">
                    Freq (min)
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Frecuencia de Ingesta:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Minutos entre cada consulta al feed RSS. El programador solo revisa este feed cuando se cumple este intervalo (ej. <strong class="text-gray-800 dark:text-gray-200">60</strong> = 1 hora, <strong class="text-gray-800 dark:text-gray-200">120</strong> = 2 horas).
                </p>
            </div>

            <!-- Score (Salud) -->
            <div class="pt-6">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 mb-2">
                    Score (Salud)
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Índice de Fiabilidad:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Salud técnica del feed. Suma <strong class="text-emerald-600 dark:text-emerald-400">+2 puntos</strong> con noticias nuevas y resta <strong class="text-rose-600 dark:text-rose-400">-5 puntos</strong> si la URL falla o da error de conexión.
                </p>
            </div>

            <!-- Máx. Días -->
            <div class="pt-6">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 mb-2">
                    Máx. Días
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Filtro de Antigüedad:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Límite de frescura. Descarta automáticamente cualquier noticia cuya fecha de publicación original sea anterior a X días (ej. <strong class="text-gray-800 dark:text-gray-200">1 día</strong>).
                </p>
            </div>

            <!-- Verificada -->
            <div class="pt-6">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 mb-2">
                    Verificada
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Fuente Oficial:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Identifica medios y canales oficiales de alta reputación. Sus artículos tienen prioridad en la cola de procesamiento editorial.
                </p>
            </div>

            <!-- Activa -->
            <div class="pt-6">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 mb-2">
                    Activa
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Interruptor de Ingesta:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Pausa o reactiva la sincronización del feed con un solo clic sin necesidad de borrar su configuración.
                </p>
            </div>

            <!-- Última Ingesta -->
            <div class="pt-6">
                <div class="inline-block px-2.5 py-1 text-xs font-bold rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 mb-2">
                    Última Ingesta
                </div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                    Sincronización:
                </h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mt-1.5 max-w-4xl">
                    Fecha y hora exacta del último escaneo exitoso realizado en segundo plano por el programador.
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>