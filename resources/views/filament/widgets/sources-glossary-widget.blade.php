<x-filament-widgets::widget>
    <div class="space-y-6">
        <!-- Master Switch Executive Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-cyan-500/10 via-slate-500/5 to-blue-500/10 p-6 border border-cyan-500/20 dark:border-cyan-500/30 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">
                                Guía Operativa y Control de Ingesta RSS
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-500/30">
                                Master Switch Global
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 max-w-3xl leading-relaxed">
                            Control central de sincronización. Si está <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Activo</strong>, el programador ejecuta la lectura según los límites individuales. Si se <strong class="text-rose-600 dark:text-rose-400 font-bold">Pausa</strong>, se abortan todas las llamadas HTTP y el consumo de tokens en IA se congela a cero.
                        </p>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-2 bg-white/80 dark:bg-slate-900/80 px-3.5 py-2 rounded-2xl border border-slate-200 dark:border-slate-800 text-[11px] font-mono text-slate-600 dark:text-slate-300 shadow-xs">
                    <span class="text-cyan-600 dark:text-cyan-400 font-bold">CLI:</span>
                    <code>php artisan rss:fetch</code>
                </div>
            </div>
        </div>

        <!-- 3-Column Modular Parameter Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Card 1: Límite (Posts) -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-cyan-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Límite (Posts)</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                            Volumen
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Número de artículos a extraer de este feed en cada corrida del cron para evitar saturar la cola.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-cyan-600 dark:text-cyan-400">0</span>
                        <span class="text-slate-500 dark:text-slate-400">Sin Límite (Todas las noticias)</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-cyan-600 dark:text-cyan-400">3</span>
                        <span class="text-slate-500 dark:text-slate-400">Top 3 más recientes</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Frecuencia (min) -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Freq (minutos)</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                            Tiempo
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Intervalo de tiempo en minutos entre cada consulta que realiza el programador a la URL.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400">60 min</span>
                        <span class="text-slate-500 dark:text-slate-400">Escaneo cada 1 hora</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400">120 min</span>
                        <span class="text-slate-500 dark:text-slate-400">Escaneo cada 2 horas</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Score (Salud) -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Score (Salud)</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            0 a 100
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Índice automático de fiabilidad técnica. Si la fuente entrega notas suma puntos; si falla resta.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">+2 pts</span>
                        <span class="text-slate-500 dark:text-slate-400">Por cada escaneo con notas</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-rose-500">-5 pts</span>
                        <span class="text-slate-500 dark:text-slate-400">Por timeout o error de conexión</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Máx. Días -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-purple-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Máx. Días</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                            Frescura
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Filtro de antigüedad. Descarta noticias cuya fecha de publicación supere este umbral.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-purple-600 dark:text-purple-400">1 día</span>
                        <span class="text-slate-500 dark:text-slate-400">Solo noticias del día de hoy</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-mono font-bold text-purple-600 dark:text-purple-400">3 días</span>
                        <span class="text-slate-500 dark:text-slate-400">Ventana de 72 horas</span>
                    </div>
                </div>
            </div>

            <!-- Card 5: Verificada (Tier 1) -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Verificada</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            Tier 1
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Etiqueta de prestigio periodístico oficial (Ars Technica, MIT Tech, Bleeping Computer, Hugging Face).
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-bold text-amber-600 dark:text-amber-400">Prioridad Alta</span>
                        <span class="text-slate-500 dark:text-slate-400">Atención rápida en Horizon</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-bold text-slate-500">EEAT Score</span>
                        <span class="text-slate-500 dark:text-slate-400">Citas con autor verificado</span>
                    </div>
                </div>
            </div>

            <!-- Card 6: Activa (Toggle) -->
            <div class="rounded-3xl p-5 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-teal-500/50 transition-all duration-200 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Estado Activa</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">
                            1 Clic
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Interruptor de sincronización rápida. Pausa o activa la lectura de este feed sin borrarlo.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800/80 text-[11px] space-y-1.5 font-medium">
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">🟢 Activa</span>
                        <span class="text-slate-500 dark:text-slate-400">Sincronización en curso</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                        <span class="font-bold text-rose-500">🔴 Inactiva</span>
                        <span class="text-slate-500 dark:text-slate-400">Pausada por el admin</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>