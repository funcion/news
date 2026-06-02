<x-layouts.app>
    <x-slot:title>{{ __('ui.metodologia_title') }} | {{ config('app.name') }}</x-slot>
    <x-slot:metaDescription>{{ __('ui.metodologia_meta_desc') }}</x-slot>

    <x-slot:head>
        <meta property="og:title" content="{{ __('ui.metodologia_title') }} | {{ config('app.name') }}" />
        <meta property="og:description" content="{{ __('ui.metodologia_meta_desc') }}" />
    </x-slot>

    <div class="max-w-3xl">
        <!-- Header -->
        <header class="mb-16">
            <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 text-[10px] font-black uppercase tracking-[0.3em] rounded-lg mb-6">{{ __('ui.about') }}</span>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.05] mb-8">
                {{ __('ui.metodologia_heading') }}
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ __('ui.metodologia_intro') }}
            </p>
        </header>

        <!-- Section: How it works -->
        <section class="mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6">{{ __('ui.metodologia_how_title') }}</h2>
            <ol class="space-y-6 list-decimal pl-6 text-slate-600 dark:text-slate-400">
                <li><strong class="text-slate-900 dark:text-white">{{ __('ui.metodologia_step1_title') }}</strong> — {{ __('ui.metodologia_step1_desc') }}</li>
                <li><strong class="text-slate-900 dark:text-white">{{ __('ui.metodologia_step2_title') }}</strong> — {{ __('ui.metodologia_step2_desc') }}</li>
                <li><strong class="text-slate-900 dark:text-white">{{ __('ui.metodologia_step3_title') }}</strong> — {{ __('ui.metodologia_step3_desc') }}</li>
                <li><strong class="text-slate-900 dark:text-white">{{ __('ui.metodologia_step4_title') }}</strong> — {{ __('ui.metodologia_step4_desc') }}</li>
            </ol>
        </section>

        <!-- Section: AI Transparency -->
        <section class="mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6">{{ __('ui.metodologia_transparency_title') }}</h2>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                {{ __('ui.metodologia_transparency_desc') }}
            </p>
            <ul class="space-y-3 text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-cyan-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('ui.metodologia_bullet1') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-cyan-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('ui.metodologia_bullet2') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-cyan-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('ui.metodologia_bullet3') }}
                </li>
            </ul>
        </section>

        <!-- Section: Sources -->
        <section class="mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6">{{ __('ui.metodologia_sources_title') }}</h2>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ __('ui.metodologia_sources_desc') }}
            </p>
        </section>
    </div>
</x-layouts.app>
