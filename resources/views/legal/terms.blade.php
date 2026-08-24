<x-layouts.app :title="__('ui.terms_of_service') . ' | ' . config('app.name')">
    <div class="max-w-4xl mx-auto px-4 py-8 lg:py-12">
        <div class="mb-8">
            <span class="px-3 py-1 rounded-full bg-cyan-500/10 text-xs font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest inline-block mb-3">
                {{ app()->getLocale() === 'es' ? 'Legal' : 'Legal' }}
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'es' ? 'Términos y Condiciones de Uso' : 'Terms of Service' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ app()->getLocale() === 'es' ? 'Última actualización: Agosto 2026' : 'Last updated: August 2026' }}
            </p>
        </div>

        <div class="prose prose-base md:prose-lg max-w-none text-gray-700 dark:text-gray-300 prose-headings:font-black prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-cyan-600 dark:prose-a:text-cyan-400 leading-relaxed">
            @if(app()->getLocale() === 'es')
                <h2>1. Aceptación de los Términos</h2>
                <p>Al acceder y utilizar este sitio web, aceptas cumplir con los presentes Términos y Condiciones de Uso, todas las leyes y regulaciones aplicables, y aceptas que eres responsable del cumplimiento de las leyes locales aplicables.</p>

                <h2>2. Propiedad Intelectual y Uso del Contenido</h2>
                <p>Todo el contenido original publicado en Glodaxia (textos, análisis, logotipos y elementos gráficos) está protegido por las leyes de derechos de autor. Se permite citar extractos de nuestros artículos siempre que se incluya un enlace directo y visible a la publicación original.</p>

                <h2>3. Descargo de Responsabilidad</h2>
                <p>Los artículos publicados en este sitio tienen fines informativos y educativos. Glodaxia no ofrece asesoramiento financiero, legal o de inversión. Cualquier decisión técnica o de inversión tomada a partir de la información de este sitio es responsabilidad exclusiva del usuario.</p>

                <h2>4. Asistencia de IA y Supervisión Humana</h2>
                <p>En Glodaxia combinamos el periodismo tecnológico de investigación con la asistencia avanzada de inteligencia artificial. Todo el contenido es rigurosamente verificado, revisado y curado por editores humanos antes de su publicación definitiva para garantizar los más altos estándares de calidad y veracidad informativa.</p>
            @else
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using this website, you agree to be bound by these Terms of Service, all applicable laws, and regulations, and agree that you are responsible for compliance with any applicable local laws.</p>

                <h2>2. Intellectual Property and Content Use</h2>
                <p>All original content published on Glodaxia is protected by copyright laws. Brief excerpts and quotes may be shared provided that clear attribution and a direct link to the original article are included.</p>

                <h2>3. Disclaimer</h2>
                <p>The materials on Glodaxia are provided for general educational and informational purposes only. We do not provide financial, legal, or investment advice.</p>

                <h2>4. AI Assistance & Human Editorial Oversight</h2>
                <p>We pair investigative tech journalism with advanced AI assistance. All content is strictly fact-checked, reviewed, and curated by human editors prior to publication to ensure maximum factual accuracy and technical depth.</p>
            @endif
        </div>
    </div>
</x-layouts.app>