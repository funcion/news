<x-layouts.app :title="__('ui.cookie_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-black uppercase tracking-widest border border-amber-500/20">
                    {{ app()->getLocale() === 'es' ? 'Gestión de Cookies & Directiva ePrivacy' : 'Cookie Governance & ePrivacy' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Vigente: Agosto 2026' : 'Effective: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Cookies y Almacenamiento Local' : 'Cookie & Local Storage Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Transparencia técnica sobre las cookies empleadas en Glodaxia, finalidad de cada tipología, persistencia en LocalStorage y mecanismos de revocación.' 
                    : 'Technical transparency regarding cookies deployed on Glodaxia, categorization, LocalStorage persistence, and revocation mechanisms.' }}
            </p>
        </div>

        <div class="prose prose-slate dark:prose-invert max-w-none">
            @if(app()->getLocale() === 'es')
                <p>
                    Esta Política de Cookies forma parte integrante de nuestra Política de Privacidad y Términos de Uso, y se emite en conformidad con la <strong>Directiva 2002/58/CE (Directiva ePrivacy)</strong>, el <strong>Reglamento General de Protección de Datos (RGPD)</strong> y el artículo 22.2 de la <strong>LSSI-CE</strong>.
                </p>

                <h2>1. ¿Qué es una Cookie y qué tecnologías empleamos?</h2>
                <p>
                    Una cookie es un pequeño fichero de texto que los sitios web descargan en su dispositivo al acceder a determinadas páginas. Permiten recordar sus preferencias y asegurar la navegación. Además de cookies HTTP, empleamos tecnologías equivalentes como <code>localStorage</code> del navegador.
                </p>

                <h2>2. Clasificación Exhaustiva de Cookies Utilizadas</h2>
                <div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">⚙️</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Cookies Técnicas Estrictamente Necesarias</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal mb-3">
                            Imprescindibles para permitir la navegación, mantener la seguridad de las sesiones contra ataques CSRF y recordar la preferencia de consentimiento.
                        </p>
                        <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1 pl-4 list-disc">
                            <li><strong>XSRF-TOKEN:</strong> Seguridad contra falsificación de peticiones (Sesión).</li>
                            <li><strong>glodaxia_session:</strong> Identificador de sesión técnica anónima (2 horas).</li>
                            <li><strong>cc_cookie:</strong> Registro del consentimiento otorgado en CookieConsent (182 días).</li>
                        </ul>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">📊</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Cookies Analíticas Agregadas</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal mb-3">
                            Recopilan métricas de lectura y rendimiento del servidor de manera agregada y 100% anonimizada sin identificar individualmente al lector.
                        </p>
                        <ul class="text-[11px] text-slate-500 dark:text-slate-400 space-y-1 pl-4 list-disc">
                            <li><strong>_ga / _gid:</strong> Conteo de visitas anónimas con anonimización de IP (Opcionales, solo con su consentimiento).</li>
                        </ul>
                    </div>
                </div>

                <h2>3. Tecnologías de Almacenamiento Local (LocalStorage)</h2>
                <p>Glodaxia utiliza <code>localStorage</code> del navegador exclusivamente para parámetros visuales sin almacenar datos sensibles:</p>
                <ul>
                    <li><code>darkMode</code>: Registra si usted prefiere el <em>Modo Claro</em> o <em>Modo Oscuro</em> para evitar parpadeos visuales al recargar.</li>
                    <li><code>cookie_consent_accepted</code>: Persiste la decisión sobre cookies en navegadores antiguos.</li>
                </ul>

                <h2>4. Mecanismo de Revocación y Cambio de Preferencias</h2>
                <p>
                    Usted puede revocar o modificar su consentimiento de cookies en cualquier momento haciendo clic en el enlace permanente ubicado en el pie de página de nuestro portal:
                </p>
                <div class="not-prose my-4">
                    <button type="button" data-cc="show-preferencesModal" class="py-2.5 px-5 rounded-xl bg-[#2b7fff] hover:bg-blue-600 text-white font-bold text-xs shadow-md transition-colors inline-flex items-center gap-2">
                        <span>🍪</span> Abrir Centro de Preferencias de Cookies
                    </button>
                </div>

                <h2>5. Cómo Bloquear o Eliminar Cookies desde su Navegador</h2>
                <p>Usted puede en cualquier momento permitir, bloquear o eliminar las cookies instaladas en su equipo mediante las opciones de configuración de su navegador (Chrome, Firefox, Safari, Edge).</p>
            @else
                <p>
                    This Cookie Policy constitutes an integral component of our Privacy Policy and Terms of Service, formulated in accordance with the <strong>ePrivacy Directive</strong> and the <strong>EU GDPR</strong>.
                </p>

                <h2>1. Technical Categorization</h2>
                <ul>
                    <li><strong>Strictly Necessary:</strong> Session security, CSRF protection, and theme preference state.</li>
                    <li><strong>Aggregate Analytics:</strong> Anonymous readership measurement enabled solely upon express consent.</li>
                </ul>

                <h2>2. Instant Revocation</h2>
                <p>You can adjust or revoke your cookie choices at any moment via the button below or the permanent footer link:</p>
                <div class="not-prose my-4">
                    <button type="button" data-cc="show-preferencesModal" class="py-2.5 px-5 rounded-xl bg-[#2b7fff] hover:bg-blue-600 text-white font-bold text-xs shadow-md transition-colors inline-flex items-center gap-2">
                        <span>🍪</span> Open Cookie Preferences Modal
                    </button>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>