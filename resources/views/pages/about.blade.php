<x-layouts.app :trendingTags="$trendingTags ?? collect()">
    @section('title', __('ui.about_hero_title') . ' — ' . __('ui.site_name'))
    @section('meta_description', __('ui.about_hero_subtitle'))

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
        
        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-500/20 mb-6">
                <span>⚡</span>
                <span>{{ __('ui.about_hero_badge') }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 dark:text-white uppercase leading-[1.15] mb-6">
                {{ __('ui.about_hero_title') }}
            </h1>
            
            <p class="text-base sm:text-xl text-slate-600 dark:text-slate-300 font-normal leading-relaxed">
                {{ __('ui.about_hero_subtitle') }}
            </p>
        </div>

        <!-- Section 1: Our Story (Premium Dark Glass Card without harsh white borders) -->
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 dark:bg-slate-900/90 text-white p-8 sm:p-14 md:p-16 border border-slate-800 shadow-2xl mb-16 sm:mb-24">
            <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-400 mb-3 block">
                    {{ __('ui.about_who_we_are_heading') }}
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight leading-tight mb-6">
                    Tecnología analizada desde las entrañas, sin sensacionalismo ni compromisos comerciales.
                </h2>
                <div class="space-y-4 text-base sm:text-lg text-slate-300 font-normal leading-relaxed">
                    <p>{{ __('ui.about_who_we_are_p1') }}</p>
                    <p>{{ __('ui.about_who_we_are_p2') }}</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Mission, Vision, Values (3 Equal Columns Centered with Vector SVGs) -->
        <div class="mb-16 sm:mb-24">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Principios
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    {{ __('ui.about_mvv_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                <!-- Mission Card -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-cyan-500/40 hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3">
                            {{ __('ui.about_mission_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_mission_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Vision Card -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-blue-500/40 hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3">
                            {{ __('ui.about_vision_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_vision_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Values Card -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-emerald-500/40 hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3">
                            {{ __('ui.about_values_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_values_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Core Coverage Pillars (Clean 2x2 Grid) -->
        <div class="mb-16 sm:mb-24">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Especialización
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    {{ __('ui.about_pillars_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <!-- Pillar 1: AI -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-cyan-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                            {{ __('ui.about_pillar_1_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_1_desc') }}
                    </p>
                </div>

                <!-- Pillar 2: Security -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-blue-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                            {{ __('ui.about_pillar_2_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_2_desc') }}
                    </p>
                </div>

                <!-- Pillar 3: Hardware -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-amber-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                            {{ __('ui.about_pillar_3_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_3_desc') }}
                    </p>
                </div>

                <!-- Pillar 4: Software -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:border-emerald-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                            {{ __('ui.about_pillar_4_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_4_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 4: E-E-A-T & AI Transparency (Dual Box) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 mb-16 sm:mb-24">
            <!-- EEAT Box -->
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">
                        {{ __('ui.about_eeat_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    {{ __('ui.about_eeat_desc') }}
                </p>
            </div>

            <!-- AI Transparency Box -->
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">
                        {{ __('ui.about_ai_transparency_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    {{ __('ui.about_ai_transparency_desc') }}
                </p>
            </div>
        </div>

        <!-- Section 5: Editorial Contact CTA (Refined, Roomy, Premium Button) -->
        <div class="text-center rounded-3xl p-10 sm:p-16 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 shadow-xs">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase mb-4">
                {{ __('ui.about_contact_cta') }}
            </h3>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-9 max-w-xl mx-auto leading-relaxed">
                {{ __('ui.about_contact_desc') }}
            </p>
            <div>
                <a href="{{ app()->getLocale() === 'es' ? url('/es/contacto') : url('/contact') }}" 
                   class="inline-flex items-center justify-center gap-3 px-10 py-5 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-sm sm:text-base tracking-wider uppercase shadow-xl shadow-cyan-500/20 hover:shadow-cyan-500/35 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('ui.about_contact_btn') }}</span>
                </a>
            </div>
        </div>

    </div>
</x-layouts.app>