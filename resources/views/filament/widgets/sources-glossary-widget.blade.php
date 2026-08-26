<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-book-open"
        collapsible
    >
        <x-slot name="heading">
            <span class="text-base font-black tracking-tight uppercase text-gray-900 dark:text-white">
                Guía Operativa y Glosario de Ingesta RSS
            </span>
        </x-slot>

        <x-slot name="description">
            Manual de referencia técnica para la configuración y límites del programador de contenidos.
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex items-center gap-2">
                <x-filament::badge color="gray" icon="heroicon-o-command-line">
                    php artisan rss:fetch
                </x-filament::badge>
            </div>
        </x-slot>

        <div class="space-y-6">
            <!-- Master Switch Info Banner -->
            <div class="rounded-xl p-4 bg-gray-50 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <x-filament::badge color="info">Master Switch Global</x-filament::badge>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">Interruptor Maestro de Sincronización</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        Control general en la barra superior. Si se <strong class="text-gray-900 dark:text-white">Pausa</strong>, el sistema aborta de inmediato las lecturas RSS y congela el consumo de tokens en IA a cero sin alterar la configuración de cada feed.
                    </p>
                </div>
                <div class="shrink-0 font-mono text-[11px] text-gray-500 dark:text-gray-400">
                    Control CLI: <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 font-bold">ingestion:control</code>
                </div>
            </div>

            <!-- Native 3-Column Fieldset Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Fieldset 1: Límite de Ingesta -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Límite (Posts)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="info">fetch_limit</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Número de artículos a extraer de este feed en cada corrida para evitar saturar la cola de procesamiento.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">0</span>
                                <span class="font-sans text-gray-500">Sin límite (Todo el feed)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">3</span>
                                <span class="font-sans text-gray-500">Top 3 más recientes</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 2: Frecuencia -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Frecuencia (min)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="primary">frequency</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Minutos que espera el programador en segundo plano entre cada consulta a la URL del feed.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary-600 dark:text-primary-400">60m</span>
                                <span class="font-sans text-gray-500">Consulta cada 1 hora</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary-600 dark:text-primary-400">120m</span>
                                <span class="font-sans text-gray-500">Consulta cada 2 horas</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 3: Score de Salud -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Score (Salud)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="success">0 - 100 pts</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Métrica automática de estabilidad. Premia feeds activos y penaliza fuentes con caídas o errores.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">+2 pts</span>
                                <span class="font-sans text-gray-500">Por escaneo con notas</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-rose-500">-5 pts</span>
                                <span class="font-sans text-gray-500">Por error de conexión</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 4: Máx. Días -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Máx. Días (Frescura)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="warning">max_age_days</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Filtro cronológico. Descarta automáticamente noticias que superen este número de días de antigüedad.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-600 dark:text-amber-400">1 día</span>
                                <span class="font-sans text-gray-500">Solo noticias de hoy</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-600 dark:text-amber-400">3 días</span>
                                <span class="font-sans text-gray-500">Ventana de 72 horas</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 5: Verificada -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Verificada (Tier 1)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="danger">trusted</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Distintivo de medios oficiales de élite (Ars Technica, MIT Tech, Hugging Face, Bleeping Computer).
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-rose-600 dark:text-rose-400">Prioridad Alta</span>
                                <span class="text-gray-500">En cola de Horizon</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-700 dark:text-gray-300">EEAT Score</span>
                                <span class="text-gray-500">Autor verificado</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 6: Estado Activa -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Estado Activa
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Parámetro</span>
                            <x-filament::badge color="success">is_active</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Interruptor individual para pausar o activar la lectura de este feed sin borrarlo de la base de datos.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">🟢 Activa</span>
                                <span class="text-gray-500">Sincronización ON</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-rose-500">🔴 Inactiva</span>
                                <span class="text-gray-500">Pausada por el admin</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>