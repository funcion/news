<x-layouts.app :title="__('ui.cookie_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-12">
        <div class="mb-8">
            <span class="px-3 py-1 rounded-full bg-cyan-500/10 text-xs font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest inline-block mb-3">
                {{ app()->getLocale() === 'es' ? 'Cookies' : 'Cookies' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Política de Cookies' : 'Cookie Policy' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last updated: August 2026' }}
            </p>
        </div>

        <div class="prose prose-base md:prose-lg max-w-none text-gray-700 dark:text-gray-300 prose-headings:font-black prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400 leading-relaxed">
            @if(app()->getLocale() === 'es')
                <h2>1. ¿Qué son las Cookies?</h2>
                <p>Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo para recordar tus preferencias (como el Modo Oscuro o tu idioma preferido) y medir de forma anónima el rendimiento de la web.</p>

                <h2>2. Tipos de Cookies que Utilizamos</h2>
                <ul>
                    <li><strong>Cookies Esenciales:</strong> Necesarias para recordar tus preferencias de sesión y tema visual.</li>
                    <li><strong>Cookies Analíticas:</strong> Nos ayudan a entender qué artículos son más leídos de forma totalmente anónima.</li>
                </ul>

                <h2>3. Cómo Administrar o Desactivar las Cookies</h2>
                <p>Puedes cambiar tus preferencias o desactivar las cookies en cualquier momento desde la configuración de tu navegador web.</p>
            @else
                <h2>1. What are Cookies?</h2>
                <p>Cookies are small text files stored on your device to remember user preferences (such as Dark Mode or language settings) and measure aggregate site performance.</p>

                <h2>2. Cookies We Use</h2>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required to remember your UI theme and session preferences.</li>
                    <li><strong>Analytics Cookies:</strong> Help us measure anonymous readership trends to improve editorial quality.</li>
                </ul>

                <h2>3. Managing Cookies</h2>
                <p>You can adjust or disable cookies at any time through your browser settings.</p>
            @endif
        </div>
    </div>
</x-layouts.app>