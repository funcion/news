<x-layouts.app>
    <x-slot:title>{{ __('ui.terms_of_service') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>Términos y condiciones de uso legal y normativo del portal {{ config('app.name', 'Glodaxia') }}.</x-slot>
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-purple-500/10 text-purple-700 dark:text-purple-400 text-xs font-black uppercase tracking-widest border border-purple-500/20">
                    {{ app()->getLocale() === 'es' ? 'Condiciones Generales & Blindaje Jurídico Universal' : 'Terms of Service & Universal Legal Shield' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Términos y Condiciones de Uso' : 'Terms of Service & User Agreement' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Contrato vinculante de acceso y utilización de la plataforma digital Glodaxia, exenciones integrales de responsabilidad, régimen de propiedad intelectual y cláusulas de indemnidad universal.' 
                    : 'Binding user agreement for accessing and using the Glodaxia digital platform, comprehensive disclaimers, intellectual property protections, and universal indemnification clauses.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/25 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-950 dark:text-amber-300 uppercase tracking-wider mb-2">Aviso Legal Importante: Aceptación Vinculante</h3>
                    <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        El acceso, navegación, lectura, suscripción o utilización de <strong>Glodaxia</strong> (en adelante, "la Plataforma", "el Sitio" o "nosotros") constituye la aceptación plena, incondicional e irrevocable de la totalidad de las cláusulas aquí estipuladas. Si el usuario no está de acuerdo con alguna disposición de estos Términos, debe abstenerse de utilizar el Sitio de forma inmediata.
                    </p>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Objeto y Naturaleza Informativa y Periodística</h2>
                <p>
                    <strong>Glodaxia</strong> es un medio de comunicación digital independiente dedicado a la divulgación, investigación periodística, análisis crítico de noticias, comparativas de tecnología, software, hardware, inteligencia artificial y ciberseguridad. 
                </p>
                <p>
                    Todos los artículos, reportajes, análisis de mercado, opiniones, tutoriales, guías y recursos publicados en Glodaxia se ofrecen con carácter <strong>estrictamente informativo, divulgativo, periodístico y educativo</strong>.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. EXENCIÓN TOTAL DE RESPONSABILIDAD PROFESIONAL Y TÉCNICA ("TAL CUAL" / "AS IS")</h2>
                <p>
                    EL CONTENIDO SE PROPORCIONA ESTRICTAMENTE "TAL CUAL" (*AS IS*) Y "SEGÚN DISPONIBILIDAD" (*AS AVAILABLE*), SIN GARANTÍAS DE NINGÚN TIPO, YA SEAN EXPRESAS, IMPLÍCITAS, LEGALES O DE OTRO TIPO:
                </p>
                <ul>
                    <li><strong>Ausencia de Asesoramiento Financiero, Fiscal o de Inversión:</strong> Los análisis sobre acciones tecnológicas, empresas cotizadas, rondas de financiación, criptoactivos, valoraciones de mercado o modelos de negocio tienen fines meramente informativos y periodísticos. <strong>Bajo ningún concepto constituyen asesoramiento financiero, recomendación de inversión, incitación a la compra/venta de activos ni consultoría financiera.</strong> Cualquier decisión económica es responsabilidad exclusiva y soberana del lector.</li>
                    <li><strong>Ausencia de Asesoramiento Técnico, de Infraestructura o de Ciberseguridad:</strong> Las referencias a código fuente, comandos de terminal, configuraciones de red, arquitecturas en la nube o herramientas de seguridad son ejemplos teóricos e ilustrativos. Glodaxia no asume responsabilidad alguna por caídas de servidores, brechas de seguridad, pérdida de datos o mal funcionamiento en entornos de producción.</li>
                    <li><strong>Ausencia de Asesoramiento Médico, Farmacéutico o Sanitario:</strong> Las notas sobre salud digital, dispositivos médicos o biotecnología son de divulgación científica y no reemplazan la consulta con un médico o profesional sanitario cualificado.</li>
                    <li><strong>Ausencia de Asesoramiento Jurídico:</strong> Los análisis de normativas (como el AI Act o leyes de privacidad) no constituyen asesoramiento legal formal.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">3. LÍMITE ABSOLUTO DE RESPONSABILIDAD UNIVERSAL (LIMITATION OF LIABILITY)</h2>
                <p>
                    EN LA MÁXIMA MEDIDA PERMITIDA POR LA LEGISLACIÓN APLICABLE EN CUALQUIER JURISDICCIÓN DEL MUNDO, <strong>GLODAXIA, SUS PROPIETARIOS, DIRECTORES, EMPLEADOS, REDACTORES FREELANCERS, COLABORADORES Y PROVEEDORES TECNOLÓGICOS NO SERÁN RESPONSABLES BAJO NINGUNA TEORÍA LEGAL (CONTRACTUAL, EXTRACONTRACTUAL, NEGLIGENCIA, RESPONSABILIDAD OBJETIVA O CUALQUIER OTRA) POR DAÑOS DIRECTOS, INDIRECTOS, INCIDENTALES, PUNITIVOS, ESPECIALES O CONSECUENCIALES</strong>, INCLUYENDO DE MANERA NO TAXATIVA: PÉRDIDA DE BENEFICIOS O LUCRO CESANTE, PÉRDIDA DE DATOS, PÉRDIDA DE INGRESOS, INTERRUPCIÓN DE ACTIVIDADES COMERCIALES, PÉRDIDA DE FONDO DE COMERCIO O COSTES DE ADQUISICIÓN DE SERVICIOS SUSTITUTIVOS, INCLUSO SI SE HUBIERA ADVERTIDO DE LA POSIBILIDAD DE DICHOS DAÑOS.
                </p>
                <p>
                    EN CASO DE QUE ALGUNA JURISDICCIÓN NO PERMITA LA EXCLUSIÓN TOTAL DE RESPONSABILIDAD, LA RESPONSABILIDAD MÁXIMA TOTAL ACUMULADA DE GLODAXIA ANTE CUALQUIER RECLAMACIÓN DERIVADA DEL SITIO QUEDARÁ LIMITADA DE MANERA ESTRICTA A <strong>CERO DÓLARES DE LOS ESTADOS UNIDOS ($0.00 USD)</strong> O LA CANTIDAD MÁXIMA DE DIEZ DÓLARES ($10.00 USD) SI LA LEY LOCAL IMPUSIERA UN LÍMITE OBLIGATORIO.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">4. CLÁUSULA DE INDEMNIZACIÓN Y DEFENSA POR PARTE DEL USUARIO</h2>
                <p>
                    El usuario acepta defender, indemnizar y mantener plenamente indemne a Glodaxia, sus administradores, autores, redactores freelancers y proveedores frente a cualquier reclamación, litigio, demanda, daño, pérdida, responsabilidad, sanción, coste o gasto (incluyendo honorarios razonables de abogados y costas judiciales) que surjan directa o indirectamente de:
                </p>
                <ul>
                    <li>El uso indebido, fraudulento o ilícito de la Plataforma por parte del usuario.</li>
                    <li>La violación de cualquiera de las cláusulas de los presentes Términos y Condiciones.</li>
                    <li>La infracción de cualquier derecho de terceros, incluyendo derechos de propiedad intelectual, privacidad o derecho al honor.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">5. Propiedad Intelectual, Derechos de Autor y Protección Anti-Scraping</h2>
                <ul>
                    <li><strong>Titularidad del Contenido:</strong> Todos los textos, análisis periodísticos, infografías, código fuente del sitio, logotipos, diseño visual e imágenes producidas son propiedad exclusiva de Glodaxia o se utilizan bajo licencia legítima.</li>
                    <li><strong>Derecho de Cita Periodística (Fair Use):</strong> Se autoriza la cita de fragmentos breves con fines informativos, de crítica o reseña, siempre que se atribuya de forma clara a Glodaxia e incluya un <strong>enlace directo, canónico y visible (dofollow)</strong> al artículo original.</li>
                    <li><strong>Prohibición Terminante de Scraping Masivo:</strong> Queda expresamente prohibida la extracción masiva automatizada de contenidos mediante bots, rastreadores (*crawlers*), scrapers o herramientas automatizadas sin autorización expresa por escrito.</li>
                    <li><strong>Prohibición de Entrenamiento de Modelos de IA:</strong> Se prohíbe de manera categórica el uso, recopilación o minería de los artículos y bases de datos de Glodaxia para el entrenamiento, ajuste fino (*fine-tuning*) o desarrollo de modelos de inteligencia artificial comercial sin un acuerdo contractual formal.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">6. Marcas Comerciales y Uso Nominativo Justo (*Nominative Fair Use*)</h2>
                <p>
                    Todas las marcas comerciales, nombres comerciales, logotipos, nombres de productos o empresas citados en nuestros artículos (tales como Apple, Google, Microsoft, OpenAI, NVIDIA, Meta, Amazon, SpaceX, entre otros) son propiedad exclusiva de sus respectivos titulares. Su mención en Glodaxia responde estrictamente a fines de identificación, crítica, análisis y reporte periodístico legítimo (*Nominative Fair Use*), sin que implique en ningún caso patrocinio, respaldo, asociación o afiliación oficial con dichos titulares.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">7. Procedimiento de Notificación y Retirada de Derechos de Autor (DMCA Safe Harbor)</h2>
                <p>
                    Glodaxia respeta la propiedad intelectual de terceros y actúa conforme a las disposiciones del <strong>Digital Millennium Copyright Act (17 U.S.C. § 512)</strong> y normativas internacionales de puerto seguro. Si usted es titular de derechos de autor y considera que algún contenido publicado en Glodaxia infringe sus derechos, remita una notificación formal a nuestro Agente Designado:
                </p>
                <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 not-prose text-sm mb-4">
                    <p class="mb-1 font-bold text-slate-900 dark:text-white">Agente de Derechos de Autor DMCA: <span class="font-normal">Glodaxia Legal Desk</span></p>
                    <p class="mb-0 font-bold text-slate-900 dark:text-white">Email Oficial: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>
                <p>
                    La notificación debe incluir: identificación precisa de la obra protegida, URL exacta del contenido supuestamente infractor, datos de contacto del titular y una declaración formal bajo pena de perjurio de que actúa como titular legítimo o representante autorizado. Tras recibir una notificación válida, procederemos a la revisión y retirada cautelar en un plazo máximo de 24 a 48 horas laborables.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">8. Divisibilidad, Renuncia y Ley Aplicable</h2>
                <ul>
                    <li><strong>Divisibilidad (*Severability*):</strong> Si alguna disposición de estos Términos fuera declarada nula, inaplicable o inválida por un tribunal competente, dicha cláusula se interpretará o escindirá en la mínima medida necesaria, manteniéndose todas las demás cláusulas en pleno vigor y efecto legal.</li>
                    <li><strong>Renuncia a Demandas Colectivas (*Class Action Waiver*):</strong> En la medida permitida por la ley aplicable, toda controversia se resolverá de forma individual, renunciando el usuario expresamente a iniciar o participar en demandas colectivas o acciones representativas contra Glodaxia.</li>
                </ul>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/25 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-950 dark:text-amber-300 uppercase tracking-wider mb-2">Binding Legal Notice</h3>
                    <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        By accessing, browsing, reading, or utilizing <strong>Glodaxia</strong>, you unconditionally agree to be bound by this Agreement. If you disagree with any provision, you must immediately terminate use of the platform.
                    </p>
                </div>

                <h2>1. Purpose: Tech News & Analytical Journalism</h2>
                <p>Glodaxia publishes technology journalism, breaking news analysis, hardware/software reviews, and AI research strictly for informational and educational purposes.</p>

                <h2>2. COMPREHENSIVE DISCLAIMER OF WARRANTIES ("AS IS")</h2>
                <p>
                    ALL CONTENT IS PROVIDED STRICTLY "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND.
                </p>
                <ul>
                    <li><strong>No Financial or Investment Advice:</strong> Articles discussing public companies, tech stocks, venture funding, or digital assets are journalistic analyses, not financial or investment advice. You bear sole responsibility for investment decisions.</li>
                    <li><strong>No Technical or Cybersecurity Warranty:</strong> Code snippets, terminal commands, and architectural guides are illustrative. Glodaxia is not liable for system outages, data loss, or security incidents.</li>
                </ul>

                <h2>3. UNIVERSAL LIMITATION OF LIABILITY</h2>
                <p>
                    UNDER NO LEGAL THEORY SHALL GLODAXIA, ITS FOUNDERS, FREELANCE AUTHORS, EDITORS, OR PROVIDERS BE LIABLE FOR DIRECT, INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL, OR PUNITIVE DAMAGES, INCLUDING LOST PROFITS OR DATA LOSS. MAXIMUM AGGREGATE LIABILITY IS STRICTLY CAPPED AT <strong>ZERO DOLLARS ($0.00 USD)</strong> OR $10.00 USD WHERE MANDATED BY LAW.
                </p>

                <h2>4. Indemnification</h2>
                <p>You agree to defend, indemnify, and hold harmless Glodaxia and its editorial team from any third-party claims arising from your misuse of the platform or breach of these Terms.</p>

                <h2>5. DMCA Notice & Takedown Protocol (17 U.S.C. § 512)</h2>
                <p>Send copyright notices with precise URLs and ownership verification to: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>