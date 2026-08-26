<x-layouts.app :trendingTags="$trendingTags ?? collect()">
    @section('title', __('ui.about_title') . ' — ' . __('ui.site_name'))
    @section('meta_description', __('ui.about_subtitle'))

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Header / Hero Section -->
        <div class="text-center max-w-3xl mx-auto mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-500/20 mb-5">
                <span>⚡</span>
                <span class="uppercase tracking-wider">{{ __('ui.site_name') }} Magazine</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white uppercase leading-tight mb-5">
                {{ __('ui.about_title') }}
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-300 font-normal leading-relaxed">
                {{ __('ui.about_subtitle') }}
            </p>
        </div>

        <!-- Mission Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-500/10 via-slate-500/5 to-blue-500/10 p-8 sm:p-12 border border-cyan-500/20 dark:border-cyan-500/30 shadow-xs mb-14 sm:mb-20">
            <div class="max-w-3xl">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    {{ __('ui.about_mission_heading') }}
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-snug mb-4">
                    Periodismo técnico riguroso para la era de la computación avanzada.
                </h2>
                <p class="text-base sm:text-lg text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ __('ui.about_mission_text') }}
                </p>
            </div>
        </div>

        <!-- 4 Core Coverage Pillars -->
        <div class="mb-14 sm:mb-20">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    {{ __('ui.about_pillars_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pillar 1 -->
                <div class="rounded-3xl p-7 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-cyan-500/40 transition-all duration-200">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-xl mb-5">
                        🧠
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
                        {{ __('ui.about_pillar_1_title') }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_1_desc') }}
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="rounded-3xl p-7 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-500/40 transition-all duration-200">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xl mb-5">
                        🛡️
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
                        {{ __('ui.about_pillar_2_title') }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_2_desc') }}
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="rounded-3xl p-7 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-500/40 transition-all duration-200">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl mb-5">
                        ⚡
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
                        {{ __('ui.about_pillar_3_title') }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_3_desc') }}
                    </p>
                </div>

                <!-- Pillar 4 -->
                <div class="rounded-3xl p-7 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/40 transition-all duration-200">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xl mb-5">
                        💻
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">
                        {{ __('ui.about_pillar_4_title') }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_4_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- E-E-A-T & Transparency Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-14 sm:mb-20">
            <!-- EEAT Box -->
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-3">
                    <span>👑</span> {{ __('ui.about_eeat_heading') }}
                </span>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ __('ui.about_eeat_text') }}
                </p>
            </div>

            <!-- AI Transparency Box -->
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-3">
                    <span>🤖</span> {{ __('ui.about_ai_transparency_heading') }}
                </span>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ __('ui.about_ai_transparency_text') }}
                </p>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="text-center rounded-3xl p-8 sm:p-12 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
                {{ __('ui.about_contact_cta') }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 max-w-md mx-auto">
                Nuestra redacción recibe informes técnicos, análisis de investigación y filtraciones de seguridad verificables.
            </p>
            <a href="{{ route('contact.show') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm shadow-md shadow-cyan-500/20 transition-transform active:scale-95">
                <span>✉️</span>
                <span>{{ __('ui.about_contact_btn') }}</span>
            </a>
        </div>

    </div>
</x-layouts.app>