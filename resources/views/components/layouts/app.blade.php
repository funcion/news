<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Prevent Flash of Unstyled Content (FOUC) in Dark Mode -->
    <script>
        (function() {
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'true' || (savedDarkMode === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Custom Head Codes (GA, GTM, Meta, CSS) --}}
    {!! \App\Models\CustomCode::getActive('header_head') !!}

    <title>{{ $title ?? config('app.name', 'Glodaxia') }}</title>
    
    @if(isset($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    
    @if(isset($metaKeywords))
        <meta name="keywords" content="{{ is_array($metaKeywords) ? implode(', ', $metaKeywords) : $metaKeywords }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}" />
    
    <!-- Robots (index/follow by default) -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    
        <!-- Favicons & Modern Web App Suite (Clean SEO Standard) -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
    <link rel="manifest" href="/favicon/manifest.json">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#06b6d4">

    <!-- Open Graph (Facebook, LinkedIn, WhatsApp, Telegram) -->
    <meta property="og:site_name" content="{{ config('app.name', 'Glodaxia') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="{{ $ogType ?? 'website' }}" />
    <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_ES' : 'en_US' }}" />
    <meta property="og:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Glodaxia')) }}" />
    <meta property="og:description" content="{{ $ogDescription ?? ($metaDescription ?? __('ui.meta_desc')) }}" />
    <meta property="og:image" content="{{ $ogImage ?? asset('images/glodaxia-og-cover.svg') }}" />

    <!-- Twitter Card (X, Threads) -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <meta name="twitter:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Glodaxia')) }}" />
    <meta name="twitter:description" content="{{ $ogDescription ?? ($metaDescription ?? __('ui.meta_desc')) }}" />
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/glodaxia-og-cover.svg') }}" />

    <!-- Global Schema.org JSON-LD (WebSite SearchAction & Organization) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/') . '/#organization',
                'name' => 'Glodaxia',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/glodaxia-logo.svg'),
                ],
                'email' => 'hi@glodaxia.com',
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '/#website',
                'url' => url('/'),
                'name' => 'Glodaxia',
                'description' => 'Tech & AI Magazine: Breaking news, artificial intelligence, and digital innovation.',
                'publisher' => [
                    '@id' => url('/') . '/#organization',
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/search') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
                'inLanguage' => ['en', 'es'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Google Sans Font (SIL Open Font License) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Ably Config -->
    <script>
        window.laravelConfig = {
            ably: {
                key: '{{ explode(':', config('broadcasting.connections.ably.key') ?? env('ABLY_KEY'))[0] }}'
            }
        };
    </script>

    {{ $head ?? '' }}
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-[#020617] dark:text-slate-100 flex flex-col min-h-screen relative overflow-x-hidden"
    x-data="{
        mobileMenuOpen: false,
        isDarkMode: false,
        isScrolled: false,
        showBanner: false,
        latestTitle: '',
        newArticles: 0,
        newArticleData: null,
        init() {
            // Check for saved dark mode preference
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode !== null) {
                this.isDarkMode = savedDarkMode === 'true';
            } else {
                this.isDarkMode = document.documentElement.classList.contains('dark');
            }
            
            // Apply dark mode class
            if (this.isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Handle scroll effect
            this.handleScroll();
            window.addEventListener('scroll', () => this.handleScroll());

            // Restore Live Updates (Echo)
            const pollEcho = setInterval(() => {
                if (typeof window.Echo !== 'undefined') {
                    clearInterval(pollEcho);
                    window.Echo.channel('public-news')
                        .listen('ArticlePublished', (e) => {
                            console.log('Live broadcast received:', e);
                            this.newArticles++;
                            this.newArticleData = e;
                            this.latestTitle = '{{ app()->getLocale() }}' === 'es' ? e.title_es : e.title_en;
                            this.showBanner = true;
                        });
                }
            }, 1000);
        },
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            if (this.isDarkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        },
        handleScroll() {
            this.isScrolled = window.scrollY > 10;
        }
    }">
    {{-- Custom Body Codes (GTM noscript, Body Start JS) --}}
    {!! \App\Models\CustomCode::getActive('header_body') !!}
    
    <!-- Premium Header Wrapper -->
    <div class="fixed top-0 inset-x-0 z-50 w-full">
        <!-- Header Bar -->
        <header role="banner" class="w-full fixed-main-header backdrop-blur-md transition-all duration-300 border-b border-slate-200/90 dark:border-slate-800/90 bg-white/90 dark:bg-slate-900/90 shadow-sm"
                x-bind:class="isScrolled ? 'shadow-sm' : ''">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-3 md:py-4 flex items-center justify-between">
                <!-- Logo -->
                <x-ui.logo />
                
                <!-- Right Side: Navigation & Actions -->
                <div class="flex items-center gap-4 lg:gap-8">
                    <!-- Desktop Navigation (hidden on mobile) -->
                    <nav aria-label="{{ app()->getLocale() === 'es' ? 'Navegación principal' : 'Main navigation' }}" class="hidden md:flex items-center gap-5 lg:gap-8">
                        <!-- Home Link -->
                        <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors {{ request()->is('/') ? 'text-cyan-500 dark:text-cyan-400' : '' }}">
                            {{ __('ui.home') }}
                        </a>
                        
                        <!-- Categories Dropdown (CSS Multi-Column Dynamic Flow) -->
                        <div class="relative group nav-item" 
                             x-data="{ open: false }" 
                             @mouseenter="open = true" 
                             @mouseleave="open = false">
                            <button @click="open = !open" 
                                    type="button" 
                                    :aria-expanded="open" 
                                    aria-haspopup="true" 
                                    class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500 rounded-md px-1 cursor-pointer select-none">
                                <span>{{ __('ui.categories') }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Seamless Hover Bridge & Dropdown Wrapper -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute top-full left-1/2 -translate-x-1/2 pt-1.5 z-[100]"
                                 style="width: 440px;"
                                 @click.away="open = false">
                                
                                @php
                                    $categories = \App\Models\Category::whereNull('parent_id')->whereHas('articles', fn($q) => $q->published())->get();
                                @endphp

                                <div class="w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-3 overflow-hidden">
                                    <!-- CSS Multi-Column Flow (No Scrollbar, Naturally Balanced) -->
                                    <div style="column-count: 2; column-gap: 8px; max-height: 400px;">
                                        @foreach($categories as $category)
                                            <a href="{{ $category->url }}" 
                                               class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all duration-150 group break-inside-avoid mb-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500/30 group-hover:bg-cyan-500 group-hover:scale-125 transition-all flex-shrink-0"></span>
                                                <span class="truncate">{{ $category->name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Latest News -->
                        <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors">
                            {{ __('ui.latest_news') }}
                        </a>
                        
                        <div class="h-6 w-px bg-gray-200 dark:bg-white/10 mx-2"></div>
                        
                        <!-- Lang Switcher (Desktop >768px: Single Alternate Flag Toggle) -->
                        <div class="flex items-center pl-1">
                            @if(app()->getLocale() === 'es')
                                <!-- Currently Spanish -> Show USA Flag to Switch to English -->
                                <a hreflang="en" 
                                   href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" 
                                   title="Switch to English"
                                   aria-label="{{ app()->getLocale() === 'es' ? 'Cambiar idioma a Inglés (Switch to English)' : 'Switch language to Spanish (Cambiar a Español)' }}"
                                   class="relative block w-6 h-4 rounded-[3px] overflow-hidden border border-slate-300 dark:border-slate-700 shadow-xs transition-transform duration-200 hover:scale-110">
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                        <path fill="#bd3d44" d="M0 0h640v480H0z"/>
                                        <path stroke="#fff" stroke-width="37" d="M0 55.4h640M0 129.2h640M0 203h640M0 277h640M0 350.8h640M0 424.6h640"/>
                                        <path fill="#192f5d" d="M0 0h290v258.5H0z"/>
                                    </svg>
                                </a>
                            @else
                                <!-- Currently English -> Show Spain Flag to Switch to Spanish -->
                                <a hreflang="es" 
                                   href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" 
                                   title="Cambiar a Español"
                                   aria-label="{{ app()->getLocale() === 'es' ? 'Cambiar idioma a Inglés (Switch to English)' : 'Switch language to Spanish (Cambiar a Español)' }}"
                                   class="relative block w-6 h-4 rounded-[3px] overflow-hidden border border-slate-300 dark:border-slate-700 shadow-xs transition-transform duration-200 hover:scale-110">
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                        <path fill="#aa151b" d="M0 0h640v480H0z"/>
                                        <path fill="#f1bf00" d="M0 120h640v240H0z"/>
                                        <path fill="#aa151b" d="M0 0h640v120H0zm0 360h640v120H0z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </nav>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 sm:gap-4 border-l border-gray-100 dark:border-white/5 pl-4 sm:pl-8">
                        <!-- Search Trigger (Clean Icon Only) -->
                        <button @click="$dispatch('open-search-modal')" 
                                type="button"
                                aria-label="{{ __('ui.search') }}"
                                class="p-2 text-slate-700 dark:text-slate-300 hover:text-cyan-500 dark:hover:text-cyan-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Auth / Profile Button -->
                        @auth
                            <div x-data="{ userMenuOpen: false }" class="relative inline-block">
                                <button @click="userMenuOpen = !userMenuOpen"
                                        aria-label="{{ auth()->user()->name }}"
                                        class="flex items-center gap-1 p-0.5 rounded-full hover:opacity-85 transition cursor-pointer">
                                    <!-- Minimalist Borderless Circular Badge -->
                                    <div style="width: 34px; height: 34px; min-width: 34px; min-height: 34px;"
                                         class="w-[34px] h-[34px] rounded-full bg-slate-100 dark:bg-slate-800 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-[11px] font-bold tracking-normal overflow-hidden flex-shrink-0 select-none">
                                        @if (auth()->user()->avatar_url)
                                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <span class="leading-none text-center">{{ auth()->user()->initials }}</span>
                                        @endif
                                    </div>
                                    <svg class="w-3 h-3 text-slate-400 dark:text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="userMenuOpen"
                                     @click.away="userMenuOpen = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-1.5 z-50 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-850 flex items-center gap-3">
                                        <div style="width: 34px; height: 34px; min-width: 34px; min-height: 34px;"
                                             class="w-[34px] h-[34px] rounded-full bg-slate-200/70 dark:bg-slate-800 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-[11px] font-bold tracking-normal overflow-hidden flex-shrink-0 select-none">
                                            @if (auth()->user()->avatar_url)
                                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover rounded-full">
                                            @else
                                                <span class="leading-none text-center">{{ auth()->user()->initials }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                            <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                    <div class="p-1 space-y-0.5">
                                        @if (auth()->user()->slug === 'admin' || auth()->user()->id === 1)
                                            <a href="/admin" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-cyan-600 dark:hover:text-cyan-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                                <span>⚙️</span>
                                                <span>{{ __('ui.admin_panel') }}</span>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer text-left">
                                                <span>🚪</span>
                                                <span>{{ __('ui.auth_logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/login') }}" class="text-sm font-bold text-slate-900 dark:text-slate-100 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                {{ __('ui.auth_sign_in_button') }}
                            </a>
                        @endauth

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" 
                                aria-label="Toggle dark mode"
                                class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                            <svg x-show="!isDarkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg x-show="isDarkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </button>

                        <!-- Hamburger Button (Standard SVG for reliability) -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                aria-label="Toggle mobile menu"
                                class="md:hidden p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu (dentro del flujo HTML, debajo del header) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 max-h-0"
             x-transition:enter-end="opacity-100 max-h-[100vh]"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 max-h-[400px]"
             x-transition:leave-end="opacity-0 max-h-0"
             class="md:hidden overflow-hidden bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800">
        
        <!-- Contenido del menú -->
        <div class="px-4 py-4 space-y-4 max-h-[350px] overflow-y-auto">
                    <!-- Navegación principal -->
                    <div class="space-y-3">
                        <a href="{{ url('/') }}" 
                           class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1 {{ request()->is('/') ? 'text-cyan-500 dark:text-cyan-400' : '' }}"
                           @click="mobileMenuOpen = false">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('ui.home') }}
                        </a>
                        
                        <!-- Categories Accordion -->
                        <div x-data="{ categoriesOpen: false }" class="border-t border-gray-100 dark:border-slate-800 pt-3">
                            <button @click="categoriesOpen = !categoriesOpen" 
                                    class="flex items-center justify-between w-full text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    {{ __('ui.categories') }}
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': categoriesOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="categoriesOpen" class="mt-2 pl-6 space-y-1">
                                @php
                                    $mobileCategories = \App\Models\Category::whereNull('parent_id')->whereHas('articles', fn($q) => $q->published())->get();
                                @endphp
                                
                                @foreach($mobileCategories as $category)
                                    <a href="{{ $category->url }}" 
                                       class="block text-xs text-gray-600 dark:text-gray-400 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1"
                                       @click="mobileMenuOpen = false">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                                
                                @if($mobileCategories->isEmpty())
                                    <div class="text-xs text-gray-500 dark:text-gray-400 italic py-1">
                                        {{ __('No categories available') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <a href="{{ url('/') }}" 
                           class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1 {{ request()->is('/') ? 'text-cyan-500 dark:text-cyan-400' : '' }}"
                           @click="mobileMenuOpen = false">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            {{ __('ui.latest_news') }}
                        </a>
                        
                        <button @click="mobileMenuOpen = false; $nextTick(() => $dispatch('open-search-modal'))" type="button" class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1 w-full text-left">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        {{ __('ui.search') }}
    </button>
                        
                        <a href="{{ route('contact.show') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ __('ui.contact') }}</a>
                        <a href="#about" 
                           class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1"
                           @click="mobileMenuOpen = false">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('ui.about') }}
                        </a>
                    </div>
                    
                    <!-- Separador -->
                    <!-- Separador -->
                    <div class="border-t border-gray-100 dark:border-slate-800 pt-4">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">{{ __('ui.preferences') }}</h3>
                        
                        <!-- Dark/Light Mode Toggle -->
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.theme') }}</span>
                            </div>
                            <button @click="toggleDarkMode()" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 dark:bg-slate-700 transition-colors"
                                    x-bind:class="isDarkMode ? 'bg-cyan-500' : ''">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                      x-bind:style="isDarkMode ? 'transform: translateX(1.5rem)' : 'transform: translateX(0.25rem)'"></span>
                            </button>
                        </div>
                        
                        <!-- Language Selector con Banderas -->
                        <div class="py-2">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.language') }}</span>
                            </div>
                            
                            <div class="flex gap-3">
                                <!-- Bandera USA -->
                                <a hreflang="en" 
                                   href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" 
                                   class="flex-1 flex flex-col items-center p-3 rounded-lg border transition-all duration-200 {{ app()->getLocale() === 'en' ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20' : 'border-gray-200 dark:border-slate-700 hover:border-cyan-300 dark:hover:border-cyan-600 hover:bg-gray-50 dark:hover:bg-slate-800' }}"
                                   @click="mobileMenuOpen = false">
                                    <div class="w-8 h-6 mb-2 rounded overflow-hidden shadow-sm relative bg-white border border-gray-100 dark:border-white/10">
                                        <!-- Stripes -->
                                        <div class="flex flex-col h-full">
                                            <div class="h-[14%] bg-red-600"></div>
                                            <div class="h-[14%] bg-white"></div>
                                            <div class="h-[14%] bg-red-600"></div>
                                            <div class="h-[14%] bg-white"></div>
                                            <div class="h-[14%] bg-red-600"></div>
                                            <div class="h-[14%] bg-white"></div>
                                            <div class="h-[16%] bg-red-600"></div>
                                        </div>
                                        <!-- Blue Canton -->
                                        <div class="absolute top-0 left-0 w-4 h-3 bg-blue-700 flex items-center justify-center">
                                            <div class="grid grid-cols-3 gap-0.5 scale-75 opacity-60">
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                                <div class="w-0.5 h-0.5 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium {{ app()->getLocale() === 'en' ? 'text-cyan-600 dark:text-cyan-400' : 'text-gray-700 dark:text-gray-300' }}">English</span>
                                </a>
                                
                                <!-- Bandera España -->
                                <a hreflang="es" 
                                   href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" 
                                   class="flex-1 flex flex-col items-center p-3 rounded-lg border transition-all duration-200 {{ app()->getLocale() === 'es' ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20' : 'border-gray-200 dark:border-slate-700 hover:border-cyan-300 dark:hover:border-cyan-600 hover:bg-gray-50 dark:hover:bg-slate-800' }}"
                                   @click="mobileMenuOpen = false">
                                    <div class="w-8 h-6 mb-2 rounded overflow-hidden shadow-sm">
                                        <div class="h-2 bg-red-600"></div>
                                        <div class="h-2 bg-yellow-400"></div>
                                        <div class="h-2 bg-red-600"></div>
                                    </div>
                                    <span class="text-sm font-medium {{ app()->getLocale() === 'es' ? 'text-cyan-600 dark:text-cyan-400' : 'text-gray-700 dark:text-gray-300' }}">Español</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <!-- Separador -->
                    <div class="border-t border-gray-100 dark:border-slate-800 pt-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.site_name') }} &copy; {{ date('Y') }}
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Header Wrapper -->

<main id="main-content" role="main" tabindex="-1" class="flex-grow w-full max-w-7xl mx-auto px-4 lg:px-6 pt-24 pb-12 lg:pt-28 lg:pb-24 focus:outline-none">
        @if(isset($sidebar) && !empty(trim((string) $sidebar)))
            <div class="grid grid-cols-1 gap-6 lg:gap-[30px] items-start lg:grid-cols-[minmax(0,1fr)_300px] xl:grid-cols-[minmax(0,1fr)_320px]">
                <!-- Left Column (Primary) -->
                <div class="min-w-0">
                    {{ $slot }}
                </div>
                
                <!-- Right Column (Sidebar) -->
                <aside class="w-full lg:shrink-0 sticky top-24">
                    <div class="flex flex-col gap-6 lg:gap-8">
                        {{ $sidebar }}
                    </div>
                </aside>
            </div>
        @else
            <!-- Single Column (Full Width & Centered) for Error Pages, Legal Pages, etc. -->
            <div class="w-full min-w-0">
                {{ $slot }}
            </div>
        @endif
    </main>

    <!-- Rich Professional Footer with AI Transparency & Legal Compliance -->
    <footer role="contentinfo" class="bg-white dark:bg-slate-950 border-t border-gray-200 dark:border-white/5 transition-colors" style="padding-top: 50px; padding-bottom: 20px;">
        <style>
            .footer-grid-3cols {
                display: grid !important;
                grid-template-columns: 1.5fr 1fr 1fr !important;
                gap: 3rem !important;
            }
            @media (max-width: 860px) {
                .footer-grid-3cols {
                    grid-template-columns: 1fr !important;
                    gap: 2.5rem !important;
                    text-align: center !important;
                }
                .footer-col {
                    align-items: center !important;
                    text-align: center !important;
                    padding-left: 0 !important;
                }
                .footer-links {
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                }
                .footer-links a {
                    justify-content: center !important;
                }
            }
        </style>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="footer-grid-3cols pb-12 border-b border-gray-100 dark:border-white/5">
                <!-- Columna 1: Marca & Declaración de Especialización -->
                <div class="footer-col flex flex-col gap-4">
                    <div class="mb-1 flex items-center justify-start max-[860px]:justify-center">
                        <x-ui.logo size="lg" />
                    </div>
                    <p class="text-sm md:text-[15px] text-slate-600 dark:text-slate-300 leading-relaxed font-normal max-w-md">
                        {{ __('ui.editorial_disclosure_footer') }}
                    </p>
                </div>

                <!-- Columna 2: Enlaces de Interés -->
                <div class="footer-col flex flex-col md:pl-6">
                    <h4 class="text-base font-black uppercase tracking-wider text-slate-900 dark:text-white mb-4">{{ __('ui.links_of_interest') }}</h4>
                    <ul class="footer-links flex flex-col gap-3 text-sm text-slate-600 dark:text-slate-300 font-medium">
                        <li><a href="{{ app()->getLocale() === 'es' ? url('/es/nosotros') : url('/about') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.about_us') }}</a></li>
                        <li><a href="{{ route('contact.show') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.contact') }}</a></li>
                        @auth
                            <li><a href="{{ auth()->user()->slug === 'admin' || auth()->user()->id === 1 ? '/admin' : '#' }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.auth_my_account') }}</a></li>
                        @else
                            <li><a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/login') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.auth_sign_in_button') }}</a></li>
                        @endauth
                        <li>
                            <a href="{{ route('sitemap') }}" target="_blank" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-flex items-center gap-1.5">
                                {{ __('ui.sitemap_xml') }}
                                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; opacity: 0.6; display: inline-block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sitemap.news') }}" target="_blank" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-flex items-center gap-1.5">
                                {{ __('ui.google_news_sitemap') }}
                                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; opacity: 0.6; display: inline-block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('feed') }}" target="_blank" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-flex items-center gap-1.5">
                                {{ __('ui.rss_feed') }}
                                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; opacity: 0.6; display: inline-block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 3: Legal & Transparencia -->
                <div class="footer-col flex flex-col md:pl-6">
                    <h4 class="text-base font-black uppercase tracking-wider text-slate-900 dark:text-white mb-4">{{ __('ui.legal_nav') }}</h4>
                    <ul class="footer-links flex flex-col gap-3 text-sm text-slate-600 dark:text-slate-300 font-medium">
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.privacy_policy') }}</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.terms_of_service') }}</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.cookie_policy') }}</a></li>
                        <li><button type="button" data-cc="show-preferencesModal" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block text-left">{{ __('ui.cookie_settings') }}</button></li>
                        <li><a href="{{ route('legal.editorial') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors inline-block">{{ __('ui.editorial_policy') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright (Centrado y Limpio) -->
            <div class="pt-8 text-center text-sm text-slate-500 dark:text-slate-400 font-medium">
                <p>&copy; {{ date('Y') }} {{ __('ui.site_name') }}. {{ app()->getLocale() === 'es' ? 'Todos los derechos reservados.' : 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Breaking News Banner (Bottom Right, Fixed) -->
    <div x-show="showBanner" 
         x-transition:enter="transition ease-out duration-500 transform"
         x-transition:enter-start="translate-y-20 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-end="translate-y-20 opacity-0"
         class="fixed bottom-8 right-8 z-[100] max-w-sm w-[calc(100%-4rem)] md:w-full">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 p-6 rounded-lg shadow-[0_20px_50px_rgba(0,0,0,0.3)] dark:shadow-[0_20px_50px_rgba(255,255,255,0.1)] border border-white/10 dark:border-gray-200 flex items-center gap-5 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 pointer-events-none"></div>
            <div class="w-12 h-12 shrink-0 bg-cyan-500 rounded-lg flex items-center justify-center shadow-lg shadow-cyan-500/30">
                <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="flex-1 min-w-0 relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-400 dark:text-cyan-600 mb-1">{{ __('ui.new_update') }}</p>
                <h4 x-text="latestTitle" class="text-sm font-bold truncate mb-3"></h4>
                <div class="flex items-center gap-4">
                    <button @click="window.location.reload()" class="text-[12px] font-black uppercase tracking-widest hover:text-cyan-500 transition-colors underline underline-offset-4 decoration-2">{{ __('ui.read_now') }}</button>
                    <button @click="showBanner = false" class="text-[12px] font-black uppercase tracking-widest opacity-40 hover:opacity-100 transition-opacity">{{ __('ui.dismiss') }}</button>
                </div>
            </div>
        </div>
    </div>

{{-- Custom Footer Codes (analytics, chat, scripts) --}}
{!! \App\Models\CustomCode::getActive('footer') !!}

    <!-- Global Notification Toast (Flash Messages) -->
    @if(session('success') || session('error') || session('info'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 7000)"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-y-10 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-end="-translate-y-10 opacity-0"
             class="fixed top-20 right-4 md:right-8 z-[200] max-w-md w-[calc(100%-2rem)] shadow-2xl rounded-2xl overflow-hidden border backdrop-blur-xl {{ session('error') ? 'bg-rose-950/90 border-rose-500/30 text-rose-100' : 'bg-slate-900/95 dark:bg-slate-900/95 border-cyan-500/30 text-white' }}">
            <div class="p-4 flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ session('error') ? 'bg-rose-500/20 text-rose-400' : 'bg-cyan-500/20 text-cyan-400' }}">
                    @if(session('error'))
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <p class="text-[13px] font-semibold leading-relaxed">
                        {{ session('success') ?? session('error') ?? session('info') }}
                    </p>
                </div>
                <button type="button" @click="show = false" class="text-slate-400 hover:text-white transition-colors p-1 -mr-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

                                                                <!-- Simple Standard Search Modal (Guaranteed 700px Max-Width & 48px Icon Spacing) -->
    <div x-data="{
            open: false,
            query: '',
            loading: false,
            articles: [],
            viewAllUrl: null,
            hasSearched: false,
            async search() {
                if (this.query.trim().length < 2) {
                    this.articles = [];
                    this.hasSearched = false;
                    this.loading = false;
                    return;
                }
                this.loading = true;
                try {
                    const res = await axios.get('/api/search', {
                        params: { q: this.query, locale: '{{ app()->getLocale() }}' }
                    });
                    this.articles = res.data.articles || [];
                    this.viewAllUrl = res.data.viewAllUrl;
                    this.hasSearched = true;
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },
            submitSearch() {
                if (this.query.trim().length > 0) {
                    const target = '{{ app()->getLocale() === 'es' ? '/es/search?q=' : '/search?q=' }}' + encodeURIComponent(this.query.trim());
                    window.location.href = target;
                }
            }
         }"
         @open-search-modal.window="open = true; setTimeout(() => $refs.modalSearchInput.focus(), 100)"
         @keydown.escape.window="open = false"
         x-show="open"
         x-cloak
         style="z-index: 999999 !important;"
         role="dialog" aria-modal="true" aria-label="{{ __('ui.search') }}" class="fixed inset-0 flex items-start justify-center pt-2 sm:pt-4 px-4 bg-slate-950/60 backdrop-blur-md transition-all">
         
        <!-- Backdrop -->
        <div class="fixed inset-0" @click="open = false"></div>

        <!-- Dialog Box strictly 700px max width -->
        <div style="max-width: 700px !important;"
             class="relative w-full max-w-[700px] bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-800 shadow-xl p-4 z-10">
            <form @submit.prevent="submitSearch()" class="relative">
                <input x-ref="modalSearchInput"
                       x-model="query"
                       @input.debounce.200ms="search()"
                       type="text"
                       placeholder="{{ app()->getLocale() === 'es' ? 'Buscar...' : 'Search...' }}"
                       style="padding-left: 48px !important; padding-right: 40px !important;"
                       class="w-full bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border border-gray-200 dark:border-slate-700 rounded-md pl-12 pr-10 py-2.5 text-sm outline-none focus:border-cyan-500 dark:focus:border-cyan-500 transition-colors">
                
                <!-- Left Icon strictly at 16px from left -->
                <div style="left: 16px !important;"
                     class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none">
                    <span x-show="loading" class="text-cyan-500 animate-spin">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    <svg x-show="!loading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Clear button -->
                <button x-show="query.length > 0" 
                        @click="query = ''; articles = []; hasSearched = false; $refs.modalSearchInput.focus()" 
                        type="button" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>

            <!-- Results -->
            <div x-show="articles.length > 0" class="mt-3 divide-y divide-gray-100 dark:divide-slate-800 max-h-60 overflow-y-auto">
                <template x-for="item in articles" :key="item.id">
                    <a :href="item.url" class="block py-2 text-sm text-gray-800 dark:text-gray-200 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">
                        <span x-text="item.title" class="line-clamp-1"></span>
                    </a>
                </template>
            </div>

            <!-- View All Link -->
            <div x-show="viewAllUrl && articles.length > 0" class="mt-2 pt-2 border-t border-gray-100 dark:border-slate-800 text-center">
                <a :href="viewAllUrl" class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">
                    {{ app()->getLocale() === 'es' ? 'Ver todos los resultados' : 'View all results' }}
                </a>
            </div>

            <!-- Empty State -->
            <div x-show="hasSearched && articles.length === 0 && !loading" class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400 py-3">
                {{ app()->getLocale() === 'es' ? 'No se encontraron resultados' : 'No results found' }}
            </div>
        </div>
    </div>
</body>
</html>
