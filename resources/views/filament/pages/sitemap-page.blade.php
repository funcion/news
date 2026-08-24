<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Quick Informational Banner -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                        Sistema de Sitemaps Dinámicos & Indexación en Tiempo Real
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                        Los sitemaps se generan automáticamente en tiempo real directamente desde la base de datos de PostgreSQL. Cuando se publica un nuevo artículo, la caché se invalida al instante para que Googlebot y Bing siempre reciban la versión más fresca.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="https://search.google.com/search-console" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/>
                        </svg>
                        Google Search Console
                    </a>
                </div>
            </div>
        </div>

        <!-- Sitemaps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($sitemaps as $sitemap)
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm flex flex-col justify-between hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-md bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                {{ $sitemap['badge'] }}
                            </span>
                            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $sitemap['records'] }}
                            </span>
                        </div>

                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1.5">
                            {{ $sitemap['title'] }}
                        </h4>

                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-4">
                            {{ $sitemap['description'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2">
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 font-mono truncate max-w-[160px]">
                            {{ $sitemap['path'] }}
                        </div>
                        <a href="{{ $sitemap['url'] }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/60 transition-colors">
                            <span>Abrir XML</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- How it updates Explanation Card -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ¿Cómo y Cuándo se Actualiza el Sitemap?
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-gray-600 dark:text-gray-400">
                <div class="space-y-1.5 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800">
                    <strong class="font-bold text-gray-900 dark:text-white block">1. 100% Automático y en Tiempo Real:</strong>
                    Los sitemaps no son archivos estáticos que debas compilar manualmente; se generan dinámicamente mediante código PHP consultando la base de datos al momento.
                </div>

                <div class="space-y-1.5 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800">
                    <strong class="font-bold text-gray-900 dark:text-white block">2. Invalidador de Caché por Eventos:</strong>
                    Cada vez que el pipeline de IA publica o edita una noticia, el método <code class="text-primary-600 dark:text-primary-400">SitemapController::flushCache()</code> borra la caché, garantizando que el próximo bot de Google reciba el contenido más reciente.
                </div>

                <div class="space-y-1.5 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800">
                    <strong class="font-bold text-gray-900 dark:text-white block">3. Notificación Instantánea (IndexNow):</strong>
                    Además del sitemap pasivo, el sistema envía una solicitud HTTP activa al protocolo IndexNow para que los motores de búsqueda indexen la nueva URL en cuestión de minutos.
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>