<x-layouts.app :title="__('ui.editorial_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-black uppercase tracking-widest border border-emerald-500/20">
                    {{ app()->getLocale() === 'es' ? 'Ética Periodística & Supervisión Humana 100%' : 'Journalism Ethics & 100% Human Oversight' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política Editorial, Supervisión de Inteligencia Artificial y Transparencia' : 'Editorial Policy, AI Oversight & Journalism Transparency' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Principios deontológicos, protocolo de verificación factual, supervisión editorial humana garantizada y marco de transparencia en el uso de herramientas tecnológicas avanzadas.' 
                    : 'Ethical journalism guidelines, fact-checking protocols, mandatory human editorial verification, and transparent disclosure on advanced AI research tools.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Declaración Editorial y Supervisión Humana Garantizada</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> es una publicación digital de análisis crítico, investigación y divulgación tecnológica. En un ecosistema saturado de desinformación y contenido automatizado de baja calidad, nuestro compromiso irrenunciable es la <strong>calidad informativa, el rigor técnico y la supervisión humana garantizada en cada artículo publicado</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Filosofía y Misión Periodística</h2>
                <p>
                    Nuestra misión es explicar con claridad y rigor cómo la tecnología, el software, la inteligencia artificial, la ciberseguridad y la ingeniería digital transforman la sociedad, los negocios y la vida cotidiana. Nos esforzamos por ofrecer análisis que vayan más allá de los comunicados corporativos, desgranando compromisos técnicos (*trade-offs*), costes ocultos y consecuencias reales para los usuarios.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Uso Ético de la Inteligencia Artificial como Herramienta de Apoyo</h2>
                <p>
                    En Glodaxia utilizamos tecnologías de inteligencia artificial exclusivamente como <strong>herramientas auxiliares de investigación, soporte documental, síntesis de fuentes extensas y traducción bilingüe</strong>. 
                </p>
                <ul>
                    <li><strong>Prohibición de Publicación Desatendida o 100% Autónoma:</strong> Ningún artículo se publica de forma automática o sin la revisión, edición, contextualización y firma de un redactor o editor humano.</li>
                    <li><strong>Responsabilidad Editorial Humana:</strong> Los redactores y editores de Glodaxia asumen la plena responsabilidad editorial por la veracidad, estilo y exactitud de cada texto publicado.</li>
                    <li><strong>Eliminación Activa de Alucinaciones:</strong> Todas las citas, cifras, fechas y fragmentos de código son contrastados manualmente contra fuentes primarias antes de ver la luz.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">3. Protocolo de Verificación Factual (*Fact-Checking*)</h2>
                <ol>
                    <li><strong>Consulta de Fuentes Primarias:</strong> Priorizamos la verificación en repositorios oficiales de código (GitHub/GitLab), documentación de APIs, comunicados oficiales de fabricantes, patentes y publicaciones científicas revisadas por pares (*peer-reviewed*).</li>
                    <li><strong>Separación Clara entre Hechos y Opinión:</strong> Distinguimos nítidamente entre la información fáctica comprobada y el análisis o juicio crítico formulado por nuestros redactores.</li>
                    <li><strong>Atribución Transparente y Enlaces Canónicos:</strong> Reconocemos siempre el trabajo de periodistas, investigadores o medios que hayan obtenido una primicia o exclusiva, enlazando directamente a la fuente original.</li>
                </ol>

                <h2 class="text-slate-900 dark:text-white font-black">4. Independencia Editorial y Conflictos de Interés</h2>
                <p>
                    La línea editorial de Glodaxia es completamente independiente. No aceptamos pagos ni presiones de empresas tecnológicas a cambio de coberturas favorables o calificaciones positivas de productos.
                </p>
                <p>
                    En caso de existir algún acuerdo de patrocinio o contenido patrocinado en el futuro, este será identificado de forma visible y categórica con las etiquetas <strong>"Patrocinado"</strong> o <strong>"Colaboración Comercial"</strong>, manteniéndose siempre una estricta separación entre el equipo comercial y el equipo de redacción.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Protección de la Libertad de Expresión y Crítica Técnica</h2>
                <p>
                    Las valoraciones, análisis de software/hardware y opiniones expresadas por nuestros redactores constituyen juicios de valor y críticas técnicas profesionales amparadas por la libertad de prensa, el derecho a la información veraz y la libertad de expresión protegida por convenios internacionales. Se fundamentan en pruebas reproducibles, datos públicos y metodologías de evaluación objetivas.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">6. Política de Correcciones, Fe de Errores y Derecho de Rectificación</h2>
                <p>
                    La transparencia es nuestro pilar fundamental. Si cometemos un error factual o una imprecisión técnica en cualquier publicación, lo corregiremos con total prontitud y dejaremos constancia mediante una <strong>Nota Editorial de Corrección</strong> al final del artículo correspondiente.
                </p>
                <p>
                    Cualquier lector, empresa o entidad que considere que un artículo contiene un dato inexacto puede solicitar una rectificación o enviar información complementaria a nuestro escritorio de redacción:
                </p>
                <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 not-prose text-sm mb-4">
                    <p class="mb-1 font-bold text-slate-900 dark:text-white">Mesa de Redacción & Rectificaciones: <span class="font-normal">Glodaxia Editorial Desk</span></p>
                    <p class="mb-0 font-bold text-slate-900 dark:text-white">Email de Contacto: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Editorial Charter & Guaranteed Human Oversight</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                <strong>Glodaxia</strong> is an independent digital media publication focused on technology analysis, software architecture, and AI research. We uphold the highest standards of journalistic integrity and <strong>guarantee mandatory human editorial oversight on every single article published</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <h2>1. Ethical Use of AI Research Tools</h2>
                <p>We leverage advanced AI strictly as an auxiliary tool for source monitoring, synthesis of complex technical whitepapers, and bilingual drafting. <strong>No content is ever published autonomously without rigorous human review, fact-checking, and final approval by a human writer.</strong></p>

                <h2>2. Fact-Checking & Primary Sources</h2>
                <p>All technical benchmarks, architectural breakdowns, and product evaluations are cross-checked against primary sources, official documentation, and verified code repositories.</p>

                <h2>3. Editorial Corrections Desk</h2>
                <p>If you identify a factual error, contact our editorial verification team at: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>. Corrections are published transparently via an Editorial Note.</p>
            @endif
        </div>
    </div>
</x-layouts.app>