<x-layouts.app :title="__('ui.terms_of_service') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-purple-500/10 text-purple-700 dark:text-purple-400 text-xs font-black uppercase tracking-widest border border-purple-500/20">
                    {{ app()->getLocale() === 'es' ? 'Condiciones Generales & Blindaje Legal' : 'Terms of Use & Legal Shield' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Términos y Condiciones de Uso' : 'Terms of Service & Disclaimer' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Contrato vinculante de uso de nuestra plataforma de noticias y análisis tecnológico, limitaciones de responsabilidad legal y cláusulas de indemnidad.' 
                    : 'Binding user agreement for our tech news and analysis platform, limitation of liability, and indemnification clauses.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2">Aviso Legal Importante y Aceptación de Términos</h3>
                    <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        El acceso, lectura o utilización de Glodaxia constituye la aceptación incondicional e irrevocable de los presentes Términos. Si no está de acuerdo con alguna disposición, debe cesar el uso de la plataforma de inmediato.
                    </p>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Objeto: Plataforma Digital de Análisis de Noticias Tecnológicas</h2>
                <p>
                    <strong>Glodaxia</strong> es una plataforma digital de periodismo, análisis crítico de noticias, comparativas técnicas y divulgación sobre el ecosistema tecnológico. Todos los contenidos, noticias, tutoriales y recursos se ofrecen exclusivamente con fines <strong>informativos, formativos y de divulgación</strong>.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Exención Total de Responsabilidad Financiera, Legal y Técnica ("AS IS")</h2>
                <p>
                    EL CONTENIDO SE PROPORCIONA ESTRICTAMENTE "TAL CUAL" (*AS IS*) Y "SEGÚN DISPONIBILIDAD" (*AS AVAILABLE*):
                </p>
                <ul>
                    <li><strong>Ausencia de Asesoramiento Financiero o de Inversión:</strong> Los análisis de noticias sobre valores tecnológicos, empresas cotizadas, mercados cripto o modelos de negocio NO constituyen asesoramiento ni recomendación de inversión. Las decisiones financieras son responsabilidad exclusiva del lector.</li>
                    <li><strong>Ausencia de Asesoramiento Técnico o de Ciberseguridad:</strong> Los análisis arquitectónicos, comandos de terminal o ejemplos de código son conceptos ilustrativos. Glodaxia no responde por caídas de producción, brechas de seguridad o pérdida de datos derivadas de su uso.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">3. Asistencia de Inteligencia Artificial y Supervisión Humana</h2>
                <p>
                    Utilizamos IA como herramienta de apoyo documental y analítico. <strong>Todo el contenido es rigurosamente verificado, editado y curado por periodistas y redactores humanos antes de publicarse.</strong> Supervisión editorial humana 100% garantizada.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">4. Propiedad Intelectual y Prohibición de Scraping</h2>
                <ul>
                    <li><strong>Cita Periodística Autorizada:</strong> Se autoriza citar fragmentos breves con mención expresa y <strong>enlace canónico visible y directo (dofollow)</strong> a la noticia original en Glodaxia.</li>
                    <li><strong>Prohibición Absoluta de Scraping y Minería de Datos:</strong> Queda terminantemente prohibida la extracción masiva automatizada con bots o scrapers.</li>
                    <li><strong>Prohibición de Entrenamiento de Modelos de IA:</strong> Se prohíbe el uso de la base de datos de Glodaxia para entrenar modelos de IA sin acuerdo previo por escrito.</li>
                </ul>

                <h2 class="text-slate-900 dark:text-white font-black">5. Procedimiento DMCA / Notificación de Derechos de Autor</h2>
                <p>
                    Para notificaciones formales de retirada por derechos de autor (DMCA), escriba a: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">6. Límite Absoluto de Responsabilidad</h2>
                <p>
                    EN LA MÁXIMA MEDIDA PERMITIDA POR LA LEY, GLODAXIA Y SUS EDITORES NO RESPONDERÁN POR DAÑOS INDIRECTOS, LUCRO CESANTE O PÉRDIDA DE DATOS. LA RESPONSABILIDAD TOTAL MÁXIMA ACUMULADA ANTE CUALQUIER RECLAMACIÓN ESTARÁ LIMITADA A CERO DÓLARES ($0.00 USD).
                </p>

                <h2 class="text-slate-900 dark:text-white font-black">7. Indemnidad por Parte del Usuario</h2>
                <p>
                    El usuario defenderá e indemnizará a Glodaxia y a su equipo frente a cualquier reclamación de terceros derivada del uso ilícito de la plataforma o la infracción de estos Términos.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2">Important Legal Notice & Terms Acceptance</h3>
                    <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        By accessing or utilizing Glodaxia, you agree to be bound by these Terms. If you do not agree, terminate use immediately.
                    </p>
                </div>

                <h2 class="text-slate-900 dark:text-white font-black">1. Platform Mission: Tech News & Analysis</h2>
                <p>Glodaxia is a digital technology journalism, news analysis, and research publication. All content is published for informational and educational purposes.</p>

                <h2 class="text-slate-900 dark:text-white font-black">2. Disclaimer of Warranties ("AS IS")</h2>
                <p>All content is provided "as is". No financial, investment, or technical warranty is implied. Maximum aggregate liability is limited to zero dollars ($0.00 USD).</p>

                <h2 class="text-slate-900 dark:text-white font-black">3. DMCA Takedown Desk</h2>
                <p>Send notices to <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>