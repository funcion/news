<x-layouts.app>
    <x-slot:robots>noindex, nofollow</x-slot>
    <x-slot:title>{{ __('ui.editorial_policy') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>{{ app()->getLocale() === 'es' ? 'Código ético, criterios de opinión periodística, verificación de fuentes y transparencia editorial de ' . config('app.name', 'Glodaxia') . '.' : 'Editorial code of ethics, opinion journalism standards, source verification, and transparency guidelines at ' . config('app.name', 'Glodaxia') . '.' }}</x-slot>
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ __('ui.editorial_policy') }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-black uppercase tracking-widest border border-emerald-500/20">
                    {{ app()->getLocale() === 'es' ? 'Ética Editorial & Periodismo de Opinión Independiente' : 'Editorial Ethics & Independent Opinion Journalism' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política Editorial y Criterio de Opinión' : 'Editorial Policy & Opinion Standards' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Nuestros principios de investigación periodística, enfoque analítico y de opinión, verificación de fuentes, independencia editorial y transparencia en herramientas tecnológicas avanzadas.' 
                    : 'Our core journalistic principles, opinion and critical analysis framework, primary source verification, editorial independence, and transparent technological tool integration.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Declaración de Independencia y Criterio Humano</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> es una publicación digital de análisis, investigación y opinión tecnológica. Nuestro objetivo fundamental es <strong>analizar las noticias globales, compartir nuestras perspectivas y opiniones informadas, y hacer que la complejidad del mundo digital sea comprensible y accesible para todos</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Misión Editorial y Libertad de Opinión</h2>
                <p>
                    Creemos firmemente en el poder del análisis crítico. En lugar de limitarnos a reproducir notas de prensa comerciales o resúmenes automatizados, nuestros artículos aportan contexto, interpretan el impacto real de las tecnologías emergentes y expresan el <strong>punto de vista honesto de nuestros autores</strong>.
                </p>
                <p>
                    Las opiniones expresadas en nuestros artículos representan la visión analítica de su autor basada en hechos públicos y contrastados en el momento de la publicación.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Verificación de Hechos y Fuentes Primarias</h2>
                <p>
                    Nuestros análisis se sustentan en fuentes primarias verificadas: publicaciones académicas, repositorios oficiales de código, comunicados de prensa institucionales y documentación técnica directa de los creadores. Cada noticia incluye la atribución y el enlace transparente a la fuente original.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">3. Uso Transparente de Tecnología y Asistencia de IA</h2>
                <p>
                    En Glodaxia empleamos herramientas de inteligencia artificial de última generación de forma auxiliar para la curación de información, traducción bilingüe y síntesis documental. 
                </p>
                <ul>
                    <li><strong>Criterio y Firma de Autores Reales:</strong> Todos los contenidos son curados y estructurados bajo la dirección de redactores y editores especializados.</li>
                    <li><strong>Filtrado Anti-Sesgos y Veracidad:</strong> Las citas, cifras y datos técnicos son contrastados para garantizar la máxima exactitud.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">4. Independencia Frente a Marcas y Ausencia de Conflicto de Interés</h2>
                <p>
                    Nuestras opiniones, reseñas y análisis son 100% independientes. Ninguna compañía tecnológica, anunciante o fabricante influye en nuestras conclusiones editoriales ni tiene poder de veto sobre nuestras críticas.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Política de Correcciones Abiertas y Rectificación</h2>
                <p>
                    Si detectas cualquier error factual en alguno de nuestros artículos o deseas aportar información adicional, puedes escribirnos a <a href="mailto:editorial@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">editorial@glodaxia.com</a>. Las correcciones verificadas se aplican con total transparencia.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Statement of Independence and Human Judgment</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> is an independent digital publication dedicated to tech analysis, research, and opinion journalism. Our core mission is to <strong>analyze global news, share informed opinions and viewpoints, and make complex digital innovation simple, human, and accessible to everyone</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Editorial Mission & Freedom of Opinion</h2>
                <p>
                    We believe in the essential value of critical analysis. Rather than republishing corporate press releases, our articles provide real-world context, examine technical trade-offs, and deliver the <strong>independent perspectives and opinions of our authors</strong>.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Primary Source Verification & Fact-Checking</h2>
                <p>
                    Our reporting is grounded in primary verifiable data: research papers, official source repositories, institutional filings, and engineering documentation. Every article cites and links to the original source.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">3. Transparent AI Assistance & Editorial Standards</h2>
                <p>
                    We utilize advanced artificial intelligence tools strictly as research aids for document synthesis, linguistic translation, and data organization. All editorial pieces reflect the oversight and curation of our specialized team.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">4. Editorial Independence & No Conflicts of Interest</h2>
                <p>
                    Our critiques and reviews remain completely independent. No tech company, sponsor, or vendor holds editorial sway over our published evaluations or viewpoints.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Open Correction Policy</h2>
                <p>
                    We are committed to prompt corrections when factual inaccuracies occur. Reach out to <a href="mailto:editorial@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">editorial@glodaxia.com</a> and we will review and update the record transparently.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>