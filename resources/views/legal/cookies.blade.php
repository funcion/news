<x-layouts.app>
    <x-slot:robots>noindex, nofollow</x-slot>
    <x-slot:title>{{ __('ui.cookie_policy') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>{{ app()->getLocale() === 'es' ? 'Descubre cómo ' . config('app.name', 'Glodaxia') . ' utiliza cookies esenciales y analíticas para garantizar una navegación segura y ofrecerte la mejor experiencia de lectura.' : 'Learn how ' . config('app.name', 'Glodaxia') . ' uses essential cookies and performance analytics to ensure secure browsing and deliver the best reading experience on our site.' }}</x-slot>
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-14">
        <!-- Accessible Breadcrumbs (ADA / WCAG Compliant) -->
        <nav aria-label="{{ __('ui.breadcrumbs') }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-8">
            <a href="{{ url(app()->getLocale() === 'es' ? '/es' : '/') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded">{{ __('ui.home') }}</a>
            <svg class="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">{{ __('ui.cookie_policy') }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <span class="px-3 py-1 rounded-md bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-xs font-black uppercase tracking-widest border border-cyan-500/20">
                    {{ app()->getLocale() === 'es' ? 'Transparencia & ePrivacy Directive' : 'Transparency & ePrivacy Directive' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Cookies y Tecnologías de Almacenamiento Local' : 'Cookie & Local Storage Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Información detallada y transparente sobre el uso de cookies técnicas, analíticas, almacenamiento local y control de preferencias del usuario conforme a la Directiva ePrivacy y el RGPD.' 
                    : 'Clear and transparent disclosure regarding technical cookies, analytics, local storage, and granular user consent controls under the ePrivacy Directive and GDPR.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-base md:prose-lg prose-cyan dark:prose-dark max-w-none text-slate-700 dark:text-slate-200">
            @if(app()->getLocale() === 'es')
                <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white mb-1">Centro de Preferencias de Consentimiento</h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-normal mb-0">
                            Puede modificar o revocar su consentimiento de cookies en cualquier momento.
                        </p>
                    </div>
                    <button type="button" data-cc="show-preferencesModal" class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold uppercase tracking-wider transition-colors shrink-0 shadow-sm">
                        Configurar Cookies
                    </button>
                </div>

                <h2>1. ¿Qué son las Cookies y Tecnologías Similares?</h2>
                <p>
                    Las cookies son pequeños archivos de texto que los sitios web almacenan en el navegador del usuario al visitarlos. Permiten que la plataforma recuerde configuraciones básicas (como el tema visual oscuro o claro, la sesión activa o el idioma preferido) y proteja contra ataques de falsificación de peticiones en sitios cruzados (CSRF).
                </p>

                <h2>2. Tipología y Clasificación de Cookies en Glodaxia</h2>
                <p>En Glodaxia clasificamos las tecnologías de almacenamiento en dos categorías estrictas:</p>
                
                <h3>A. Cookies Técnicas y Estrictamente Necesarias (Exentas de Consentimiento Previo)</h3>
                <p>Son esenciales para el funcionamiento técnico, la navegación y la seguridad de la plataforma. No almacenan información personal identificable y no pueden desactivarse:</p>
                <ul>
                    <li><strong><code>XSRF-TOKEN</code> / <code>laravel_session</code>:</strong> Garantiza la seguridad de los formularios y previene ataques maliciosos de tipo CSRF. (Duración: Sesión / 2 horas).</li>
                    <li><strong><code>theme</code> / LocalStorage:</strong> Guarda la preferencia del usuario entre modo claro y modo oscuro. (Duración: Persistente).</li>
                    <li><strong><code>cc_cookie</code>:</strong> Almacena el estado y las opciones de consentimiento seleccionadas por el usuario en el gestor CookieConsent v3. (Duración: 6 meses).</li>
                </ul>

                <h3>B. Cookies Analíticas y de Rendimiento (Requieren Consentimiento Previo)</h3>
                <p>
                    Utilizadas exclusivamente para medir de forma agregada y anónima el rendimiento de la web, tiempos de carga y artículos más leídos con el objetivo de mejorar la experiencia del usuario. <strong>No realizan rastreo cruzado entre sitios (*cross-site tracking*) ni elaboran perfiles comerciales.</strong>
                </p>

                <h2>3. Declaración de No Rastreo Publicitario de Terceros</h2>
                <p>
                    Glodaxia no utiliza cookies de seguimiento invasivo de redes publicitarias externas para comerciar con datos de navegación del usuario.
                </p>

                <h2>4. Cómo Desactivar o Eliminar Cookies desde su Navegador</h2>
                <p>
                    Además de nuestro centro de preferencias, el usuario puede permitir, bloquear o eliminar las cookies instaladas en su equipo mediante la configuración de las opciones de su navegador:
                </p>
                <ul>
                    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/es-es/HT201265" target="_blank" rel="noopener noreferrer">Apple Safari</a></li>
                    <li><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-las-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
                </ul>
            @else
                <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm not-prose mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white mb-1">Consent Preferences Center</h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-normal mb-0">
                            You may review, modify, or revoke your cookie choices at any time.
                        </p>
                    </div>
                    <button type="button" data-cc="show-preferencesModal" class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold uppercase tracking-wider transition-colors shrink-0 shadow-sm">
                        Cookie Settings
                    </button>
                </div>

                <h2>1. What are Cookies?</h2>
                <p>Cookies are small text files stored in your browser to maintain security, preserve layout preferences (such as Dark/Light mode), and protect forms.</p>

                <h2>2. Cookies We Use</h2>
                <ul>
                    <li><strong>Strictly Necessary Cookies:</strong> <code>laravel_session</code>, <code>XSRF-TOKEN</code>, and <code>cc_cookie</code> to maintain session integrity, CSRF cryptographic protection, and consent tracking.</li>
                    <li><strong>Performance Telemetry:</strong> Aggregated, anonymized metrics without cross-site tracking.</li>
                </ul>

                <h2>3. Browser Management</h2>
                <p>You can block or purge cookies anytime via your browser settings or using our built-in Consent Center button above.</p>
            @endif
        </div>
    </div>
</x-layouts.app>