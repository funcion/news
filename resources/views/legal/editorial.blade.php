<x-layouts.app :title="__('ui.editorial_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20">
                    {{ app()->getLocale() === 'es' ? 'Estándares Editoriales & Ética' : 'Editorial Standards & Ethics' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Actualizado: Agosto 2026' : 'Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política Editorial y Declaración de IA' : 'Editorial Policy & AI Transparency Statement' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Nuestra metodología de trabajo, principios de rigor periodístico y compromiso ético de supervisión humana en cada análisis publicado.' 
                    : 'Our editorial methodology, journalistic standards, and ethical commitment to human-in-the-loop oversight on every published report.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-li:text-slate-700 dark:prose-li:text-slate-300 prose-strong:text-slate-900 dark:prose-strong:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Compromiso Human-in-the-Loop (Supervisión Humana 100%)</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                                En <strong>Glodaxia</strong> combinamos el periodismo tecnológico de investigación con tecnologías de inteligencia artificial de vanguardia. La IA actúa exclusivamente como asistente de síntesis y procesamiento de datos. <strong>Cada artículo, titular, dato técnico y análisis es auditado, verificado y aprobado por un redactor humano antes de su publicación.</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <h2>1. Nuestra Misión Periodística</h2>
                <p>
                    Glodaxia nace con el objetivo de proporcionar información tecnológica veraz, rigurosa y libre de sensacionalismo. Cubrimos los avances más significativos en inteligencia artificial, desarrollo de software, ciberseguridad, infraestructura en la nube y computación cuántica, explicando su impacto técnico y económico real.
                </p>

                <h2>2. Cómo Utilizamos la Inteligencia Artificial</h2>
                <p>
                    Utilizamos modelos avanzados de lenguaje (LLMs) como asistentes de apoyo para tareas operativas: monitorización de fuentes oficiales, procesamiento multilingüe y estructuración inicial de datos.
                </p>

                <h2>3. Protocolo Estricto de Verificación (Fact-Checking)</h2>
                <p>
                    Ningún contenido se publica de manera autónoma sin filtro. Nuestro equipo editorial verifica las fuentes primarias, audita posibles alucinaciones, contrasta los compromisos técnicos y asegura enlaces canónicos transparentes.
                </p>

                <h2>4. Correcciones y Rectificaciones</h2>
                <p>
                    La honestidad informativa es nuestro pilar fundamental. Si se detecta una imprecisión factual, corregiremos el artículo de forma inmediata y transparente mediante una nota editorial.
                </p>

                <h2>5. Independencia Editorial</h2>
                <p>
                    Nuestros redactores y editores mantienen independencia total de criterio. Los análisis técnicos y críticas de productos no están sujetos a acuerdos comerciales no declarados.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Our Human-in-the-Loop Guarantee</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal">
                                At <strong>Glodaxia</strong>, we pair investigative technology journalism with advanced artificial intelligence. AI serves solely as a research and synthesis aid. <strong>Every single article, technical benchmark, headline, and analysis is audited, fact-checked, and approved by human journalists before publication.</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <h2>1. Our Editorial Mission</h2>
                <p>
                    Glodaxia is dedicated to delivering high-signal, accurate, and insightful tech journalism. We track breakthroughs in artificial intelligence, software engineering, cybersecurity, cloud infrastructure, and emerging tech.
                </p>

                <h2>2. How We Leverage Artificial Intelligence</h2>
                <p>
                    We deploy state-of-the-art Large Language Models strictly as research assistants for source ingestion, multilingual translation, and drafting assistance.
                </p>

                <h2>3. Rigorous Fact-Checking Protocol</h2>
                <p>
                    No content is ever published unsupervised. Our editorial staff cross-references primary documentation, audits technical claims, and eliminates unverified statements.
                </p>

                <h2>4. Corrections & Accountability</h2>
                <p>
                    We take factual accuracy seriously. If an error is identified, we correct the article promptly and issue a transparent editorial note detailing the update.
                </p>

                <h2>5. Editorial Independence</h2>
                <p>
                    Our editorial opinions remain strictly independent and governed exclusively by technical merit and journalistic integrity.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>