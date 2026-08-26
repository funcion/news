<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-book-open"
        collapsible
    >
        <x-slot name="heading">
            <span class="text-base font-black tracking-tight uppercase text-gray-900 dark:text-white">
                Guía de Columnas y Operativa · Laboratorio de Noticias Crudas
            </span>
        </x-slot>

        <x-slot name="description">
            Manual de referencia técnica para la gestión, estados y evaluación editorial con IA de las noticias crudas.
        </x-slot>

        <div class="space-y-6">
            <!-- Summary Info Banner -->
            <div class="rounded-xl p-4 bg-gradient-to-r from-blue-500/10 via-slate-500/5 to-cyan-500/10 border border-blue-500/20 dark:border-blue-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <x-filament::badge color="info">Laboratorio de Ingesta</x-filament::badge>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">Bandeja de Entrada de Contenidos</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                        Todas las noticias extraídas de los feeds RSS o creadas manualmente como "Semillas de Ideas" entran a esta tabla. Aquí son evaluadas por el <strong>AI Editorial Ranker</strong> antes de pasar a la redacción y generación de imágenes.
                    </p>
                </div>
                <div class="shrink-0 font-mono text-[11px] text-gray-500 dark:text-gray-400">
                    Acción Rápida: <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20">Procesar y Publicar ⚡</span>
                </div>
            </div>

            <!-- Native 3-Column Fieldset Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Fieldset 1: Fuente y Título -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Fuente & Titular
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Columnas</span>
                            <x-filament::badge color="gray">source.name / title</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Identifica el medio periodístico emisor (o "Semilla Manual") y el titular original en inglés o español.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">Fuente RSS</span>
                                <span class="text-gray-500">Tier 1 Oficial</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-600 dark:text-amber-400">Semilla</span>
                                <span class="text-gray-500">Idea creada por el editor</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 2: Score IA -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Score IA (Evaluación)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Columna</span>
                            <x-filament::badge color="info">metadata.curation.score</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Calificación del 1 al 10 en Impacto Tecnológico y Search Intent. Al pasar el cursor, muestra el motivo editorial.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">&ge; 7.0 / 10</span>
                                <span class="font-sans text-gray-500">Élite (Pasa a Redacción)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-600 dark:text-amber-400">&lt; 7.0 / 10</span>
                                <span class="font-sans text-gray-500">Descartada (Ignorada)</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 3: Estado de Procesamiento -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Estado (Status)
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Columna</span>
                            <x-filament::badge color="success">status</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Ciclo de vida del artículo dentro de la cola asíncrona de procesamiento.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-500">Pendiente</span>
                                <span class="text-gray-400">En cola de evaluación</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-blue-500">Procesando</span>
                                <span class="text-gray-400">Redactando + Imagen R2</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Procesada</span>
                                <span class="text-gray-400">Publicada en Portada</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-500">Ignorada</span>
                                <span class="text-gray-400">Descartada (0 costo)</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 4: Modelo IA -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Modelo IA Asignado
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Columna</span>
                            <x-filament::badge color="primary">ai_model</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Motor de lenguaje que redactó el artículo final con el sistema de Style DNA periodístico.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs font-mono">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">DeepSeek Chat</span>
                                <span class="font-sans text-gray-500">Análisis y Ciberseguridad</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">GPT-4o Mini</span>
                                <span class="font-sans text-gray-500">Hardware y Startups</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 5: Fecha de Publicación -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Fecha de Origen
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Columna</span>
                            <x-filament::badge color="gray">published_at</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Timestamp original emitido por el medio de comunicación en el feed RSS (d/m/Y H:i).
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-700 dark:text-gray-300">Frescura</span>
                                <span class="text-gray-500">Ventana de 24h a 72h</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-700 dark:text-gray-300">Orden</span>
                                <span class="text-gray-500">Cronológico inverso</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

                <!-- Fieldset 6: Botones de Acción -->
                <x-filament::fieldset>
                    <x-slot name="label">
                        <span class="font-bold text-xs uppercase tracking-wider text-gray-900 dark:text-white">
                            Acciones Disponibles
                        </span>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Interacciones</span>
                            <x-filament::badge color="success">1 Clic</x-filament::badge>
                        </div>

                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                            Permite forzar o reintentar el procesamiento de cualquier noticia ignorada o pendiente.
                        </p>

                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-blue-600 dark:text-blue-400">Procesar con IA</span>
                                <span class="text-gray-500">Cola estándar</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Procesar y Publicar ⚡</span>
                                <span class="text-gray-500">Prioridad inmediata</span>
                            </div>
                        </div>
                    </div>
                </x-filament::fieldset>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>