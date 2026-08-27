<x-layouts.app>
    <x-slot:robots>noindex, nofollow</x-slot>
    <x-slot:title>{{ __('ui.privacy_policy') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>{{ app()->getLocale() === 'es' ? 'Conoce nuestra política de privacidad: cumplimiento de GDPR y CCPA, garantía de no venta de datos y protección total de tu información en ' . config('app.name', 'Glodaxia') . '.' : 'Read our comprehensive privacy policy: GDPR & CCPA compliance, zero-data-selling guarantee, and strict user information safeguards on ' . config('app.name', 'Glodaxia') . '.' }}</x-slot>
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ __('ui.privacy_policy') }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-blue-500/10 text-blue-700 dark:text-blue-400 text-xs font-black uppercase tracking-widest border border-blue-500/20">
                    {{ app()->getLocale() === 'es' ? 'Protección Global de Datos (RGPD / CCPA / LGPD)' : 'Global Data Protection (GDPR / CCPA / LGPD)' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Privacidad y Protección de Datos Personales' : 'Privacy & Personal Data Protection Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Transparencia absoluta, bases jurídicas de tratamiento, medidas de ciberseguridad y ejercicio de derechos conforme al RGPD (UE 2016/679), CCPA/CPRA (EE. UU.), LGPD y normativas internacionales.' 
                    : 'Transparent disclosure regarding legal processing bases, data security protocols, and global user rights under the EU GDPR, CCPA/CPRA, LGPD, and international privacy standards.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 rounded-2xl bg-blue-500/5 border border-blue-500/20 shadow-sm not-prose mb-8">
                    <h3 class="text-base font-black text-blue-900 dark:text-blue-300 uppercase tracking-wider mb-2">Compromiso Fundamental de Privacidad</h3>
                    <p class="text-sm text-blue-950 dark:text-blue-200/90 leading-relaxed font-medium mb-0">
                        En <strong>Glodaxia</strong> la privacidad del usuario es un derecho inviolable. <strong>NUNCA vendemos, alquilamos, monetizamos ni transferimos sus datos personales a intermediarios de datos (*data brokers*), redes publicitarias de terceros ni a empresas externas.</strong> Tampoco utilizamos los datos de los usuarios para alimentar ni entrenar modelos de inteligencia artificial de terceros.
                    </p>
                </div>

                <h2>1. Responsable del Tratamiento de Datos</h2>
                <div class="p-5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 not-prose mb-6 text-sm">
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Denominación: <span class="font-normal text-slate-700 dark:text-slate-300">Glodaxia Media Network</span></p>
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Actividad Principal: <span class="font-normal text-slate-700 dark:text-slate-300">Plataforma digital de periodismo, análisis tecnológico y divulgación</span></p>
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Sitio Web Oficial: <span class="font-normal text-slate-700 dark:text-slate-300">https://glodaxia.com</span></p>
                    <p class="mb-0 text-slate-900 dark:text-white font-bold">Delegado de Privacidad & Contacto: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>

                <h2>2. Marco Legal y Cobertura Territorial Internacional</h2>
                <p>Esta política se rige y adapta rigurosamente a las normativas de protección de datos más estrictas del mundo, incluyendo pero sin limitarse a:</p>
                <ul>
                    <li><strong>Unión Europea y Reino Unido:</strong> Reglamento General de Protección de Datos (RGPD - Reglamento UE 2016/679), Directiva ePrivacy 2002/58/CE y UK GDPR.</li>
                    <li><strong>Estados Unidos:</strong> California Consumer Privacy Act (CCPA) y California Privacy Rights Act (CPRA), junto con leyes estatales de privacidad de Virginia (VCDPA), Colorado (CPA), Connecticut (CTDPA) y Utah (UCPA).</li>
                    <li><strong>Latinoamérica y resto del mundo:</strong> Ley General de Protección de Datos Personales (LGPD - Brasil), Ley Federal de Protección de Datos Personales en Posesión de los Particulares (México), Ley 1581/2012 (Colombia) y principios universales de privacidad.</li>
                </ul>

                <h2>3. Categorías de Datos Objeto de Tratamiento</h2>
                <p>Glodaxia opera bajo el principio estricto de <strong>minimización de datos (Art. 5.1.c RGPD)</strong>, recabando únicamente lo estrictamente indispensable:</p>
                <ul>
                    <li><strong>Datos facilitados voluntariamente por el usuario:</strong> Dirección de correo electrónico al suscribirse voluntariamente a nuestro boletín de noticias (*Newsletter*) o nombre, correo y contenido del mensaje al utilizar el formulario de contacto oficial.</li>
                    <li><strong>Datos técnicos y de seguridad de red:</strong> Dirección IP anonimizada mediante truncamiento de bytes, identificador de agente de usuario (*User-Agent*), fecha/hora de acceso y páginas consultadas, procesados de forma temporal y agregada con el único fin de mitigar ataques DDoS, prevenir abusos en el servidor y salvaguardar la infraestructura.</li>
                    <li><strong>Preferencias de consentimiento:</strong> Registro local en el navegador del usuario a través de cookies técnicas sobre las preferencias de consentimiento expresado en el banner CookieConsent v3.</li>
                </ul>

                <h2>4. Bases Jurídicas y Finalidades del Tratamiento</h2>
                <ol>
                    <li><strong>Consentimiento Expreso del Interesado (Art. 6.1.a RGPD):</strong> Para la remisión periódica de boletines informativos y para dar respuesta a solicitudes de contacto o soporte iniciadas por el usuario.</li>
                    <li><strong>Interés Legítimo y Seguridad de la Red (Art. 6.1.f RGPD):</strong> Para garantizar la seguridad del sistema informático, detectar accesos no autorizados, mitigar ataques de denegación de servicio y optimizar la velocidad de entrega del contenido.</li>
                    <li><strong>Cumplimiento de Obligaciones Legales (Art. 6.1.c RGPD):</strong> Para atender requerimientos formales, legítimos y debidamente motivados emitidos por autoridades judiciales o administrativas competentes.</li>
                </ol>

                <h2>5. Cláusula Estricta "Do Not Sell or Share My Personal Info"</h2>
                <p>
                    En cumplimiento de la CCPA/CPRA y los más altos estándares éticos, <strong>Glodaxia declara que no vende, no alquila, no comparte ni comercializa información personal de ningún usuario bajo ninguna circunstancia.</strong> No participamos en intercambios comerciales de listas de correo ni en perfilado cruzado con redes de seguimiento de terceros.
                </p>

                <h2>6. Proveedores de Infraestructura y Transferencias Internacionales</h2>
                <p>
                    Para la entrega global de contenidos y alta disponibilidad, Glodaxia utiliza proveedores tecnológicos líderes (como Cloudflare para CDN, seguridad DDoS y almacenamiento de objetos R2, y servidores dedicados en centros de datos certificados ISO/IEC 27001). Todas las transferencias internacionales de datos se realizan bajo <strong>Cláusulas Contractuales Tipo (SCC) aprobadas por la Comisión Europea</strong> y acuerdos de procesamiento de datos con cifrado de extremo a extremo.
                </p>

                <h2>7. Plazos de Conservación y Supresión de Datos</h2>
                <ul>
                    <li><strong>Suscripciones de Newsletter:</strong> Los datos se conservan únicamente mientras el usuario mantenga activa su suscripción. Cada correo incluye un enlace de desuscripción automática con un solo clic (*One-Click Unsubscribe*), suprimiéndose los datos de la lista de envío de inmediato.</li>
                    <li><strong>Registros Técnicos de Servidor (*Logs*):</strong> Se purgan, anonimizan o eliminan automáticamente en un plazo máximo de noventa (90) días.</li>
                </ul>

                <h2>8. Ejercicio Universal de Derechos (ARCO / RGPD / CCPA / LGPD)</h2>
                <p>
                    Cualquier usuario, con independencia de su país de residencia, puede ejercer de forma gratuita y en cualquier momento sus derechos de:
                </p>
                <ul>
                    <li><strong>Acceso:</strong> Conocer qué datos personales conservamos.</li>
                    <li><strong>Rectificación:</strong> Solicitar la corrección de datos inexactos o incompletos.</li>
                    <li><strong>Supresión ("Derecho al Olvido"):</strong> Solicitar la eliminación total e irreversible de sus datos de nuestros registros.</li>
                    <li><strong>Limitación del Tratamiento y Oposición:</strong> Restringir oponerse al procesamiento de sus datos por motivos legítimos.</li>
                    <li><strong>Portabilidad:</strong> Recibir sus datos en un formato digital estructurado y de uso común.</li>
                    <li><strong>Revocación del Consentimiento:</strong> Retirar el consentimiento otorgado previamente sin efectos retroactivos.</li>
                </ul>
                <p>
                    Para ejercer cualquiera de estos derechos, remita su solicitud por correo electrónico a: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>. Atenderemos y responderemos toda solicitud en un plazo máximo de treinta (30) días hábiles.
                </p>

                <h2>9. Protección de Menores (COPPA & RGPD)</h2>
                <p>
                    Glodaxia es una publicación orientada al público general y no recopila deliberadamente datos personales de menores de 16 años (o la edad mínima legal en su jurisdicción). Si un padre, madre o tutor legal tiene conocimiento de que un menor nos ha facilitado datos personales sin consentimiento, solicitamos que nos contacte de inmediato para proceder a su eliminación irrevocable.
                </p>

                <h2>10. Medidas de Seguridad Técnicas y Criptográficas</h2>
                <p>
                    Adoptamos medidas de seguridad de vanguardia: cifrado forzoso TLS 1.3 / HTTPS en todas las comunicaciones, encabezados de seguridad HTTP (HSTS, Content-Security-Policy, X-Frame-Options), validación criptográfica de tokens CSRF, aislamiento de bases de datos PostgreSQL en redes privadas sin exposición pública y copias de seguridad redundantes cifradas.
                </p>
            @else
                <div class="p-6 rounded-2xl bg-blue-500/5 border border-blue-500/20 shadow-sm not-prose mb-8">
                    <h3 class="text-base font-black text-blue-900 dark:text-blue-300 uppercase tracking-wider mb-2">Fundamental Privacy Pledge</h3>
                    <p class="text-sm text-blue-950 dark:text-blue-200/90 leading-relaxed font-medium mb-0">
                        At <strong>Glodaxia</strong>, privacy is an absolute human right. <strong>We NEVER sell, lease, monetize, or trade your personal information with data brokers, ad networks, or third parties.</strong> We never feed subscriber data into AI training sets.
                    </p>
                </div>

                <h2>1. Data Controller & Privacy Officer</h2>
                <div class="p-5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 not-prose mb-6 text-sm">
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Entity: <span class="font-normal text-slate-700 dark:text-slate-300">Glodaxia Media Network</span></p>
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Website: <span class="font-normal text-slate-700 dark:text-slate-300">https://glodaxia.com</span></p>
                    <p class="mb-0 text-slate-900 dark:text-white font-bold">Privacy Desk: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>

                <h2>2. Global Regulatory Compliance</h2>
                <p>This policy complies comprehensively with the <strong>EU General Data Protection Regulation (GDPR - EU 2016/679)</strong>, <strong>UK GDPR</strong>, <strong>California Consumer Privacy Act (CCPA/CPRA)</strong>, US State Privacy Laws, Brazil's <strong>LGPD</strong>, and universal data governance standards.</p>

                <h2>3. Categories of Data Processed</h2>
                <ul>
                    <li><strong>Voluntarily Provided Data:</strong> Email addresses for newsletter subscriptions or inquiries submitted through our verified contact form.</li>
                    <li><strong>Technical Telemetry:</strong> Anonymized IP hashes, User-Agent strings, and server access logs strictly processed for DDoS prevention, bot mitigation, and infrastructure integrity.</li>
                    <li><strong>Consent Flags:</strong> Stored locally in your browser via CookieConsent v3.</li>
                </ul>

                <h2>4. Absolute No-Sale & No-Share Pledge (CCPA/CPRA)</h2>
                <p><strong>Glodaxia does not sell, share, or rent personal data.</strong> We do not engage in cross-context behavioral advertising.</p>

                <h2>5. Exercise of Global User Rights</h2>
                <p>You may exercise your rights of <strong>Access, Rectification, Erasure ("Right to Be Forgotten"), Restriction, Data Portability, and Objection</strong> by contacting: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>