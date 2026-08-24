<x-layouts.app :title="__('ui.cookie_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-16">
        <!-- Header -->
        <div class="mb-10 pb-8 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-black uppercase tracking-widest border border-amber-500/20">
                    {{ app()->getLocale() === 'es' ? 'Gestión de Cookies' : 'Cookie Management' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">● {{ app()->getLocale() === 'es' ? 'Actualizado: Agosto 2026' : 'Updated: August 2026' }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4 leading-tight">
                {{ app()->getLocale() === 'es' ? 'Política de Cookies y Tecnologías de Rastreo' : 'Cookie & Tracking Technologies Policy' }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-medium">
                {{ app()->getLocale() === 'es' 
                    ? 'Información transparente sobre el uso de cookies técnicas y analíticas en nuestro portal, y cómo gestionarlas desde tu navegador.' 
                    : 'Transparent information regarding technical and analytics cookies used across our portal and how to manage your preferences.' }}
            </p>
        </div>

        <!-- Content Body -->
        <div class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-li:text-slate-700 dark:prose-li:text-slate-300 prose-strong:text-slate-900 dark:prose-strong:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400">
            @if(app()->getLocale() === 'es')
                <h2>1. ¿Qué son las Cookies?</h2>
                <p>
                    Una cookie es un pequeño archivo de texto que un sitio web almacena en tu navegador cuando lo visitas. Permiten que la web recuerde información sobre tu visita (como tu idioma preferido o tu elección de Modo Oscuro) para que tu próxima navegación sea más rápida, cómoda y personalizada.
                </p>

                <h2>2. Clasificación de Cookies que Utilizamos</h2>
                <p>En Glodaxia clasificamos nuestras cookies en dos categorías principales:</p>

                <div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">⚙️</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Cookies Técnicas Esenciales</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Son estrictamente necesarias para el funcionamiento de la web. Permiten la navegación, protegen contra ataques CSRF y recuerdan tu preferencia de <strong>Modo Oscuro / Modo Claro</strong> y el consentimiento de cookies. No se pueden desactivar.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">📊</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Cookies Analíticas Agregadas</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Nos ayudan a cuantificar de forma totalmente anonimizada el número de lectores y qué artículos de tecnología generan mayor interés, permitiéndonos mejorar continuamente nuestra oferta editorial.
                        </p>
                    </div>
                </div>

                <h2>3. Base de Almacenamiento Local (LocalStorage)</h2>
                <p>
                    Además de cookies, utilizamos <code>localStorage</code> en tu navegador exclusivamente para almacenar:
                </p>
                <ul>
                    <li><code>darkMode</code>: Para recordar de forma instantánea si prefieres el tema claro u oscuro, evitando parpadeos de pantalla al recargar.</li>
                    <li><code>cookie_consent_accepted</code>: Para registrar tu decisión en el banner de consentimiento y no volverte a interrumpir.</li>
                </ul>

                <h2>4. Cómo Administrar o Desactivar las Cookies</h2>
                <p>
                    Puedes permitir, bloquear o eliminar las cookies instaladas en tu equipo mediante la configuración de las opciones de tu navegador web:
                </p>
                <ul>
                    <li><strong>Google Chrome:</strong> Configuración &rarr; Privacidad y seguridad &rarr; Cookies y otros datos de sitios.</li>
                    <li><strong>Mozilla Firefox:</strong> Ajustes &rarr; Privacidad y Seguridad &rarr; Cookies y datos del sitio.</li>
                    <li><strong>Apple Safari:</strong> Preferencias &rarr; Privacidad &rarr; Bloquear todas las cookies.</li>
                    <li><strong>Microsoft Edge:</strong> Configuración &rarr; Cookies y permisos del sitio &rarr; Administrar y eliminar cookies.</li>
                </ul>
            @else
                <h2>1. What Are Cookies?</h2>
                <p>
                    Cookies are small text files placed on your device by websites you visit. They help websites remember preferences (such as your chosen UI theme or language settings) to provide a seamless browsing experience.
                </p>

                <h2>2. Categories of Cookies We Use</h2>

                <div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">⚙️</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Essential Technical Cookies</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Strictly necessary for website operation, CSRF security verification, and remembering your <strong>Dark Mode / Light Mode</strong> preference.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
                        <span class="text-2xl mb-2 block">📊</span>
                        <h4 class="text-base font-black text-slate-900 dark:text-white mb-2">Aggregate Analytics Cookies</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Enable anonymous readership metrics to identify top-performing technical analyses and improve user experience.
                        </p>
                    </div>
                </div>

                <h2>3. Browser LocalStorage Usage</h2>
                <p>We utilize client-side <code>localStorage</code> strictly for:</p>
                <ul>
                    <li><code>darkMode</code>: Prevents flash of unstyled theme on page refresh.</li>
                    <li><code>cookie_consent_accepted</code>: Remembers your cookie preference to prevent banner reappearance.</li>
                </ul>

                <h2>4. Managing and Disabling Cookies</h2>
                <p>
                    You can manage or disable cookies at any time directly through your web browser settings (Chrome, Firefox, Safari, Edge).
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>