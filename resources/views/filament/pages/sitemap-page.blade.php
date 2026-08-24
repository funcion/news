<x-filament-panels::page>
    <style>
        .sitemap-card {
            background: var(--fi-section-bg, #ffffff);
            border: 1px solid rgba(150, 150, 150, 0.18);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .dark .sitemap-card {
            background: rgba(24, 24, 27, 0.7);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .sitemap-card:hover {
            border-color: rgba(14, 165, 233, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .sitemap-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            margin-right: 0.5rem;
        }
        .badge-master { background: #e0f2fe; color: #0369a1; }
        .dark .badge-master { background: rgba(3, 105, 161, 0.25); color: #7dd3fc; }
        
        .badge-news { background: #dcfce7; color: #15803d; }
        .dark .badge-news { background: rgba(21, 128, 61, 0.25); color: #86efac; }

        .badge-lang { background: #f3e8ff; color: #7e22ce; }
        .dark .badge-lang { background: rgba(126, 34, 206, 0.25); color: #d8b4fe; }

        .badge-tax { background: #fef3c7; color: #b45309; }
        .dark .badge-tax { background: rgba(180, 83, 9, 0.25); color: #fde68a; }

        .badge-img { background: #ffe4e6; color: #be123c; }
        .dark .badge-img { background: rgba(190, 18, 60, 0.25); color: #fda4af; }

        .sitemap-url-box {
            font-family: ui-monospace, monospace;
            font-size: 0.78rem;
            padding: 0.35rem 0.75rem;
            background: rgba(150, 150, 150, 0.08);
            border-radius: 0.5rem;
            display: inline-block;
            margin-top: 0.35rem;
            color: #0284c7;
        }
        .dark .sitemap-url-box {
            background: rgba(255, 255, 255, 0.05);
            color: #38bdf8;
        }
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .faq-item {
            padding: 1.25rem;
            border-radius: 0.75rem;
            background: rgba(150, 150, 150, 0.05);
            border: 1px solid rgba(150, 150, 150, 0.12);
        }
        .dark .faq-item {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.06);
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Banner Superior -->
        <x-filament::section>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.25rem;">
                        Indexación Dinámica & Protocolos de Sitemaps
                    </h3>
                    <p style="font-size: 0.85rem; color: #71717a; max-width: 800px; line-height: 1.4;">
                        Los sitemaps se generan al vuelo directamente desde la base de datos de PostgreSQL. Cuando se publica una noticia, la memoria caché se invalida y los buscadores reciben los datos más recientes.
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <x-filament::button href="https://search.google.com/search-console" tag="a" target="_blank" color="gray" size="sm">
                        Google Search Console &nearr;
                    </x-filament::button>
                    <x-filament::button href="https://www.bing.com/webmasters" tag="a" target="_blank" color="gray" size="sm">
                        Bing Webmaster Tools &nearr;
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        <!-- Lista de Sitemaps -->
        <div style="margin-top: 1.5rem;">
            <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #a1a1aa; margin-bottom: 1rem;">
                Estructura de Sitemaps Disponibles (7 Subsistemas)
            </h3>

            @foreach($sitemaps as $sitemap)
                @php
                    $badgeClass = match($sitemap['badge']) {
                        'Maestro' => 'badge-master',
                        'Google News 48h' => 'badge-news',
                        'Bilingüe ES', 'Bilingüe EN' => 'badge-lang',
                        'Google Images' => 'badge-img',
                        default => 'badge-tax',
                    };
                @endphp
                <div class="sitemap-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                        <div style="flex: 1; min-width: 280px;">
                            <div style="margin-bottom: 0.5rem; display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <span class="sitemap-badge {{ $badgeClass }}">
                                    {{ $sitemap['badge'] }}
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #10b981;">
                                    ● {{ $sitemap['records'] }}
                                </span>
                                <span style="font-size: 0.75rem; color: #a1a1aa; margin-left: 0.5rem;">
                                    (Caché: {{ $sitemap['ttl'] }})
                                </span>
                            </div>

                            <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                                {{ $sitemap['title'] }}
                            </h4>

                            <div class="sitemap-url-box">
                                {{ $sitemap['url'] }}
                            </div>

                            <p style="font-size: 0.85rem; color: #71717a; margin-top: 0.75rem; line-height: 1.5;">
                                {{ $sitemap['description'] }}
                            </p>
                        </div>

                        <div style="margin-top: 0.5rem;">
                            <x-filament::button href="{{ $sitemap['url'] }}" tag="a" target="_blank" color="primary" size="sm">
                                Abrir XML &nearr;
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Guía de Funcionamiento y Preguntas Frecuentes -->
        <div style="margin-top: 1.5rem;">
            <x-filament::section heading="Guía de Funcionamiento y Preguntas Frecuentes" description="Información técnica sobre el ciclo de actualización e indexación de Glodaxia">
                <div class="faq-grid">
                    <div class="faq-item">
                        <span class="sitemap-badge badge-master">1. Compilación Dinámica</span>
                        <h5 style="font-size: 0.9rem; font-weight: 800; margin: 0.5rem 0 0.25rem 0;">¿Cómo se genera el XML?</h5>
                        <p style="font-size: 0.8rem; color: #71717a; line-height: 1.4;">
                            No son archivos estáticos en disco. Cada vez que ingresas a una ruta <code style="font-size: 0.75rem;">/sitemap...xml</code>, Laravel consulta la base de datos y arma el XML en milisegundos.
                        </p>
                    </div>

                    <div class="faq-item">
                        <span class="sitemap-badge badge-news">2. Ciclo de Caché</span>
                        <h5 style="font-size: 0.9rem; font-weight: 800; margin: 0.5rem 0 0.25rem 0;">¿Cuándo se actualiza?</h5>
                        <p style="font-size: 0.8rem; color: #71717a; line-height: 1.4;">
                            Al publicar o editar una noticia, el sistema vacía automáticamente la memoria con <code style="font-size: 0.75rem;">flushCache()</code> para que el siguiente rastreo de Googlebot reciba la información más reciente.
                        </p>
                    </div>

                    <div class="faq-item">
                        <span class="sitemap-badge badge-lang">3. Protocolo IndexNow</span>
                        <h5 style="font-size: 0.9rem; font-weight: 800; margin: 0.5rem 0 0.25rem 0;">¿Cómo se envía a los buscadores?</h5>
                        <p style="font-size: 0.8rem; color: #71717a; line-height: 1.4;">
                            El sistema notifica activamente a Bing y Yandex mediante IndexNow. Para Google, solo debes registrar la URL <code style="font-size: 0.75rem;">/sitemap.xml</code> una sola vez en Search Console.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>