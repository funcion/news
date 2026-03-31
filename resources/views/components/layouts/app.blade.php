<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Noticias Platform') }}</title>
    
    @if(isset($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    
    @if(isset($metaKeywords))
        <meta name="keywords" content="{{ is_array($metaKeywords) ? implode(', ', $metaKeywords) : $metaKeywords }}">
    @endif

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Reverb Config -->
    <script>
        window.laravelConfig = {
            reverb: {
                appKey: '{{ config('reverb.apps.0.key') ?? env('REVERB_APP_KEY') }}',
                host: '{{ env('VITE_REVERB_HOST', 'localhost') }}',
                port: {{ env('VITE_REVERB_PORT', 8080) }},
                scheme: '{{ env('VITE_REVERB_SCHEME', 'http') }}'
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
    
    <!-- Premium Header Wrapper -->
    <div class="sticky top-0 z-50 w-full">
        <!-- Header Bar -->
        <header class="w-full backdrop-blur-md transition-all duration-300 border-b border-gray-100 dark:border-white/5 bg-white/80 dark:bg-slate-950/80"
                x-bind:class="isScrolled ? 'shadow-sm' : ''">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 group" aria-label="Home page">
                    <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center transform group-hover:rotate-6 transition-transform shadow-lg shadow-cyan-500/20">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                           <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col -gap-1">
                        <span class="font-black text-xl tracking-tighter text-slate-950 dark:text-white uppercase leading-none">Tech AI</span>
                        <span class="text-[9px] font-bold text-cyan-500 uppercase tracking-[0.2em] ml-0.5">Magazine</span>
                    </div>
                </a>
                
                <!-- Right Side: Navigation & Actions -->
                <div class="flex items-center gap-4 lg:gap-8">
                    <!-- Desktop Navigation (hidden on mobile) -->
                    <nav class="hidden lg:flex items-center gap-8">
                        <!-- Home Link -->
                        <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors {{ request()->is('/') ? 'text-cyan-500 dark:text-cyan-400' : '' }}">
                            {{ __('Home') }}
                        </a>
                        
                        <!-- Categories Dropdown -->
                        <div class="relative group nav-item" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="flex items-center gap-1 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-2">
                                {{ __('Categories') }}
                                <svg class="w-4 h-4 transition-transform duration-200 dropdown-arrow" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full right-0 mt-2 w-64 bg-white dark:bg-slate-900 rounded-lg shadow-xl categories-dropdown border border-gray-200 dark:border-slate-800 py-2 z-50"
                                 @click.away="open = false">
                                @php
                                    $categories = \App\Models\Category::whereNull('parent_id')->get();
                                @endphp
                                
                                @foreach($categories as $category)
                                    <a href="{{ $category->url }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Latest News -->
                        <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors">
                            {{ __('Latest News') }}
                        </a>
                        
                        <div class="h-6 w-px bg-gray-200 dark:bg-white/10 mx-2"></div>
                        
                        <!-- Lang Switcher (Desktop) -->
                        <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                            <a hreflang="en" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" class="transition-colors hover:text-cyan-500 {{ app()->getLocale() === 'en' ? 'text-cyan-500 underline underline-offset-4 decoration-2' : '' }}">EN</a>
                            <div class="w-1 h-1 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                            <a hreflang="es" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" class="transition-colors hover:text-cyan-500 {{ app()->getLocale() === 'es' ? 'text-cyan-500 underline underline-offset-4 decoration-2' : '' }}">ES</a>
                        </div>
                    </nav>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 sm:gap-4 border-l border-gray-100 dark:border-white/5 pl-4 sm:pl-8">
                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode()" 
                                class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                            <svg x-show="!isDarkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg x-show="isDarkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </button>

                        <!-- Hamburger Button (Standard SVG for reliability) -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="lg:hidden p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
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
             class="lg:hidden overflow-hidden bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800">
        
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
                            {{ __('Home') }}
                        </a>
                        
                        <!-- Categories Accordion -->
                        <div x-data="{ categoriesOpen: false }" class="border-t border-gray-100 dark:border-slate-800 pt-3">
                            <button @click="categoriesOpen = !categoriesOpen" 
                                    class="flex items-center justify-between w-full text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    {{ __('Categories') }}
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': categoriesOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="categoriesOpen" class="mt-2 pl-6 space-y-1">
                                @php
                                    $mobileCategories = \App\Models\Category::whereNull('parent_id')->get();
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
                            {{ __('Latest News') }}
                        </a>
                        
                        <a href="#search" 
                           class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1"
                           @click="mobileMenuOpen = false">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {{ __('Search') }}
                        </a>
                        
                        <a href="#about" 
                           class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-cyan-500 dark:hover:text-cyan-400 transition-colors py-1"
                           @click="mobileMenuOpen = false">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('About') }}
                        </a>
                    </div>
                    
                    <!-- Separador -->
                    <div class="border-t border-gray-100 dark:border-slate-800 pt-4">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">{{ __('Preferences') }}</h3>
                        
                        <!-- Dark/Light Mode Toggle -->
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Theme') }}</span>
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
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Language') }}</span>
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
                    <div class="border-t border-gray-100 dark:border-slate-800 pt-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Tech AI Magazine') }} &copy; {{ date('Y') }}
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">
                                {{ __('100% AI-Generated News Platform') }}
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Header Wrapper -->

<main class="flex-grow w-full max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-8">
        <div class="grid grid-cols-1 gap-[30px] items-start lg:grid-cols-[minmax(0,1fr)_300px] xl:grid-cols-[minmax(0,1fr)_320px]">
            <!-- Left Column (Primary) -->
            <div class="min-w-0">
                {{ $slot }}
            </div>
            
            <!-- Right Column (Sidebar) -->
            <aside class="w-full lg:shrink-0 sticky top-24">
                <div class="flex flex-col gap-8">
                    {{ $sidebar ?? '' }}
                </div>
            </aside>
        </div>
    </main>

    <!-- Minimalist Footer -->
    <footer class="bg-white dark:bg-slate-950 border-t border-gray-100 dark:border-white/5 py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex flex-col items-center gap-6">
                <div class="flex items-center gap-2 opacity-40 grayscale pointer-events-none">
                    <span class="font-black text-xl tracking-tighter text-slate-900 dark:text-white uppercase leading-none">Tech AI</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-[0.4em] max-w-xs leading-relaxed">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. 100% AI-Generated News Platform.
                </p>
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
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-400 dark:text-cyan-600 mb-1">New Update</p>
                <h4 x-text="latestTitle" class="text-sm font-bold truncate mb-3"></h4>
                <div class="flex items-center gap-4">
                    <button @click="window.location.reload()" class="text-[10px] font-black uppercase tracking-widest hover:text-cyan-500 transition-colors underline underline-offset-4 decoration-2">Read Now</button>
                    <button @click="showBanner = false" class="text-[10px] font-black uppercase tracking-widest opacity-40 hover:opacity-100 transition-opacity">Dismiss</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
