<x-layouts.app :title="__('ui.terms_of_service') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
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
                    ? 'Contrato vinculante de uso, limitaciones de responsabilidad legal, exenciones técnicas y financieras, derechos de propiedad intelectual y cláusulas de indemnidad.' 
                    : 'Binding user agreement, limitation of liability, technical and financial disclaimers, intellectual property protections, and indemnification clauses.' }}
            </p>
        </div>

        <div class="prose prose-slate dark:prose-invert max-w-none">
            @if(app()->getLocale() === 'es')
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2">Aviso Legal Importante y Aceptación de Términos</h3>
                    <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        El acceso, navegación o utilización de cualquier página o servicio de Glodaxia constituye la aceptación plena, incondicional e irrevocable de los presentes Términos y Condiciones. Si usted no está conforme con alguna de las cláusulas aquí estipuladas, debe abstenerse de utilizar este sitio web inmediatamente.
                    </p>
                </div>

                <h2>1. Objeto del Servicio y Fines Exclusivamente Informativos</h2>
                <p>
                    <strong>Glodaxia</strong> es una publicación digital de divulgación tecnológica e informativa. Todos los artículos, tutoriales, comparativas, noticias y recursos disponibles se proporcionan exclusivamente con fines <strong>educativos, formativos y de divulgación</strong>.
                </p>

                <h2>2. Exención Total de Responsabilidad Financiera, Legal y Técnica ("AS IS")</h2>
                <p>
                    EL CONTENIDO SE PROPORCIONA ESTRICTAMENTE "TAL CUAL" (*AS IS*) Y "SEGÚN DISPONIBILIDAD" (*AS AVAILABLE*), SIN GARANTÍAS DE NINGÚN TIPO, YA SEAN EXPRESAS O IMPLÍCITAS:
                </p>
                <ul>
                    <li><strong>Ausencia de Asesoramiento Financiero o de Inversión:</strong> Ninguna publicación, análisis de mercado, artículo sobre criptoactivos o valores tecnológicos constituye recomendación de inversión. Las decisiones financieras son responsabilidad exclusiva del usuario.</li>
                    <li><strong>Ausencia de Asesoramiento Técnico o de Ciberseguridad:</strong> Las instrucciones de configuración de servidores, fragmentos de código fuente (*code snippets*), scripts de terminal o recomendaciones arquitectónicas se publican como ejemplos conceptuales. Glodaxia no asume responsabilidad alguna por caídas de servicio, pérdida de datos, brechas de seguridad o daños en hardware o software derivados de su implementación.</li>
                    <li><strong>Ausencia de Garantía de Exactitud Temporal:</strong> Glodaxia no garantiza que la información técnica permanezca actualizada o libre de errores en todo momento.</li>
                </ul>

                <h2>3. Asistencia de Inteligencia Artificial y Supervisión Humana</h2>
                <p>
                    En Glodaxia combinamos el periodismo tecnológico de investigación con la asistencia avanzada de inteligencia artificial. La IA se utiliza exclusivamente como herramienta de soporte para la recopilación y síntesis de datos. <strong>Todo el contenido es rigurosamente verificado, contrastado, revisado y curado por redactores y editores humanos antes de su publicación.</strong> Supervisión editorial humana 100% garantizada.
                </p>

                <h2>4. Propiedad Intelectual y Prohibición Estricta de Scraping</h2>
                <ul>
                    <li><strong>Cita Periodística Permitida:</strong> Se autoriza la cita de fragmentos breves (máximo 150 palabras) con fines informativos, siempre que se incorpore una mención expresa y un <strong>enlace canónico visible y directo (dofollow)</strong> al artículo original en Glodaxia.</li>
                    <li><strong>Prohibición Absoluta de Raspado Masivo (*Scraping*):</strong> Queda terminantemente prohibido el uso de bots, rastreadores automatizados, scripts de extracción de datos o técnicas de ingeniería inversa para copiar o republicar nuestra base de datos.</li>
                    <li><strong>Prohibición de Entrenamiento de Modelos de IA no Autorizados:</strong> Queda expresamente prohibido el uso del contenido de Glodaxia para el entrenamiento masivo de modelos de lenguaje o sistemas de IA sin acuerdo comercial previo por escrito.</li>
                </ul>

                <h2>5. Procedimiento DMCA / Retirada de Derechos de Autor</h2>
                <p>
                    Para enviar una notificación formal de infracción de derechos de autor conforme a la <strong>Digital Millennium Copyright Act (DMCA)</strong>, remita los detalles al correo: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.
                </p>

                <h2>6. Límite Absoluto de Responsabilidad y Daños Consecuenciales</h2>
                <p>
                    EN LA MÁXIMA MEDIDA PERMITIDA POR LA LEY, EN NINGÚN CASO GLODAXIA, SUS PROPIETARIOS, DIRECTORES O REDACTORES SERÁN RESPONSABLES POR DAÑOS DIRECTOS, INDIRECTOS, INCIDENTALES, CONSECUENCIALES O PUNITIVOS (INCLUYENDO PÉRDIDA DE BENEFICIOS, LUCRO CESANTE O PÉRDIDA DE DATOS). LA RESPONSABILIDAD TOTAL MÁXIMA DE GLODAXIA ANTE CUALQUIER RECLAMACIÓN ESTARÁ LIMITADA A CERO DÓLARES ($0.00 USD).
                </p>

                <h2>7. Obligación de Indemnidad por el Usuario</h2>
                <p>
                    El usuario se compromete a indemnizar, defender y mantener indemne a Glodaxia y a su equipo frente a cualquier reclamación, demanda, daño, coste o gasto (incluidos honorarios de abogados) derivados del uso indebido del sitio web o la infracción de estos Términos.
                </p>

                <h2>8. Jurisdicción y Ley Aplicable</h2>
                <p>
                    Estos Términos se rigen por las leyes aplicables en la jurisdicción del titular del portal. Cualquier controversia se someterá a los tribunales competentes de dicha demarcación.
                </p>
            @else
                <div class="p-6 md:p-8 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-sm not-prose mb-10">
                    <h3 class="text-base font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2">Important Legal Notice & Terms Acceptance</h3>
                    <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200/90 leading-relaxed font-medium mb-0">
                        By accessing, browsing, or utilizing Glodaxia, you agree to be unconditionally and irrevocably bound by these Terms of Service. If you do not agree, you must immediately terminate use of this website.
                    </p>
                </div>

                <h2>1. Purpose of the Platform</h2>
                <p>All materials, tutorials, architectural reviews, and dispatches are published strictly for educational, informational, and entertainment purposes.</p>

                <h2>2. Complete Disclaimer of Warranties ("AS IS")</h2>
                <p>ALL CONTENT IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS WITHOUT WARRANTIES OF ANY KIND. We disclaim all liability for financial, technical, or operational decisions made based on this publication.</p>

                <h2>3. Absolute Limitation of Liability</h2>
                <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, GLODAXIA DISCLAIMS ALL LIABILITY FOR CONSEQUENTIAL, INDIRECT, OR PUNITIVE DAMAGES. MAXIMUM AGGREGATE LIABILITY SHALL NOT EXCEED ZERO DOLLARS ($0.00 USD).</p>

                <h2>4. DMCA Copyright Notice</h2>
                <p>Send formal DMCA takedown requests to <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>