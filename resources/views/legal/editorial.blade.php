<x-layouts.app :title="__('ui.editorial_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-12">
        <div class="mb-8">
            <span class="px-3 py-1 rounded-full bg-cyan-500/10 text-xs font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest inline-block mb-3">
                {{ app()->getLocale() === 'es' ? 'Transparencia & Ética' : 'Transparency & Ethics' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Política Editorial y Declaración de IA' : 'Editorial Policy & AI Transparency Statement' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last updated: August 2026' }}
            </p>
        </div>

        <div class="prose prose-base md:prose-lg max-w-none text-gray-700 dark:text-gray-300 prose-headings:font-black prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400 leading-relaxed">
            @if(app()->getLocale() === 'es')
                <div class="p-6 rounded-2xl bg-cyan-500/5 border border-cyan-500/20 mb-8 not-prose">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nuestro Compromiso de Supervisión Humana</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        En <strong>Glodaxia</strong> combinamos el periodismo tecnológico de investigación con tecnologías de inteligencia artificial de última generación. La IA actúa como un asistente editorial avanzado para el procesamiento de datos y redacción inicial, pero <strong>cada artículo es supervisado, verificado, estructurado y curado por nuestro equipo editorial humano</strong> antes de su publicación definitiva.
                    </p>
                </div>

                <h2>1. Cómo Utilizamos la Inteligencia Artificial</h2>
                <p>
                    Utilizamos modelos avanzados de lenguaje (LLMs) como asistentes de investigación y síntesis para analizar fuentes primarias, comunicados técnicos, repositorios de código abierto y documentos de investigación. La IA nos ayuda a procesar grandes volúmenes de información técnica y elaborar borradores bilingües.
                </p>

                <h2>2. Verificación Humana y Control de Calidad (Fact-Checking)</h2>
                <p>
                    Ningún contenido es publicado de forma 100% automatizada o sin supervisión. Nuestros redactores y editores:
                </p>
                <ul>
                    <li>Verifican los datos técnicos, citas, fechas y fuentes originales.</li>
                    <li>Eliminan alucinaciones, sesgos o afirmaciones no comprobables.</li>
                    <li>Añaden contexto crítico de la industria, análisis de consecuencias y opiniones editoriales fundamentadas.</li>
                    <li>Garantizan el cumplimiento estricto de las directrices de calidad y valor E-E-A-T de Google.</li>
                </ul>

                <h2>3. Transparencia y Atribución de Fuentes</h2>
                <p>
                    Creemos firmemente en el respeto a la propiedad intelectual. Todos nuestros artículos enlazan de forma visible y directa a las fuentes originales, artículos de investigación o comunicados oficiales donde se originó la noticia.
                </p>

                <h2>4. Correcciones y Rectificaciones</h2>
                <p>
                    Si detectas algún error factual en nuestros análisis, nuestro equipo editorial lo corregirá con total transparencia. Puedes contactarnos en cualquier momento a través de nuestros canales oficiales.
                </p>
            @else
                <div class="p-6 rounded-2xl bg-cyan-500/5 border border-cyan-500/20 mb-8 not-prose">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Our Human-in-the-Loop Commitment</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        At <strong>Glodaxia</strong>, we pair investigative tech journalism with cutting-edge artificial intelligence. AI serves as an editorial research assistant for data ingestion and drafting, but <strong>every single article is supervised, fact-checked, structured, and curated by human journalists</strong> prior to final publication.
                    </p>
                </div>

                <h2>1. How We Use Artificial Intelligence</h2>
                <p>
                    We leverage advanced Large Language Models as research assistants to parse technical papers, release notes, and industry dispatches. AI assists in synthesizing complex technical topics and drafting bilingual articles.
                </p>

                <h2>2. Human Verification and Quality Control</h2>
                <p>
                    No content is published as an unsupervised black box. Our editorial staff:
                </p>
                <ul>
                    <li>Fact-checks technical claims, citations, dates, and primary sources.</li>
                    <li>Eliminates hallucinations, biases, or unverified assertions.</li>
                    <li>Injects real-world engineering trade-offs and analytical nuance.</li>
                    <li>Ensures compliance with Google's E-E-A-T high-quality content standards.</li>
                </ul>

                <h2>3. Source Attribution & Ethics</h2>
                <p>
                    We uphold full journalistic integrity. Every report cites and links directly to verified primary sources, official documentation, or original reporting.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>