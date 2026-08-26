<x-layouts.app :trendingTags="$trendingTags ?? collect()">
    @section('title', __('ui.about_hero_title') . ' — ' . __('ui.site_name'))
    @section('meta_description', __('ui.about_hero_subtitle'))

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        
        <!-- Hero Section -->
        <div class="text-center max-w-4xl mx-auto mb-16 sm:mb-24">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-500/30 mb-6 shadow-xs">
                <span>⚡</span>
                <span>{{ __('ui.about_hero_badge') }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-950 dark:text-white uppercase leading-[1.1] mb-6">
                {{ __('ui.about_hero_title') }}
            </h1>
            
            <p class="text-base sm:text-xl text-slate-600 dark:text-slate-300 font-normal leading-relaxed max-w-3xl mx-auto">
                {{ __('ui.about_hero_subtitle') }}
            </p>
        </div>

        <!-- Section 1: Who We Are & Story -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-cyan-950 p-8 sm:p-12 md:p-16 text-white border border-cyan-500/30 shadow-2xl mb-16 sm:mb-24">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
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

        <!-- Section 2: Mission, Vision, Values Grid -->
        <div class="mb-16 sm:mb-24">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Principios
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-950 dark:text-white uppercase">
                    {{ __('ui.about_mvv_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                <!-- Mission -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-cyan-500/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 transition-transform">
                            🎯
                        </div>
                        <h3 class="text-xl font-black text-slate-950 dark:text-white mb-3">
                            {{ __('ui.about_mission_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_mission_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Vision -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-500/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 transition-transform">
                            🔭
                        </div>
                        <h3 class="text-xl font-black text-slate-950 dark:text-white mb-3">
                            {{ __('ui.about_vision_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_vision_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Values -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-2xl mb-6 group-hover:scale-110 transition-transform">
                            💎
                        </div>
                        <h3 class="text-xl font-black text-slate-950 dark:text-white mb-3">
                            {{ __('ui.about_values_title') }}
                        </h3>
                        <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('ui.about_values_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Core Coverage Pillars -->
        <div class="mb-16 sm:mb-24">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Especialización
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-950 dark:text-white uppercase">
                    {{ __('ui.about_pillars_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <!-- Pillar 1 -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-cyan-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-lg">
                            🧠
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-950 dark:text-white">
                            {{ __('ui.about_pillar_1_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_1_desc') }}
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                            🛡️
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-950 dark:text-white">
                            {{ __('ui.about_pillar_2_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_2_desc') }}
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg">
                            ⚡
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-950 dark:text-white">
                            {{ __('ui.about_pillar_3_title') }}
                        </h3>
                    </div>
                    <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ __('ui.about_pillar_3_desc') }}
                    </p>
                </div>

                <!-- Pillar 4 -->
                <div class="rounded-3xl p-8 bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/40 transition-all duration-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg">
                            💻
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-950 dark:text-white">
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
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">👑</span>
                    <h3 class="text-lg font-black text-slate-950 dark:text-white uppercase tracking-tight">
                        {{ __('ui.about_eeat_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    {{ __('ui.about_eeat_desc') }}
                </p>
            </div>

            <!-- AI Transparency Box -->
            <div class="rounded-3xl p-8 bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">🤖</span>
                    <h3 class="text-lg font-black text-slate-950 dark:text-white uppercase tracking-tight">
                        {{ __('ui.about_ai_transparency_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    {{ __('ui.about_ai_transparency_desc') }}
                </p>
            </div>
        </div>

        <!-- Section 5: Editorial Contact CTA -->
        <div class="text-center rounded-3xl p-8 sm:p-14 bg-gradient-to-br from-white via-slate-50 to-cyan-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-cyan-950/30 border border-cyan-500/20 dark:border-cyan-500/30 shadow-lg">
            <h3 class="text-2xl sm:text-3xl font-black text-slate-950 dark:text-white tracking-tight uppercase mb-3">
                {{ __('ui.about_contact_cta') }}
            </h3>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-8 max-w-xl mx-auto leading-relaxed">
                {{ __('ui.about_contact_desc') }}
            </p>
            <a href="{{ route('contact.show') }}" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-sm tracking-wide shadow-lg shadow-cyan-500/25 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                <span>✉️</span>
                <span>{{ __('ui.about_contact_btn') }}</span>
            </a>
        </div>

    </div>
</x-layouts.app>