<x-layouts.app :title="__('ui.editorial_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20">
                    {{ app()->getLocale() === 'es' ? 'Código Deontológico & Análisis de Noticias' : 'Editorial Standards & News Analysis' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política Editorial, Supervisión de IA y Análisis de Noticias' : 'Editorial Policy, AI Oversight & News Analysis' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Principios deontológicos de nuestra plataforma de análisis de noticias tecnológicas, rigor de fact-checking y supervisión humana obligatoria.' 
                    : 'Ethical journalism standards for our tech news analysis platform, fact-checking rigor, and mandatory human oversight.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Plataforma de Análisis de Noticias con Supervisión Humana Garantizada</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> es una plataforma de análisis crítico de noticias, investigación tecnológica y divulgación técnica. Combinamos el seguimiento constante de la actualidad tecnológica con modelos de inteligencia artificial como herramientas de asistencia documental y traducción. <strong>El 100% de los análisis, artículos de noticias, notas técnicas y titulares son rigurosamente verificados, contrastados, contextualizados y aprobados por periodistas y redactores humanos antes de publicarse.</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Naturaleza de la Plataforma y Análisis de Noticias</h2>
                <p>
                    Glodaxia opera como un medio especializado en el <strong>análisis e interpretación crítica de noticias tecnológicas</strong>. Nuestro equipo desglosa comunicados de prensa, actualizaciones de software, filtraciones verificadas, avances en inteligencia artificial, ciberseguridad e infraestructura en la nube, aportando contexto de ingeniería, análisis de impacto económico y verificación independiente.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Protocolo de Verificación Factual (Fact-Checking)</h2>
                <ol>
                    <li><strong>Auditoría de Fuentes Primarias:</strong> Toda noticia o análisis técnico se contrasta directamente con repositorios oficiales de código (GitHub), documentación de APIs, comunicados oficiales de fabricantes o publicaciones académicas revisadas por pares.</li>
                    <li><strong>Supresión de Alucinaciones y Datos Falsos:</strong> Verificamos manualmente todas las métricas, benchmarks, fechas y fragmentos de código antes de la publicación.</li>
                    <li><strong>Análisis Crítico y Compromisos Técnicos (*Trade-offs*):</strong> Desglosamos limitaciones, vulnerabilidades potenciales y costes ocultos, garantizando coberturas objetivas y no promocionales.</li>
                    <li><strong>Atribución y Enlaces Canónicos:</strong> Enlazamos de forma transparente a las fuentes originales de la noticia.</li>
                </ol>

                <h2 class="text-slate-900 dark:text-white font-black">3. Uso Ético y Asistido de la Inteligencia Artificial</h2>
                <p>
                    La IA se utiliza estrictamente para tareas auxiliares: monitoreo de fuentes internacionales, síntesis de documentación extensa y apoyo en la redacción inicial de resúmenes. Queda terminantemente prohibida la publicación autónoma o desatendida de noticias generadas por IA.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">4. Protección del Derecho al Honor y Opinión Crítica</h2>
                <p>
                    Los análisis de productos y coberturas de actualidad tecnológica publicados en Glodaxia constituyen <strong>juicios de valor, críticas técnicas y opiniones profesionales amparadas por la libertad de información y expresión</strong>. Se fundamentan en datos verificables y pruebas reproducibles.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Política de Correcciones y Rectificaciones</h2>
                <p>
                    Si detecta una imprecisión factual en cualquier análisis de noticias, corregiremos el texto con total transparencia mediante una Nota Editorial. Puede solicitar una revisión en: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">6. Independencia Editorial</h2>
                <p>
                    Nuestros análisis de noticias no están condicionados por intereses corporativos ni patrocinadores. Cualquier colaboración comercial autorizada se etiqueta de forma expresa y visible.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">News Analysis Platform with Guaranteed Human Oversight</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> is an investigative tech journalism and news analysis platform. We pair breaking tech news coverage with advanced AI research tools. <strong>100% of our news analyses, benchmarks, and dispatches are fact-checked, contextualized, and approved by human editors prior to release.</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Platform Mission & News Analysis</h2>
                <p>We analyze breaking technology news, artificial intelligence developments, cybersecurity disclosures, and cloud architecture, providing critical engineering context and independent verification.</p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Verification Standards</h2>
                <p>All claims are cross-referenced with primary documentation, verified repositories, and official vendor disclosures.</p>

                <h2 class="text-slate-900 dark:text-white font-black">3. Corrections Desk</h2>
                <p>Contact our editorial desk for verification requests at: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>