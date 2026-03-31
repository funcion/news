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
                host: '{{ env('VITE_REVERB_HOST') ?? (parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost') }}',
                port: {{ env('VITE_REVERB_PORT', 8080) }},
                scheme: '{{ env('VITE_REVERB_SCHEME', 'http') }}'
            }
        };
    </script>

    {{ $head ?? '' }}
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-[#020617] dark:text-slate-100 flex flex-col min-h-screen relative overflow-x-hidden"
    x-data="{
        newArticles: 0,
        latestTitle: '',
        showBanner: false,
        newArticleData: null,
        init() {
            const pollEcho = setInterval(() => {
                if (typeof window.Echo !== 'undefined') {
                    clearInterval(pollEcho);
                    window.Echo.channel('public-news')
                        .listen('ArticlePublished', (e) => {
                            console.log('Live broadcast received:', e);
                            this.newArticles++;
                            this.newArticleData = e;
                            this.latestTitle = {{ app()->getLocale() === 'es' ? 'e.title_es' : 'e.title_en' }};
                            this.showBanner = true;
                        });
                }
            }, 1000);
        }
    }">
    
    <!-- Premium Header -->
    <header class="sticky top-0 z-50 w-full backdrop-blur-md transition-all duration-300 border-b border-gray-100 dark:border-white/5 bg-white/80 dark:bg-slate-950/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4 flex items-center justify-between">
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
                
                <div class="flex items-center gap-6">
                    <!-- Lang Switcher -->
                    <div class="flex items-center gap-3 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                        <a hreflang="en" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" class="transition-colors hover:text-cyan-500 {{ app()->getLocale() === 'en' ? 'text-cyan-500' : '' }}">English</a>
                        <div class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-800"></div>
                        <a hreflang="es" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" class="transition-colors hover:text-cyan-500 {{ app()->getLocale() === 'es' ? 'text-cyan-500' : '' }}">Español</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Indestructible Magazine Layout Styles -->
    <style>
        .magazine-grid { display: grid; grid-template-columns: 1fr; gap: 30px; align-items: start; }
        @media (min-width: 960px) {
            .magazine-grid { grid-template-columns: minmax(0, 1fr) 300px; }
        }
        @media (min-width: 1280px) {
            .magazine-grid { grid-template-columns: minmax(0, 1fr) 320px; }
        }
    </style>

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 lg:px-6 py-6 lg:py-8">
        <div class="magazine-grid">
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
                <div class="flex items-center gap-2 opacity-40grayscale pointer-events-none">
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
         class="fixed bottom-8 right-8 z-[100] max-w-sm w-[calc(100%-4rem)] md:w-full"
         style="display: none;">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 p-6 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] dark:shadow-[0_20px_50px_rgba(255,255,255,0.1)] border border-white/10 dark:border-gray-200 flex items-center gap-5 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 pointer-events-none"></div>
            <div class="w-12 h-12 shrink-0 bg-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
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
