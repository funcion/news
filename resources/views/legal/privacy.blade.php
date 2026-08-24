<x-layouts.app :title="__('ui.privacy_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-blue-500/10 text-blue-700 dark:text-blue-400 text-xs font-black uppercase tracking-widest border border-blue-500/20">
                    {{ app()->getLocale() === 'es' ? 'Protección de Datos' : 'Data Protection' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente desde: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Privacidad y Protección de Datos' : 'Privacy & Data Protection Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Conoce cómo recopilamos, protegemos y gestionamos tu información personal bajo los estándares internacionales de GDPR y CCPA.' 
                    : 'Learn how we collect, safeguard, and manage your personal information in compliance with international GDPR and CCPA standards.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-li:text-slate-700 dark:prose-li:text-slate-300 prose-strong:text-slate-900 dark:prose-strong:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400">
            @if(app()->getLocale() === 'es')
                <p>
                    En <strong>Glodaxia</strong> (en adelante, "el Portal" o "nosotros"), nos tomamos muy en serio la privacidad y seguridad de nuestros usuarios. Esta Política de Privacidad describe qué datos recopilamos, cómo los utilizamos y qué derechos tienes sobre tu información.
                </p>

                <h2>1. Responsable del Tratamiento</h2>
                <p>
                    El responsable del tratamiento de los datos recabados en este sitio web es el equipo editorial de <strong>Glodaxia</strong>. Para cualquier consulta referente a tu privacidad, puedes contactarnos en: <code class="text-cyan-600 dark:text-cyan-400">hi@glodaxia.com</code>.
                </p>

                <h2>2. Datos que Recopilamos</h2>
                <ul>
                    <li><strong>Datos facilitados voluntariamente:</strong> Dirección de correo electrónico cuando te suscribes voluntariamente a nuestro boletín de noticias (*Newsletter*).</li>
                    <li><strong>Datos técnicos y de navegación:</strong> Dirección IP anonimizada, tipo de navegador, sistema operativo, páginas visitadas y tiempo de lectura, recopilados de forma agregada para métricas de rendimiento.</li>
                </ul>

                <h2>3. Finalidad del Tratamiento</h2>
                <p>Utilizamos la información recopilada exclusivamente para:</p>
                <ul>
                    <li>Enviar periódicamente resúmenes informativos y noticias destacadas de tecnología (solo con tu consentimiento previo).</li>
                    <li>Optimizar la velocidad, estabilidad y seguridad técnica del servidor.</li>
                    <li>Medir tendencias generales de lectura para mejorar la calidad de nuestra cobertura informativa.</li>
                </ul>
                <p><strong>Bajo ninguna circunstancia vendemos, alquilamos ni comercializamos tus datos personales con terceros o anunciantes.</strong></p>

                <h2>4. Base Legal para el Tratamiento</h2>
                <p>
                    El tratamiento de tu correo electrónico se basa en tu <strong>consentimiento explícito</strong> al suscribirte. Para las métricas analíticas anonimizadas y la seguridad del servidor, la base legal es nuestro <strong>interés legítimo</strong> en mantener la plataforma operativa y protegida contra ciberataques.
                </p>

                <h2>5. Tus Derechos (GDPR / CCPA / ARCO)</h2>
                <p>Como usuario, tienes derecho en cualquier momento a:</p>
                <ul>
                    <li><strong>Acceso:</strong> Solicitar qué datos personales conservamos sobre ti.</li>
                    <li><strong>Rectificación:</strong> Modificar tus datos si son incorrectos.</li>
                    <li><strong>Supresión (Derecho al Olvido):</strong> Solicitar la eliminación total e inmediata de tu email de nuestra base de datos.</li>
                    <li><strong>Darse de baja instantáneamente:</strong> Cada email del boletín incluye un enlace directo para cancelar la suscripción con un solo clic.</li>
                </ul>

                <h2>6. Seguridad de la Información</h2>
                <p>
                    Implementamos certificados de cifrado SSL/TLS (HTTPS) de 256 bits, cortafuegos de aplicaciones y servidores con aislamiento de procesos para salvaguardar tu información contra accesos no autorizados.
                </p>
            @else
                <p>
                    At <strong>Glodaxia</strong> ("the Portal", "we", or "us"), protecting your personal data is a foundational commitment. This Privacy Policy details how we collect, process, and protect your information.
                </p>

                <h2>1. Data Controller</h2>
                <p>
                    The data controller for information processed through this website is the editorial team of <strong>Glodaxia</strong>. For privacy-related inquiries, contact us at: <code class="text-cyan-600 dark:text-cyan-400">hi@glodaxia.com</code>.
                </p>

                <h2>2. Data We Collect</h2>
                <ul>
                    <li><strong>Voluntarily Provided Data:</strong> Email address submitted when opting into our tech news brief.</li>
                    <li><strong>Technical & Analytical Data:</strong> Anonymized IP addresses, browser specifications, page response times, and reading duration collected in aggregate.</li>
                </ul>

                <h2>3. How We Use Your Data</h2>
                <ul>
                    <li>To deliver curated technical updates and digests (only upon explicit opt-in).</li>
                    <li>To monitor infrastructure performance, mitigate DDoS attacks, and optimize caching.</li>
                    <li>To analyze aggregate readership trends to elevate editorial quality.</li>
                </ul>
                <p><strong>We do not sell, lease, or monetize your personal data with third-party data brokers or advertisers.</strong></p>

                <h2>4. Your Legal Rights (GDPR & CCPA)</h2>
                <p>You have the legal right to:</p>
                <ul>
                    <li>Request full access to the personal data we store on you.</li>
                    <li>Request immediate rectification or permanent erasure of your data.</li>
                    <li>Opt out and unsubscribe instantly via the one-click unsubscribe link present in every newsletter edition.</li>
                </ul>

                <h2>5. Data Security</h2>
                <p>
                    All traffic is encrypted in transit via industry-standard TLS 1.3 / HTTPS encryption and protected by server-side firewall isolation.
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>