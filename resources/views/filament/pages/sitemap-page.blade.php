<x-filament-panels::page>
    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Banner de Cabecera -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 md:p-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Indexación Dinámica en Tiempo Real
                    </div>
                    <h2 class="text-xl font-black tracking-tight text-gray-900 dark:text-white">
                        Gestor de Sitemaps XML & Motores de Búsqueda
                    </h2>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                        Los sitemaps se compilan dinámicamente desde PostgreSQL. Cada vez que publicas una noticia, la caché se vacía automáticamente y se envía una notificación a los buscadores.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="https://search.google.com/search-console" target="_blank" class="px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-xs font-bold hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors">
                        Google Search Console &nearr;
                    </a>
                    <a href="https://www.bing.com/webmasters" target="_blank" class="px-4 py-2.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 text-xs font-bold hover:bg-teal-100 dark:hover:bg-teal-900/60 transition-colors">
                        Bing Webmaster Tools &nearr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista Detallada de Sitemaps (Formato Fila Espaciosa) -->
        <div class="space-y-4">
            <div class="border-b border-gray-200 dark:border-gray-800 pb-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white">
                    Estructura de Sitemaps Disponibles (7 Subsistemas)
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @foreach($sitemaps as $sitemap)
                    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-800/80 bg-white dark:bg-gray-900 p-6 shadow-sm hover:border-gray-300 dark:hover:border-gray-700 transition-all">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <!-- Información Principal -->
                            <div class="space-y-3 max-w-4xl">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="px-2.5 py-1 text-xs font-black rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                                        {{ $sitemap['badge'] }}
                                    </span>
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        {{ $sitemap['records'] }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                                        Caché: {{ $sitemap['ttl'] }}
                                    </span>
                                </div>

                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ $sitemap['title'] }}
                                    </h4>
                                    <div class="mt-1 font-mono text-xs text-primary-600 dark:text-primary-400 bg-gray-50 dark:bg-gray-800/60 px-2.5 py-1 rounded-md inline-block">
                                        {{ $sitemap['url'] }}
                                    </div>
                                </div>

                                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $sitemap['description'] }}
                                </p>
                            </div>

                            <!-- Botón de Inspección -->
                            <div class="shrink-0 pt-2">
                                <a href="{{ $sitemap['url'] }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors shadow-xs">
                                    <span>Abrir XML</span>
                                    <span class="text-sm">&nearr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Guía de Funcionamiento y Preguntas Frecuentes -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 md:p-8 shadow-sm space-y-6">
            <div class="border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white">
                    Guía de Funcionamiento y Preguntas Frecuentes
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Todo lo que necesitas saber sobre cómo se gestiona la indexación de Glodaxia
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Punto 1 -->
                <div class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        1. Generación Dinámica
                    </span>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                        ¿Cómo se compila el XML?
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        No existen archivos estáticos en disco. Cada vez que un bot o usuario ingresa a una ruta <code class="font-mono text-gray-700 dark:text-gray-300">/sitemap...xml</code>, Laravel consulta la base de datos y arma el XML en tiempo de ejecución.
                    </p>
                </div>

                <!-- Punto 2 -->
                <div class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                        2. Ciclo de Vida de Caché
                    </span>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                        ¿Cuándo se actualiza la información?
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        Para máxima velocidad, los sitemaps se guardan en caché temporal. En el momento en que se publica o edita una noticia, el sistema vacía la caché automáticamente con <code class="font-mono text-gray-700 dark:text-gray-300">flushCache()</code>.
                    </p>
                </div>

                <!-- Punto 3 -->
                <div class="p-5 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                        3. Notificación IndexNow
                    </span>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                        ¿Cómo se envía a los buscadores?
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        El sistema notifica automáticamente a Bing, Yandex y Seznam mediante el protocolo IndexNow. En Google Search Console solo debes ingresar una única vez la URL <code class="font-mono text-gray-700 dark:text-gray-300">/sitemap.xml</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>