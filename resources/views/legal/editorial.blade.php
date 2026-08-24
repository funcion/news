<x-layouts.app :title="__('ui.editorial_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20">
                    {{ app()->getLocale() === 'es' ? 'Código Deontológico & Estándares' : 'Editorial Standards & Ethics' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política Editorial, Supervisión de IA y Fact-Checking' : 'Editorial Policy, AI Oversight & Fact-Checking' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Principios deontológicos, estándares de verificación factual, política de asistencia de IA con supervisión humana obligatoria y mecanismos de rectificación.' 
                    : 'Ethical standards, fact-checking methodology, AI assistance policies with mandatory human-in-the-loop oversight, and formal correction protocols.' }}
            </p>
        </div>

        <div class="prose prose-slate dark:prose-invert max-w-none">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Declaración de Supervisión Humana Garantizada (Human-in-the-Loop)</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                En <strong>Glodaxia</strong> combinamos el periodismo tecnológico de investigación con tecnologías de inteligencia artificial. La IA se utiliza exclusivamente como herramienta de soporte para la recopilación de datos, traducción inicial y estructuración previa. <strong>El 100% de los artículos, análisis técnicos, comparativas y titulares son redactados, contrastados, verificados y aprobados por editores y periodistas humanos antes de su publicación.</strong> Ningún contenido se publica de forma autónoma o desatendida.
                            </p>
                        </div>
                    </div>
                </div>

                <h2>1. Principios Fundamentales y Misión Periodística</h2>
                <p>La misión editorial de <strong>Glodaxia</strong> es proporcionar análisis tecnológicos rigurosos, precisos y contextualizados sobre software, hardware, inteligencia artificial, ciberseguridad, computación cuántica y mercados digitales. Nos regimos por los principios universales de veracidad, imparcialidad, independencia crítica y responsabilidad informativa.</p>

                <h2>2. Protocolo de Verificación Factual (Fact-Checking)</h2>
                <p>Todo material informativo publicado en Glodaxia sigue un protocolo de control de calidad estructurado en cuatro etapas:</p>
                <ol>
                    <li><strong>Auditoría de Fuentes Primarias:</strong> Las afirmaciones técnicas se contrastan directamente contra documentación oficial de fabricantes, repositorios de código abierto verificados, artículos científicos revisados por pares (*peer-reviewed*) o declaraciones formales de portavoces autorizados.</li>
                    <li><strong>Prevención y Supresión de Alucinaciones:</strong> Se auditan de forma manual todas las cifras, especificaciones de hardware, puntos de referencia (*benchmarks*), fechas de lanzamiento y fragmentos de código fuente antes de su indexación.</li>
                    <li><strong>Balance Crítico e Inclusión de Limitaciones Técnicas:</strong> Obligamos a nuestros redactores a detallar desventajas, costes ocultos, compromisos de ingeniería (*trade-offs*) y riesgos de seguridad en cada análisis de producto o infraestructura, evitando cualquier enfoque publicitario no fundamentado.</li>
                    <li><strong>Atribución Transparente y Enlaces Canónicos:</strong> Siempre que se citan estudios o noticias de terceros, se proporciona enlace directo a la fuente primaria de origen.</li>
                </ol>

                <h2>3. Uso Ético de la Inteligencia Artificial</h2>
                <p>Reconocemos a la Inteligencia Artificial como un instrumento de productividad tecnológica. En Glodaxia establecemos límites estrictos: prohibición de publicación autónoma desatendida, verificación obligatoria de originalidad y transparencia permanente ante los lectores.</p>

                <h2>4. Protección del Derecho al Honor, Opinión Crítica y Difamación</h2>
                <p>Las valoraciones sobre productos de software, dispositivos o servicios publicadas en Glodaxia constituyen <strong>juicios de valor, críticas técnicas y opiniones profesionales protegidas por el derecho constitucional a la libertad de expresión e información</strong>. Las críticas se basan en pruebas técnicas reproducibles y en información pública disponible.</p>

                <h2>5. Política Formal de Correcciones y Fe de Errores</h2>
                <p>Mantenemos un compromiso absoluto con la exactitud. Cuando se detecta un error sustancial, se corrige de manera inmediata y se anexa una <strong>Nota Editorial de Actualización</strong>. Para solicitar una rectificación editorial justificada, contáctenos en: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>

                <h2>6. Independencia Editorial y Conflictos de Interés</h2>
                <p>Ningún patrocinador, empresa tecnológica o anunciante tiene control ni capacidad de veto sobre nuestra línea editorial. Cualquier patrocinio o enlace de afiliación comercial se divulga expresamente al lector conforme a la normativa vigente.</p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-10">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Human-in-the-Loop Oversight Guarantee</h3>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-normal mb-0">
                                At <strong>Glodaxia</strong>, we combine investigative technology journalism with advanced artificial intelligence tools. AI is deployed strictly as an auxiliary research, translation, and structured synthesis aid. <strong>100% of our published articles, benchmarks, code walkthroughs, and headlines are verified, fact-checked, edited, and approved by human journalists before publication.</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <h2>1. Core Journalistic Principles</h2>
                <p>Glodaxia delivers high-signal, factual, and deeply technical journalism covering artificial intelligence, cloud architecture, cybersecurity, and digital markets. We adhere to rigorous standards of accuracy, impartiality, and editorial accountability.</p>

                <h2>2. Four-Stage Fact-Checking Protocol</h2>
                <ol>
                    <li><strong>Primary Source Verification:</strong> Technical claims must be verified against official documentation, peer-reviewed papers, or verified commits.</li>
                    <li><strong>Hallucination Elimination:</strong> Release dates, benchmarks, code snippets, and specifications are manually audited by human reviewers.</li>
                    <li><strong>Engineering Trade-Offs:</strong> Every review must document edge cases, limitations, security concerns, and architectural compromises.</li>
                    <li><strong>Explicit Attribution:</strong> We provide canonical links to original primary research and disclosures.</li>
                </ol>

                <h2>3. Fair Comment & Technical Critique</h2>
                <p>Product evaluations and architectural breakdowns published on Glodaxia represent protected professional opinions, technical critiques, and fair commentary based on reproducible empirical testing and publicly available documentation.</p>

                <h2>4. Formal Corrections Policy</h2>
                <p>If an error is identified, our editorial desk will promptly issue a transparent correction note. To submit a verifiable correction request, contact our editorial team at: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>