<x-layouts.app :title="__('ui.privacy_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-blue-500/10 text-blue-700 dark:text-blue-400 text-xs font-black uppercase tracking-widest border border-blue-500/20">
                    {{ app()->getLocale() === 'es' ? 'Protección de Datos & GDPR / CCPA' : 'Data Protection & GDPR / CCPA' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Privacidad y Protección de Datos' : 'Privacy & Data Protection Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Información detallada sobre el tratamiento de datos personales, bases legales, medidas de seguridad y ejercicio de derechos conforme al RGPD (UE 2016/679) y la CCPA.' 
                    : 'Comprehensive disclosure regarding personal data processing, legal bases, security measures, and rights under the GDPR (EU 2016/679) and CCPA.' }}
            </p>
        </div>

        <div class="prose prose-slate dark:prose-invert max-w-none">
            @if(app()->getLocale() === 'es')
                <p>
                    La presente Política de Privacidad regula el tratamiento de los datos personales recabados a través de <strong>Glodaxia</strong>, en estricto cumplimiento del <strong>Reglamento General de Protección de Datos de la UE (RGPD - Reglamento UE 2016/679)</strong>, la <strong>Ley Orgánica 3/2018 (LOPD-GDD)</strong> y la <strong>California Consumer Privacy Act (CCPA)</strong>.
                </p>

                <h2>1. Responsable del Tratamiento de los Datos</h2>
                <div class="p-5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 not-prose mb-6 text-sm">
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Titular: <span class="font-normal text-slate-700 dark:text-slate-300">Glodaxia Digital Media</span></p>
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Finalidad Principal: <span class="font-normal text-slate-700 dark:text-slate-300">Difusión periodística de contenidos y gestión de boletines</span></p>
                    <p class="mb-0 text-slate-900 dark:text-white font-bold">Email de Contacto y Privacidad: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>

                <h2>2. Datos Personales Objeto de Tratamiento</h2>
                <ul>
                    <li><strong>Datos facilitados voluntariamente:</strong> Dirección de correo electrónico cuando el usuario se suscribe voluntariamente a nuestro boletín informativo (*Newsletter*).</li>
                    <li><strong>Datos técnicos y de navegación:</strong> Dirección IP anonimizada mediante truncamiento, User-Agent, resolución, sistema operativo y páginas consultadas, procesados de forma agregada para la seguridad del servidor y mitigación de ciberataques.</li>
                </ul>

                <h2>3. Base Jurídica y Finalidades del Tratamiento</h2>
                <ul>
                    <li><strong>Consentimiento Expreso (Art. 6.1.a RGPD):</strong> Para el envío de boletines informativos a usuarios que lo han solicitado voluntariamente.</li>
                    <li><strong>Interés Legítimo y Seguridad (Art. 6.1.f RGPD):</strong> Para la protección de servidores, prevención de ataques DDoS y estabilidad de la plataforma.</li>
                    <li><strong>Cumplimiento Legal (Art. 6.1.c RGPD):</strong> Para atender requerimientos formales de autoridades judiciales competentes.</li>
                </ul>

                <h2>4. Declaración Expresa de No Venta de Datos</h2>
                <p>
                    <strong>Glodaxia declara formalmente que NUNCA vende, alquila, comercializa ni cede datos personales a intermediarios de datos (*data brokers*), redes publicitarias ni a terceros para fines de lucro.</strong> Tampoco utilizamos los datos de nuestros suscriptores para entrenar modelos externos de inteligencia artificial.
                </p>

                <h2>5. Conservación de los Datos</h2>
                <p>Los correos de suscriptores se conservan hasta que el usuario solicite su baja o supresión. Los registros de acceso al servidor (*logs*) se purgan o anonimizan en un plazo máximo de noventa (90) días.</p>

                <h2>6. Ejercicio de Derechos (ARCO / GDPR / CCPA)</h2>
                <p>Usted puede en cualquier momento ejercer sus derechos de <strong>Acceso, Rectificación, Supresión (Derecho al Olvido), Limitación, Portabilidad y Oposición</strong> escribiendo a: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>, o utilizando el enlace de baja automática incluido en cada correo.</p>

                <h2>7. Seguridad Técnica</h2>
                <p>Implementamos cifrado forzoso TLS 1.3 (HTTPS), verificación criptográfica CSRF en todos los formularios y aislamiento de bases de datos PostgreSQL en contenedores seguros.</p>
            @else
                <p>
                    This Privacy Policy details how <strong>Glodaxia</strong> processes personal data in compliance with the <strong>EU General Data Protection Regulation (GDPR)</strong> and the <strong>California Consumer Privacy Act (CCPA)</strong>.
                </p>

                <h2>1. Data Controller</h2>
                <div class="p-5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 not-prose mb-6 text-sm">
                    <p class="mb-1 text-slate-900 dark:text-white font-bold">Controller: <span class="font-normal text-slate-700 dark:text-slate-300">Glodaxia Digital Media</span></p>
                    <p class="mb-0 text-slate-900 dark:text-white font-bold">Privacy Contact: <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code></p>
                </div>

                <h2>2. Data We Collect</h2>
                <p>We only collect email addresses for voluntary newsletter subscriptions and anonymized server telemetry strictly for infrastructure defense.</p>

                <h2>3. Absolute No-Sale Pledge</h2>
                <p><strong>We do not sell, rent, or lease personal information to third parties or data brokers.</strong></p>

                <h2>4. Your Inalienable Rights</h2>
                <p>You have the right to access, rectify, or demand the immediate deletion of your data by emailing <code class="text-cyan-600 dark:text-cyan-400 font-mono font-bold">{{ config('global.contact_email', 'hi@glodaxia.com') }}</code>.</p>
            @endif
        </div>
    </div>
</x-layouts.app>