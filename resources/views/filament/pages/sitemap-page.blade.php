<x-filament-panels::page>
    <style>
        .sitemaps-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
            margin-top: 1rem;
        }

        @media (max-width: 1200px) {
            .sitemaps-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sitemaps-grid {
                grid-template-columns: 1fr;
            }
        }

        .sitemap-card {
            border-radius: 0.85rem;
            padding: 1.25rem;
            background: #ffffff;
            border: 1px solid rgba(150, 150, 150, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .sitemap-card:hover {
            border-color: #0284c7;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
        }
        .dark .sitemap-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark .sitemap-card:hover {
            border-color: #38bdf8;
            box-shadow: 0 4px 16px rgba(56, 189, 248, 0.1);
        }
        
        .sitemap-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.55rem;
            border-radius: 0.375rem;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
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
            font-size: 0.74rem;
            padding: 0.35rem 0.65rem;
            background: rgba(150, 150, 150, 0.08);
            border-radius: 0.4rem;
            display: block;
            margin-top: 0.4rem;
            color: #0284c7;
            word-break: break-all;
        }
        .dark .sitemap-url-box {
            background: rgba(255, 255, 255, 0.05);
            color: #38bdf8;
        }
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        @media (max-width: 1024px) {
            .faq-grid {
                grid-template-columns: 1fr;
            }
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

    <div style="max-width: 1400px; margin: 0 auto;">
        <!-- Banner Superior -->
        <x-filament::section>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.25rem;">
                        Indexación Dinámica & Protocolos de Sitemaps
                    </h3>
                    <p style="font-size: 0.85rem; color: #71717a; max-width: 800px; line-height: 1.4;">
                        Los sitemaps se compilan al vuelo directamente desde PostgreSQL con caché en memoria. Al publicarse una nueva noticia, la caché se actualiza automáticamente.
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

        <!-- Grilla de Sitemaps en 3 Columnas -->
        <div style="margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <h3 style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #a1a1aa;">
                    Estructura de Sitemaps Disponibles (7 Subsistemas)
                </h3>
            </div>

            <div class="sitemaps-grid">
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
                        <div>
                            <!-- Header de la tarjeta -->
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span class="sitemap-badge {{ $badgeClass }}">
                                    {{ $sitemap['badge'] }}
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #10b981;">
                                    ● {{ $sitemap['records'] }}
                                </span>
                            </div>

                            <!-- Título y URL -->
                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0 0 0.25rem 0; line-height: 1.3;">
                                {{ $sitemap['title'] }}
                            </h4>

                            <div class="sitemap-url-box">
                                {{ $sitemap['url'] }}
                            </div>

                            <!-- Descripción -->
                            <p style="font-size: 0.8rem; color: #71717a; margin-top: 0.75rem; line-height: 1.45;">
                                {{ $sitemap['description'] }}
                            </p>
                        </div>

                        <!-- Footer de la tarjeta con TTL y Botón -->
                        <div style="display: flex; justify-content: space-between; align-items: center; pt: 0.5rem; border-top: 1px solid rgba(150, 150, 150, 0.1); margin-top: 0.75rem; padding-top: 0.75rem;">
                            <span style="font-size: 0.7rem; color: #a1a1aa;">
                                Caché: <strong>{{ $sitemap['ttl'] }}</strong>
                            </span>
                            <x-filament::button href="{{ $sitemap['url'] }}" tag="a" target="_blank" color="primary" size="xs">
                                Abrir XML &nearr;
                            </x-filament::button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Guía de Funcionamiento en 3 Columnas -->
        <div style="margin-top: 1.75rem;">
            <x-filament::section heading="Guía de Funcionamiento y Protocolos de Indexación" description="Información técnica sobre el ciclo de actualización e indexación de Glodaxia">
                <div class="faq-grid">
                    <div class="faq-item">
                        <span class="sitemap-badge badge-master">1. Compilación Dinámica</span>
                        <h5 style="font-size: 0.9rem; font-weight: 800; margin: 0.5rem 0 0.25rem 0;">¿Cómo se genera el XML?</h5>
                        <p style="font-size: 0.8rem; color: #71717a; line-height: 1.4;">
                            No son archivos estáticos en disco. Cada vez que ingresas a una ruta <code style="font-size: 0.75rem;">/sitemap...xml</code>, Laravel consulta PostgreSQL y arma el XML en milisegundos.
                        </p>
                    </div>

                    <div class="faq-item">
                        <span class="sitemap-badge badge-news">2. Ciclo de Caché</span>
                        <h5 style="font-size: 0.9rem; font-weight: 800; margin: 0.5rem 0 0.25rem 0;">¿Cuándo se actualiza?</h5>
                        <p style="font-size: 0.8rem; color: #71717a; line-height: 1.4;">
                            Al publicar o editar una noticia, el sistema vacía automáticamente la memoria con <code style="font-size: 0.75rem;">flushCache()</code> para que el siguiente rastreo de Googlebot reciba los datos frescos.
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