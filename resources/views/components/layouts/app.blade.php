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

    {{ $hreflang ?? '' }}

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Preconnect to Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{ $head ?? '' }}
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-[#0B1120] dark:text-gray-100 flex flex-col min-h-screen relative overflow-x-hidden">
    
    <!-- Alpine App State -->
    <div x-data="{
        newArticles: 0,
        latestTitle: '',
        init() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel('public-news')
                    .listen('ArticlePublished', (e) => {
                        this.newArticles++;
                        this.latestTitle = e.title_{{ app()->getLocale() }};
                    });
            }
        }
    }">
        
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full backdrop-blur flex-none transition-colors duration-500 lg:z-50 lg:border-b lg:border-gray-900/10 dark:border-gray-50/[0.06] bg-white/95 dark:bg-[#0B1120]/95 supports-backdrop-blur:bg-white/60">
        <div class="max-w-8xl mx-auto">
            <div class="py-4 border-b border-slate-900/10 lg:px-8 lg:border-0 dark:border-slate-300/10 px-4">
                <div class="relative flex items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Home page">
                        <svg class="w-8 h-8 text-cyan-500" viewBox="0 0 24 24" fill="currentColor">
                           <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                        <span class="font-bold text-xl tracking-tight text-slate-900 dark:text-white">Tech AI News</span>
                    </a>
                    
                    <div class="flex items-center gap-6">
                        <!-- Lang Switcher -->
                        <div class="flex gap-2 text-sm font-medium text-slate-600 dark:text-slate-400">
                            <a hreflang="en" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" class="hover:text-cyan-500 {{ app()->getLocale() === 'en' ? 'text-cyan-500 font-bold' : '' }}">EN</a>
                            <span class="border-r border-slate-300 dark:border-slate-700"></span>
                            <a hreflang="es" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" class="hover:text-cyan-500 {{ app()->getLocale() === 'es' ? 'text-cyan-500 font-bold' : '' }}">ES</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Realtime New Articles Pill -->
    <div x-show="newArticles > 0" x-transition.opacity class="fixed top-24 left-1/2 -translate-x-1/2 z-40">
        <button @click="window.location.reload()" class="flex items-center gap-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-5 py-2.5 rounded-full shadow-lg shadow-cyan-500/20 hover:scale-105 transition-transform font-medium text-sm border border-cyan-400/30">
            <span class="animate-pulse w-2.5 h-2.5 bg-white rounded-full"></span>
            <span x-text="newArticles + ' {{ app()->getLocale() === 'es' ? 'nuevo(s) artículo(s)' : 'new article(s)' }}'"></span>
            <span>&middot;</span>
            <span x-text="latestTitle" class="truncate max-w-[200px] opacity-90 font-normal"></span>
        </button>
    </div>
    
    </div> <!-- Close Alpine Data Container -->

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- 70% Primary Content -->
            <div class="w-full lg:w-[70%] lg:shrink-0">
                {{ $slot }}
            </div>
            
            <!-- 30% Sidebar -->
            <aside class="w-full lg:w-[30%]">
                <div class="sticky top-28 flex flex-col gap-8">
                    {{ $sidebar ?? '' }}
                </div>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-500 dark:text-slate-400 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. 100% AI-Generated Tech News.
            </p>
        </div>
    </footer>

</body>
</html>
