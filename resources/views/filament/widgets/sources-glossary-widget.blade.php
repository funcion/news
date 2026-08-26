<x-filament-widgets::widget>
    <div class="mt-8 overflow-hidden rounded-2xl border border-gray-200/80 dark:border-gray-800/80 bg-white/80 dark:bg-gray-900/90 shadow-sm backdrop-blur-sm">
        <!-- Header banner -->
        <div class="border-b border-gray-100 dark:border-gray-800/80 bg-gradient-to-r from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900/90 dark:to-gray-900 px-6 py-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-cyan-500 shadow-sm shadow-cyan-500/50"></span>
                    <div>
                        <h3 class="text-sm font-black tracking-tight text-gray-900 dark:text-white uppercase">
                            Guía Operativa & Glosario de Ingesta RSS
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Referencia sobre el Interruptor Maestro, parámetros de control y política editorial de fuentes
                        </p>
                    </div>
                </div>

                <!-- Live Master Switch Status Pill -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border {{ \App\Models\Setting::get('ingestion_enabled', true) ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20' }}">
                    <span class="w-2 h-2 rounded-full {{ \App\Models\Setting::get('ingestion_enabled', true) ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    <span>Interruptor Maestro: {{ \App\Models\Setting::get('ingestion_enabled', true) ? 'ACTIVO (Ingesta en curso)' : 'PAUSADO (Cron en espera)' }}</span>
                </div>
            </div>
        </div>

        <!-- Master Switch Section -->
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-800/60 bg-slate-50/50 dark:bg-slate-900/40">
            <div class="flex items-start gap-4">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-cyan-50 dark:bg-cyan-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-cyan-700 dark:text-cyan-300 border border-cyan-200/60 dark:border-cyan-800/60 shadow-xs">
                        Master Switch
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                        <strong class="font-bold text-gray-900 dark:text-white">Interruptor Maestro Global:</strong> Control central situado en la cabecera superior derecha (<code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 font-mono text-[11px]">Ingesta: ACTIVA / PAUSADA</code>). Permite congelar o reanudar instantáneamente todo el flujo de ingesta sin modificar la configuración individual de cada feed.
                    </p>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                        <li><strong>Al Pausar:</strong> El comando programado (<code class="px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-800 font-mono text-[10px]">rss:fetch</code>) aborta en 1ms, evitando consultas HTTP externas, encolado de trabajos y consumo de tokens en DeepSeek V4 Flash y FLUX.1.</li>
                        <li><strong>Al Reanudar:</strong> El programador retoma el ciclo normal escaneando únicamente las fuentes con estado <em>Activa</em> según su frecuencia.</li>
                        <li><strong>Control por Terminal:</strong> <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-800 font-mono text-[11px]">php artisan ingestion:control [status | pause | resume | toggle]</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- List items with single paragraph per column -->
        <div class="divide-y divide-gray-100 dark:divide-gray-800/60 p-2 sm:p-4">
            <!-- Límite (Posts) -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 bg-cyan-50/30 dark:bg-cyan-950/20 border border-cyan-100 dark:border-cyan-900/30">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 px-2.5 py-1 text-xs font-black tracking-wider border border-cyan-500/20 shadow-xs">
                        Límite (Posts)
                    </span>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                        <strong class="font-bold text-gray-900 dark:text-white">Límite Estricto de Noticias por Escaneo:</strong> Controla cuántos artículos extrae el sistema de este feed RSS en cada corrida del cron.
                    </p>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                        <li><strong class="font-semibold text-cyan-600 dark:text-cyan-400">Valor = 0 (Sin Límite):</strong> El sistema absorbe <em>todas</em> las noticias disponibles en el feed RSS sin restricción.</li>
                        <li><strong class="font-semibold text-cyan-600 dark:text-cyan-400">Valor > 0 (ej. 2, 3, 5):</strong> El sistema extrae únicamente las <em>N</em> noticias más recientes y destacadas del feed en cada ciclo, evitando saturar la cola.</li>
                    </ul>
                </div>
            </div>

            <!-- Freq (min) -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 shadow-xs">
                        Freq (min)
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Frecuencia de Ingesta:</strong> Minutos entre cada consulta al feed RSS. El programador en segundo plano solo revisa este feed cuando se cumple este intervalo de tiempo (ej. <span class="font-semibold text-blue-600 dark:text-blue-400">60</span> = cada hora, <span class="font-semibold text-blue-600 dark:text-blue-400">120</span> = cada 2 horas).
                </p>
            </div>

            <!-- Score (Salud) -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shadow-xs">
                        Score (Salud)
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Índice de Fiabilidad:</strong> Salud técnica del feed evaluada automáticamente. Suma <strong class="font-bold text-emerald-600 dark:text-emerald-400">+2 puntos</strong> cuando entrega noticias nuevas exitosamente y resta <strong class="font-bold text-rose-600 dark:text-rose-400">-5 puntos</strong> si la URL falla o da error de conexión.
                </p>
            </div>

            <!-- Máx. Días -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-purple-50 dark:bg-purple-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-purple-700 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60 shadow-xs">
                        Máx. Días
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Filtro de Antigüedad:</strong> Límite de frescura de publicación. El sistema descarta automáticamente cualquier noticia del feed cuya fecha original sea anterior a este número de días (ej. <span class="font-semibold text-purple-600 dark:text-purple-400">1 día</span> para noticias del día).
                </p>
            </div>

            <!-- Verificada -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-amber-50 dark:bg-amber-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60 shadow-xs">
                        Verificada
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Fuente Oficial (Tier 1):</strong> Marca de medio o portal verificado de alta reputación periodística (OpenAI, MIT Tech Review, TechCrunch, The Verge, Bleeping Computer, Search Engine Land, Ahrefs, Next.js Releases). Tienen máxima prioridad en la cola de Horizon.
                </p>
            </div>

            <!-- Activa -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-teal-50 dark:bg-teal-950/50 px-2.5 py-1 text-xs font-black tracking-wider text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60 shadow-xs">
                        Activa
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Interruptor de Ingesta Individual:</strong> Interruptor rápido para pausar o reactivar la sincronización de este feed específico directamente desde la tabla con un solo clic, sin borrar su configuración.
                </p>
            </div>

            <!-- Última Ingesta -->
            <div class="group flex items-start gap-4 rounded-xl p-4 transition-all duration-200 hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                <div class="shrink-0 pt-0.5">
                    <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-xs font-black tracking-wider text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-xs">
                        Última Ingesta
                    </span>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                    <strong class="font-bold text-gray-900 dark:text-white">Sincronización:</strong> Registro de fecha y hora exacta del último escaneo completado con éxito por el comando de ingesta en segundo plano.
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>