<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-sparkles"
        collapsible
    >
        <x-slot name="heading">
            <span class="text-base font-black tracking-tight uppercase text-gray-900 dark:text-white">
                Pipeline de Ingesta Inteligente & Curaduría con IA
            </span>
        </x-slot>

        <x-slot name="description">
            El sistema absorbe todos los feeds RSS y utiliza un Evaluador Editorial con IA para filtrar y publicar únicamente las noticias de mayor impacto periodístico.
        </x-slot>

        <x-slot name="headerEnd">
            <div class="flex items-center gap-2">
                <x-filament::badge color="gray" icon="heroicon-o-command-line">
                    php artisan rss:fetch
                </x-filament::badge>
            </div>
        </x-slot>

        <div class="space-y-6">
            <!-- AI Curation Architecture Banner -->
            <div class="rounded-xl p-4 bg-gradient-to-r from-cyan-500/10 via-slate-500/5 to-blue-500/10 border border-cyan-500/20 dark:border-cyan-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <x-filament::badge color="info">AI Editorial Ranker</x-filament::badge>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">Curaduría Inteligente en 2 Fases</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                        <strong>Fase 1 (Ingesta):</strong> Se descargan las noticias recientes a <code class="px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-800 text-[11px]">raw_articles</code> (Costo = $0).<br>
                        <strong>Fase 2 (Evaluación):</strong> La IA califica del 1 al 10 el Impacto Tecnológico y el Search Intent. Solo las notas con <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Score &ge; 7.0</strong> se redactan a >700 palabras y generan imagen en R2. Las notas menores quedan marcadas como <span class="text-amber-600 dark:text-amber-400 font-semibold">Ignoradas</span>.
                    </p>
                </div>
                <div class="shrink-0 font-mono text-[11px] text-gray-500 dark:text-gray-400">
                    Umbral Élite: <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20">&ge; 7.0 / 10</span>
                </div>
            </div>

            <!-- Native 3-Column Fieldset Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Fieldset 1: Frecuencia -->
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
                            Minutos que espera el cron entre cada consulta al feed RSS (ej. <span class="font-bold text-primary-600 dark:text-primary-400">60</span> = cada hora, <span class="font-bold text-primary-600 dark:text-primary-400">120</span> = cada 2 horas).
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary-600 dark:text-primary-400">60m</span>
                                <span class="font-sans text-gray-500">Escaneo cada 1 hora</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary-600 dark:text-primary-400">120m</span>
                                <span class="font-sans text-gray-500">Escaneo cada 2 horas</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 2: Score de Salud -->
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
                            Métrica automática de estabilidad técnica. Premia feeds activos y penaliza URLs con caídas o errores.
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

                <!-- Fieldset 3: Máx. Días -->
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

                <!-- Fieldset 4: Verificada (Tier 1) -->
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

                <!-- Fieldset 5: Estado Activa -->
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

                <!-- Fieldset 6: Master Switch -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Master Switch Global
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Control Central</span>
                            <x-filament::badge color="info">Global</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Interruptor superior que congela o reactiva toda la ingesta de noticias del portal a cero llamadas de IA.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">🟢 Ingesta ACTIVA</span>
                                <span class="text-gray-500">Cron funcionando</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-rose-500">⏸️ Ingesta PAUSADA</span>
                                <span class="text-gray-500">Cero consumo</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>