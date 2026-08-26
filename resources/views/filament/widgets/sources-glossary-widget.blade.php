<x-filament-widgets::widget>
    <div class="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
        
        <!-- Table Header / Title Bar -->
        <div class="px-6 py-4.5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight uppercase">
                    Guía de Referencia Operativa · Parámetros de Ingesta RSS
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    Especificación de reglas, límites de extracción y comportamiento del programador.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-600 dark:text-slate-300 shadow-xs">
                <span class="text-cyan-600 dark:text-cyan-400 font-bold">CLI:</span>
                <code>php artisan rss:fetch</code>
            </div>
        </div>

        <!-- Structured Reference Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-6 w-48">Columna / Campo</th>
                        <th class="py-3.5 px-6">Propósito y Comportamiento</th>
                        <th class="py-3.5 px-6 w-72">Valores y Ejemplos</th>
                        <th class="py-3.5 px-6 w-36 text-center">Impacto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-normal text-slate-700 dark:text-slate-300">
                    
                    <!-- Row 1: Límite (Posts) -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-500/20">
                                Límite (Posts)
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">fetch_limit</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Límite estricto de noticias por escaneo.</strong> Controla cuántos artículos extrae el crawler de este feed en cada corrida para evitar saturar la cola de procesamiento.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 font-mono text-[11px]">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-cyan-600 dark:text-cyan-400">0</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Sin límite (Todo el feed)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-cyan-600 dark:text-cyan-400">3</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Top 3 más recientes</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                Crítico (IA/Colas)
                            </span>
                        </td>
                    </tr>

                    <!-- Row 2: Freq (min) -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20">
                                Freq (min)
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">frequency</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Frecuencia de consulta.</strong> Intervalo mínimo en minutos que espera el programador antes de volver a escanear esta URL.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 font-mono text-[11px]">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">60</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Consulta cada 1 hora</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">120</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Consulta cada 2 horas</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                Tiempo / Cron
                            </span>
                        </td>
                    </tr>

                    <!-- Row 3: Score (Salud) -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                Score (Salud)
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">score (0 - 100)</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Índice automático de fiabilidad técnica.</strong> Mide la estabilidad del feed en cada conexión HTTP.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 font-mono text-[11px]">
                                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold">
                                    <span>+2 pts</span>
                                    <span class="font-sans font-normal text-slate-600 dark:text-slate-400">Por escaneo con notas</span>
                                </div>
                                <div class="flex items-center gap-2 text-rose-500 font-bold">
                                    <span>-5 pts</span>
                                    <span class="font-sans font-normal text-slate-600 dark:text-slate-400">Por timeout o caída</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                Auto-Monitoreo
                            </span>
                        </td>
                    </tr>

                    <!-- Row 4: Máx. Días -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20">
                                Máx. Días
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">max_age_days</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Filtro de frescura cronológica.</strong> Descarta automáticamente cualquier noticia con fecha anterior al límite.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 font-mono text-[11px]">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-purple-600 dark:text-purple-400">1</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Solo noticias de hoy (24h)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-purple-600 dark:text-purple-400">3</span>
                                    <span class="font-sans text-slate-600 dark:text-slate-400">= Ventana de 72 horas</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                Frescura SEO
                            </span>
                        </td>
                    </tr>

                    <!-- Row 5: Verificada -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                Verificada
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">trusted (Tier 1)</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Fuentes oficiales de alta reputación.</strong> (Ars Technica, MIT Tech Review, Hugging Face, Bleeping Computer). Tienen máxima prioridad en la cola de Horizon.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 text-[11px]">
                                <div><strong class="text-amber-600 dark:text-amber-400">Prioridad Alta:</strong> Pasa directo a IA</div>
                                <div class="text-slate-500 dark:text-slate-400">Asignación automática de EEAT</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                Calidad Tier 1
                            </span>
                        </td>
                    </tr>

                    <!-- Row 6: Activa -->
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-500/20">
                                Activa
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-1">is_active</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Interruptor de ingesta individual.</strong> Permite pausar o reactivar la sincronización de este feed específico directamente desde la tabla con un clic.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 text-[11px]">
                                <div class="text-emerald-600 dark:text-emerald-400 font-bold">🟢 ON = Sincronizando</div>
                                <div class="text-slate-500 dark:text-slate-400">🔴 OFF = Pausada por el admin</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">
                                Control Rápido
                            </span>
                        </td>
                    </tr>

                    <!-- Row 7: Master Switch -->
                    <tr class="bg-cyan-50/30 dark:bg-cyan-950/20 hover:bg-cyan-50/50 dark:hover:bg-cyan-950/30 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-slate-900 dark:bg-white text-white dark:text-slate-950 shadow-xs">
                                Master Switch
                            </span>
                            <p class="text-[10px] text-cyan-600 dark:text-cyan-400 font-mono mt-1">Botón Superior</p>
                        </td>
                        <td class="py-4 px-6 align-top leading-relaxed">
                            <strong class="font-bold text-slate-900 dark:text-white">Interruptor Maestro Global.</strong> Congela o reanuda instantáneamente todo el flujo de ingesta del portal a cero llamadas de IA.
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="space-y-1 text-[11px]">
                                <div class="font-bold text-emerald-600 dark:text-emerald-400">🟢 Ingesta ACTIVA</div>
                                <div class="font-bold text-rose-500">⏸️ Ingesta PAUSADA</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-600 dark:bg-cyan-500 text-white dark:text-slate-950 font-mono">
                                GLOBAL
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>