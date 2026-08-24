<x-layouts.app :title="__('ui.terms_of_service') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-purple-500/10 text-purple-700 dark:text-purple-400 text-xs font-black uppercase tracking-widest border border-purple-500/20">
                    {{ app()->getLocale() === 'es' ? 'Condiciones de Uso' : 'Terms of Use' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente desde: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Términos y Condiciones de Uso' : 'Terms of Service' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Reglas legales, derechos de propiedad intelectual y condiciones generales que rigen el acceso y uso de Glodaxia.' 
                    : 'Legal rules, intellectual property rights, and terms governing your access to and use of Glodaxia.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-li:text-slate-700 dark:prose-li:text-slate-300 prose-strong:text-slate-900 dark:prose-strong:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400">
            @if(app()->getLocale() === 'es')
                <h2>1. Aceptación y Ámbito de Aplicación</h2>
                <p>
                    Al acceder, navegar o utilizar este sitio web (<strong>Glodaxia</strong>), aceptas quedar vinculado por estos Términos y Condiciones, así como por nuestra Política de Privacidad y Política de Cookies. Si no estás de acuerdo con alguno de estos términos, te rogamos abstenerte de utilizar la plataforma.
                </p>

                <h2>2. Propiedad Intelectual y Licencia de Contenidos</h2>
                <p>
                    Todos los artículos, logotipos, elementos de diseño, estructuras de datos, código fuente y compilaciones editoriales son propiedad exclusiva de Glodaxia o de sus respectivos titulares de derechos.
                </p>
                <ul>
                    <li><strong>Uso Personal:</strong> Se autoriza la lectura, consulta y compartición de extractos breves en redes sociales y medios informativos, siempre que se incluya atribución clara y un enlace canónico directo al artículo original.</li>
                    <li><strong>Prohibición de Scrapping Masivo:</strong> Queda estrictamente prohibida la extracción masiva automatizada, el raspado (*scraping*) de contenido o la republicación íntegra de artículos sin consentimiento expreso por escrito.</li>
                </ul>

                <h2>3. Asistencia de Inteligencia Artificial y Supervisión Humana</h2>
                <p>
                    En Glodaxia combinamos el periodismo tecnológico de investigación con la asistencia avanzada de inteligencia artificial. La IA se utiliza exclusivamente como herramienta de soporte para la recopilación y síntesis de datos. <strong>Todo el contenido es rigurosamente verificado, contrastado, revisado y curado por redactores y editores humanos antes de su publicación.</strong> Supervisión editorial humana 100% garantizada.
                </p>

                <h2>4. Exención de Responsabilidad Técnica y Financiera</h2>
                <p>
                    Los contenidos publicados en Glodaxia tienen fines exclusivamente informativos, formativos y de divulgación técnica:
                </p>
                <ul>
                    <li><strong>Sin Asesoramiento Financiero:</strong> Ningún análisis de mercado, criptomonedas, valores tecnológicos o modelos de negocio constituye recomendación de inversión.</li>
                    <li><strong>Sin Garantía Técnica:</strong> Las configuraciones de software, fragmentos de código o tutoriales se proporcionan "tal cual". El usuario asume toda responsabilidad en la ejecución de comandos o configuraciones en entornos de producción.</li>
                </ul>

                <h2>5. Enlaces Externos de Terceros</h2>
                <p>
                    Nuestros análisis incluyen enlaces a repositorios oficiales (GitHub), comunicados de empresas tecnológicas y artículos de investigación. Glodaxia no controla ni asume responsabilidad por el contenido, políticas de privacidad o disponibilidad de dichos sitios web externos.
                </p>

                <h2>6. Modificación de los Términos</h2>
                <p>
                    Nos reservamos el derecho de actualizar estos Términos y Condiciones en cualquier momento. La fecha de última modificación permanecerá siempre visible en la cabecera de este documento.
                </p>
            @else
                <h2>1. Acceptance of Terms</h2>
                <p>
                    By accessing or using <strong>Glodaxia</strong>, you agree to be bound by these Terms of Service, along with our Privacy Policy and Cookie Policy. If you do not agree to these terms, please discontinue use of this website.
                </p>

                <h2>2. Intellectual Property & Fair Use</h2>
                <p>
                    All original articles, brand assets, site architecture, and editorial compilations are protected by international copyright laws.
                </p>
                <ul>
                    <li><strong>Permitted Sharing:</strong> Quotations and short excerpts may be shared for non-commercial commentary provided that clear attribution and a direct canonical link to the original article are included.</li>
                    <li><strong>Prohibited Scraping:</strong> Automated scraping, content mirroring, and bulk unauthorized reproduction of our database are strictly prohibited.</li>
                </ul>

                <h2>3. AI Assistance & Human Editorial Oversight</h2>
                <p>
                    We pair investigative tech journalism with advanced AI assistance. AI is used strictly as a research and synthesis aid. <strong>All content is strictly fact-checked, reviewed, and curated by human editors prior to publication.</strong> Human editorial oversight guaranteed.
                </p>

                <h2>4. Technical & Financial Disclaimer</h2>
                <p>
                    All editorial materials are published strictly for educational and informational purposes:
                </p>
                <ul>
                    <li><strong>No Financial Advice:</strong> Market commentary, crypto analyses, and venture disclosures do not constitute investment advice.</li>
                    <li><strong>No Engineering Warranty:</strong> Code snippets, server configurations, and architecture tutorials are provided "as is" without warranty.</li>
                </ul>

                <h2>5. Third-Party Links</h2>
                <p>
                    Our articles reference verified documentation, open-source repositories, and primary sources. We assume no responsibility for external third-party content or privacy practices.
                </p>

                <h2>6. Amendments</h2>
                <p>
                    We reserve the right to revise these Terms of Service at any time. Changes become effective upon posting.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>