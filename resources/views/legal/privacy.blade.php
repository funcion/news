<x-layouts.app :title="__('ui.privacy_policy') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-12">
        <div class="mb-8">
            <span class="px-3 py-1 rounded-full bg-cyan-500/10 text-xs font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest inline-block mb-3">
                {{ app()->getLocale() === 'es' ? 'Privacidad' : 'Privacy' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Política de Privacidad' : 'Privacy Policy' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last updated: August 2026' }}
            </p>
        </div>

        <div class="prose prose-base md:prose-lg max-w-none text-gray-700 dark:text-gray-300 prose-headings:font-black prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400 leading-relaxed">
            @if(app()->getLocale() === 'es')
                <h2>1. Información que Recopilamos</h2>
                <p>Respetamos tu privacidad. Solo recopilamos información de forma voluntaria, como tu dirección de correo electrónico cuando te suscribes a nuestro boletín de noticias.</p>

                <h2>2. Uso de la Información</h2>
                <p>Utilizamos tu información exclusivamente para enviarte actualizaciones tecnológicas relevantes y mejorar tu experiencia en nuestro portal. Nunca vendemos, alquilamos ni compartimos tus datos con terceros para fines comerciales.</p>

                <h2>3. Cumplimiento con GDPR y CCPA</h2>
                <p>De acuerdo con el Reglamento General de Protección de Datos (GDPR) y la Ley de Privacidad del Consumidor de California (CCPA), tienes derecho a acceder, rectificar o solicitar la eliminación total de tus datos en cualquier momento.</p>
            @else
                <h2>1. Information We Collect</h2>
                <p>We respect your privacy. We only collect personal information when voluntarily submitted, such as your email address when subscribing to our tech newsletter.</p>

                <h2>2. How We Use Information</h2>
                <p>We use collected data solely to deliver relevant editorial updates and enhance site performance. We do not sell, rent, or trade your personal data with third parties.</p>

                <h2>3. GDPR & CCPA Compliance</h2>
                <p>Under GDPR and CCPA regulations, you have the right to request access to, rectification of, or deletion of your personal data at any time.</p>
            @endif
        </div>
    </div>
</x-layouts.app>