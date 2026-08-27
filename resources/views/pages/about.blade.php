<x-layouts.app :trendingTags="$trendingTags ?? collect()">
    <x-slot:title>{{ __('ui.about_hero_title') }} | {{ config('app.name', 'Glodaxia') }}</x-slot>
    <x-slot:metaDescription>{{ __('ui.about_hero_subtitle') ?? 'Conoce la misión y estándares de periodismo tecnológico de ' . config('app.name', 'Glodaxia') }}</x-slot>
    @section('title', __('ui.about_hero_title') . ' — ' . __('ui.site_name'))
    @section('meta_description', __('ui.about_hero_subtitle'))

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-24">
        
        <!-- 1. Hero Section (Minimalist & Sleek) -->
        <div class="text-center max-w-5xl mx-auto mb-20 sm:mb-28">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20 mb-6">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                <span>{{ __('ui.about_hero_badge') }}</span>
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.15] mb-6">
                {{ __('ui.about_hero_title') }}
            </h1>
            
            <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-300 font-normal leading-relaxed">
                {{ __('ui.about_hero_subtitle') }}
            </p>
        </div>

        <!-- 2. Section: Our Story & Identity (Fluid Editorial Flow - No Harsh Boxes) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start pb-20 sm:pb-28 border-b border-slate-200/70 dark:border-slate-800/80">
            <div class="lg:col-span-5">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-3 block">
                    {{ __('ui.about_who_we_are_heading') }}
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-snug">
                    Tecnología analizada desde las entrañas, sin sensacionalismo.
                </h2>
            </div>
            <div class="lg:col-span-7 space-y-6 text-base sm:text-lg text-slate-600 dark:text-slate-300 font-normal leading-relaxed">
                <p>
                    {{ __('ui.about_who_we_are_p1') }}
                </p>
                <p>
                    {{ __('ui.about_who_we_are_p2') }}
                </p>
            </div>
        </div>

        <!-- 3. Section: Mission, Vision, Values (Strictly 1 Row, 3 Columns on Desktop) -->
        <div class="py-20 sm:py-28 border-b border-slate-200/70 dark:border-slate-800/80">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Principios
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ __('ui.about_mvv_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-12">
                <!-- Column 1: Misión -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-6 shadow-sm">
                        <svg aria-hidden="true"  class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 text-center">
                        {{ __('ui.about_mission_title') }}
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal text-center max-w-sm mx-auto">
                        {{ __('ui.about_mission_desc') }}
                    </p>
                </div>

                <!-- Column 2: Visión -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6 shadow-sm">
                        <svg aria-hidden="true"  class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 text-center">
                        {{ __('ui.about_vision_title') }}
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal text-center max-w-sm mx-auto">
                        {{ __('ui.about_vision_desc') }}
                    </p>
                </div>

                <!-- Column 3: Valores -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6 shadow-sm">
                        <svg aria-hidden="true"  class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 text-center">
                        {{ __('ui.about_values_title') }}
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal text-center max-w-sm mx-auto">
                        {{ __('ui.about_values_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 4. Section: Core Pillars (Clean Grid) -->
        <div class="py-20 sm:py-28 border-b border-slate-200/70 dark:border-slate-800/80">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-2 block">
                    Especialización
                </span>
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ __('ui.about_pillars_heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <!-- Pillar 1: AI -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true"  class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mb-2">
                            {{ __('ui.about_pillar_1_title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            {{ __('ui.about_pillar_1_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 2: Security -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true"  class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mb-2">
                            {{ __('ui.about_pillar_2_title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            {{ __('ui.about_pillar_2_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 3: Hardware -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true"  class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mb-2">
                            {{ __('ui.about_pillar_3_title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            {{ __('ui.about_pillar_3_desc') }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 4: Software -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg aria-hidden="true"  class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mb-2">
                            {{ __('ui.about_pillar_4_title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                            {{ __('ui.about_pillar_4_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- "And Much More" Open Curiosity Note -->
            <div class="mt-10 p-6 sm:p-8 rounded-3xl bg-cyan-500/5 dark:bg-cyan-500/10 border border-cyan-500/20 text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 mb-3">
                    <span>✨</span>
                    <span>{{ __('ui.about_pillars_more_title') }}</span>
                </div>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-normal leading-relaxed">
                    {{ __('ui.about_pillars_more_desc') }}
                </p>
            </div>
        </div>

        <!-- 5. Section: E-E-A-T & AI Transparency (Minimalist Clean Blocks) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-14 py-20 sm:py-28 border-b border-slate-200/70 dark:border-slate-800/80">
            <!-- EEAT -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg aria-hidden="true"  class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                        {{ __('ui.about_eeat_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                    {{ __('ui.about_eeat_desc') }}
                </p>
            </div>

            <!-- AI Transparency -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                        <svg aria-hidden="true"  class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                        {{ __('ui.about_ai_transparency_title') }}
                    </h3>
                </div>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed font-normal">
                    {{ __('ui.about_ai_transparency_desc') }}
                </p>
            </div>
        </div>

        <!-- 6. Section: Editorial Contact CTA (Spacious, Roomy Pill Button) -->
        <div class="text-center pt-20 sm:pt-28">
            <h3 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
                {{ __('ui.about_contact_cta') }}
            </h3>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 mb-10 max-w-xl mx-auto leading-relaxed font-normal">
                {{ __('ui.about_contact_desc') }}
            </p>
            <div>
                <a href="{{ app()->getLocale() === 'es' ? url('/es/contacto') : url('/contact') }}" 
                   class="inline-flex items-center justify-center gap-3 px-12 py-5 rounded-full bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-base tracking-wider uppercase shadow-xl shadow-cyan-500/25 hover:shadow-cyan-500/40 transition-all duration-300 transform hover:-translate-y-1 active:translate-y-0 cursor-pointer">
                    <svg aria-hidden="true"  class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('ui.about_contact_btn') }}</span>
                </a>
            </div>
        </div>

    </div>
</x-layouts.app>