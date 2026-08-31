<x-layouts.app>
    <x-slot:robots>noindex, nofollow</x-slot>
    <x-slot:title>{{ __('ui.terms_of_service') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>{{ app()->getLocale() === 'es' ? 'Términos y condiciones legales, exención de responsabilidad, derecho de opinión y blindaje de uso de ' . config('app.name', 'Glodaxia') . '.' : 'Terms of service, legal disclaimers, opinion rights, and limitation of liability for ' . config('app.name', 'Glodaxia') . '.' }}</x-slot>
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ __('ui.terms_of_service') }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-purple-500/10 text-purple-700 dark:text-purple-400 text-xs font-black uppercase tracking-widest border border-purple-500/20">
                    {{ app()->getLocale() === 'es' ? 'Marco Jurídico & Protección Editorial Universal' : 'Legal Framework & Global Editorial Protection' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Términos y Condiciones de Uso' : 'Terms of Service & Usage Agreement' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Regulación contractual del acceso y uso de Glodaxia, régimen de libre expresión, periodismo de opinión, exención integral de garantías y cláusulas de indemnidad internacional.' 
                    : 'Binding terms governing access and use of Glodaxia, freedom of expression protections, opinion journalism disclaimers, complete warranty exemptions, and global indemnification provisions.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/25 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-950 dark:text-amber-300 uppercase tracking-wider mb-2">Aviso Legal: Aceptación Vinculante de Condiciones</h3>
                    <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        El acceso, navegación, lectura, suscripción, interacción o uso de <strong>Glodaxia</strong> (en adelante, "el Portal", "la Plataforma" o "nosotros") implica la aceptación plena, libre y sin reservas de todos los términos aquí expuestos. Si no estás conforme con alguna de estas condiciones, te solicitamos amablemente que ceses la navegación en nuestro sitio de forma inmediata.
                    </p>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Naturaleza del Portal: Periodismo de Opinión, Análisis y Divulgación</h2>
                <p>
                    <strong>Glodaxia</strong> opera principalmente como un <strong>medio digital de periodismo de opinión, análisis crítico, divulgación tecnológica y comentarios informativos</strong>. Nuestras publicaciones, artículos y análisis reflejan el punto de vista, la interpretación editorial, las perspectivas analíticas y las valoraciones de nuestros autores, redactores y colaboradores sobre acontecimientos del ecosistema tecnológico global.
                </p>
                <p>
                    Todo el contenido se elabora con fines exclusivamente <strong>divulgativos, de debate público, formativos y de entretenimiento</strong>, amparado por los principios universales de la libertad de información, libertad de expresión y libertad de prensa.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Protección Universal de la Libertad de Expresión y Opinión</h2>
                <p>
                    Las opiniones, críticas, comparativas y análisis publicados en Glodaxia están formalmente protegidos por:
                </p>
                <ul>
                    <li>El <strong>Artículo 19 de la Declaración Universal de los Derechos Humanos</strong> (derecho inalienable a la libertad de opinión y de expresión).</li>
                    <li>La <strong>Primera Enmienda de la Constitución de los Estados Unidos</strong> (*First Amendment*) y la doctrina de libre expresión de opiniones e ideas.</li>
                    <li>El <strong>Artículo 10 del Convenio Europeo de Derechos Humanos</strong> y normativas homólogas internacionales de libertad de prensa.</li>
                    <li>La <strong>Doctrina del Uso Justo (*Fair Use*, 17 U.S.C. § 107)</strong> y el <strong>Derecho de Cita Informativa</strong>, para el comentario crítico, la reseña periodística y el análisis de productos, noticias públicas o marcas de terceros.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">3. Exención Total de Asesoramiento Profesional ("AS IS" / "TAL CUAL")</h2>
                <p>
                    TODO EL CONTENIDO SE OFRECE ESTRICTAMENTE <strong>"TAL CUAL" (*AS IS*)</strong> Y SEGÚN DISPONIBILIDAD, SIN GARANTÍAS DE NINGUNA CLASE, EXPRESAS O IMPLÍCITAS:
                </p>
                <ul>
                    <li><strong>Ausencia de Asesoramiento Financiero o de Inversión:</strong> Las valoraciones sobre empresas, acciones tecnológicas, criptomonedas o finanzas representan opiniones editoriales. <strong>Bajo ningún concepto constituyen asesoramiento financiero, recomendaciones de compra/venta ni incitación a inversiones.</strong> Cada lector asume la responsabilidad total y soberana sobre sus decisiones patrimoniales.</li>
                    <li><strong>Ausencia de Asesoramiento Técnico o de Seguridad:</strong> Cualquier fragmento de código, comando, tutorial o recomendación técnica tiene propósito ilustrativo. Glodaxia no responde por fallos de hardware, pérdidas de datos, vulnerabilidades o paradas de servicio en infraestructuras de terceros.</li>
                    <li><strong>Ausencia de Asesoramiento Jurídico o Médico:</strong> La información sobre normativas o biotecnología es puramente divulgativa y no sustituye la consulta con abogados o facultativos colegiados.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">4. Límite Absoluto de Responsabilidad e Inmunidad frente a Reclamaciones</h2>
                <p>
                    En la máxima medida permitida por la legislación internacional aplicable, <strong>Glodaxia, sus propietarios, administradores, directores, redactores, colaboradores y proveedores tecnológicos no serán responsables bajo ninguna circunstancia</strong> por daños directos, indirectos, incidentales, punitivos, lucro cesante o pérdidas derivadas de la lectura, interpretación o uso del contenido del portal.
                </p>
                <p>
                    Si alguna jurisdicción no permitiese la exclusión total de ciertas responsabilidades, la responsabilidad agregada máxima de Glodaxia frente a cualquier reclamación quedará expresamente limitada a la cantidad total de <strong>cero dólares estadounidenses ($0.00 USD)</strong> o el importe que el usuario haya pagado por el servicio en los últimos 3 meses (siendo el acceso a Glodaxia de carácter gratuito).
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Cláusula de Indemnidad Universal (*Indemnification*)</h2>
                <p>
                    El usuario acepta defender, indemnizar y mantener indemne a Glodaxia y a todo su equipo frente a cualquier reclamación, litigio, demanda, daño, sanción, coste o gasto de honorarios legales interpuestos por terceros que se deriven del uso indebido de la plataforma o del incumplimiento de estos términos.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">6. Marcas de Terceros, Citas y Notificación de Derechos (DMCA / DSA)</h2>
                <p>
                    Todos los nombres comerciales, logotipos, marcas registradas y productos de terceros mencionados pertenecen a sus respectivos titulares. Su mención en Glodaxia se realiza con fines informativos, de referencia periodística y bajo amparo de *Fair Use*.
                </p>
                <p>
                    Si eres titular de derechos y consideras que algún contenido debe ser corregido, rectificado o retirado, disponemos de un protocolo ágil y directo. Puedes escribirnos a <a href="mailto:legal@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">legal@glodaxia.com</a> o a <a href="mailto:hi@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">hi@glodaxia.com</a>, y atenderemos tu solicitud a la mayor brevedad.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/25 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-950 dark:text-amber-300 uppercase tracking-wider mb-2">Legal Notice: Binding Terms of Use</h3>
                    <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        Accessing, browsing, reading, subscribing to, or using <strong>Glodaxia</strong> (hereinafter, "the Platform", "the Site", or "we") constitutes full, unconditional, and irrevocable acceptance of these Terms of Service. If you do not agree with any part of these terms, please discontinue using the Site immediately.
                    </p>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Platform Nature: Opinion Journalism, Analysis & Digital Media</h2>
                <p>
                    <strong>Glodaxia</strong> operates primarily as an <strong>independent digital media publication focused on opinion journalism, critical analysis, technology reporting, and analytical commentary</strong>. Our articles and publications reflect the opinions, analytical perspectives, and editorial views of our writers, editors, and contributors regarding global tech developments.
                </p>
                <p>
                    All content is published strictly for <strong>informational, educational, public discussion, and commentary purposes</strong>, fully protected under universal principles of freedom of expression and press freedom.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Global Protection of Freedom of Speech and Opinion</h2>
                <p>
                    Editorial opinions, reviews, benchmarks, and commentary published on Glodaxia are legally protected under:
                </p>
                <ul>
                    <li><strong>Article 19 of the Universal Declaration of Human Rights</strong> (inalienable right to freedom of opinion and expression).</li>
                    <li>The <strong>First Amendment to the United States Constitution</strong> and the judicial defense of commentary and non-actionable opinion.</li>
                    <li><strong>Article 10 of the European Convention on Human Rights</strong> and equivalent international press freedom safeguards.</li>
                    <li>The <strong>Fair Use Doctrine (17 U.S.C. § 107)</strong> and the Right of Quotation for reporting, commentary, product reviews, and public interest news analysis.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">3. Complete Disclaimer of Professional Advice ("AS IS")</h2>
                <p>
                    ALL CONTENT IS PROVIDED STRICTLY <strong>"AS IS"</strong> AND "AS AVAILABLE", WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED:
                </p>
                <ul>
                    <li><strong>No Financial or Investment Advice:</strong> Analyses regarding tech companies, stocks, cryptoassets, or market valuations express journalistic opinion. <strong>They do not constitute financial, investment, or trading recommendations.</strong> Readers bear sole responsibility for their economic decisions.</li>
                    <li><strong>No Technical or Cybersecurity Advice:</strong> Code snippets, server commands, or system architectures are theoretical examples. Glodaxia assumes no liability for production outages, security vulnerabilities, or data loss.</li>
                    <li><strong>No Legal or Medical Advice:</strong> Regulatory discussions or health tech overviews are educational and do not substitute certified counsel.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">4. Universal Limitation of Liability</h2>
                <p>
                    To the maximum extent permitted by applicable global law, <strong>Glodaxia, its owners, operators, editors, authors, and infrastructure providers shall not be held liable</strong> for any direct, indirect, incidental, punitive, or consequential damages resulting from the use or interpretation of our content.
                </p>
                <p>
                    Our total aggregate liability for any dispute shall not exceed <strong>zero U.S. dollars ($0.00 USD)</strong> or the amount paid by the user in the preceding three months (access to Glodaxia being entirely free).
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">5. Comprehensive Indemnification (*Hold Harmless*)</h2>
                <p>
                    You agree to defend, indemnify, and hold harmless Glodaxia and its team from and against any third-party claims, liabilities, damages, judgments, or legal fees arising from your use of the platform or violation of these terms.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">6. Third-Party Trademarks, DMCA & Notice-and-Takedown</h2>
                <p>
                    All third-party trademarks, product names, and brand logos mentioned on this site belong to their respective owners and are referenced solely for editorial and identification purposes under Fair Use.
                </p>
                <p>
                    For copyright notices, corrections, or right-of-reply inquiries, contact us directly at <a href="mailto:legal@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">legal@glodaxia.com</a> or <a href="mailto:hi@glodaxia.com" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">hi@glodaxia.com</a> for prompt resolution.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>