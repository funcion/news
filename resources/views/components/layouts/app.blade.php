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
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-[#0B1120] dark:text-gray-100 flex flex-col min-h-screen relative overflow-x-hidden"
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
    
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full backdrop-blur flex-none transition-colors duration-500 lg:border-b lg:border-gray-900/10 dark:border-gray-50/[0.06] bg-white/90 dark:bg-[#0B1120]/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group" aria-label="Home page">
                    <svg class="w-8 h-8 text-cyan-500 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                       <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
                    </svg>
                    <span class="font-black text-xl tracking-tighter text-slate-900 dark:text-white uppercase italic">Tech News <span class="text-cyan-500">AI</span></span>
                </a>
                
                <div class="flex items-center gap-6">
                    <!-- Lang Switcher -->
                    <div class="flex gap-3 text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        <a hreflang="en" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en') }}" class="hover:text-cyan-500 {{ app()->getLocale() === 'en' ? 'text-cyan-500 underline underline-offset-4 decoration-2' : '' }}">EN</a>
                        <span class="opacity-20">|</span>
                        <a hreflang="es" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('es') }}" class="hover:text-cyan-500 {{ app()->getLocale() === 'es' ? 'text-cyan-500 underline underline-offset-4 decoration-2' : '' }}">ES</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- 70% Primary Content -->
            <div class="w-full lg:w-[70%] lg:shrink-0 overflow-hidden">
                {{ $slot }}
            </div>
            
            <!-- 30% Sidebar -->
            <aside class="w-full lg:w-[30%] shrink-0">
                <div class="sticky top-28 flex flex-col gap-10">
                    {{ $sidebar ?? '' }}
                </div>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-[0.2em] opacity-60">
                &copy; {{ date('Y') }} {{ config('app.name') }}. 100% AI-Generated Tech News.
            </p>
        </div>
    </footer>

    <!-- Global Broadcast Notification (Fixed at Bottom Right) -->
    <div x-show="showBanner" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         class="fixed bottom-8 right-8 z-[100] max-w-sm w-full">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 p-5 rounded-[2rem] shadow-2xl border border-white/10 dark:border-gray-200 flex items-center gap-4 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 pointer-events-none"></div>
            <div class="w-12 h-12 shrink-0 bg-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-500/20">
                <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-cyan-500 mb-1">Breaking News</p>
                <h4 x-text="latestTitle" class="text-sm font-bold truncate pr-6"></h4>
                <div class="mt-2 flex items-center gap-3">
                    <button @click="window.location.reload()" class="text-[10px] font-black uppercase tracking-widest hover:underline">Refresh Now</button>
                    <button @click="showBanner = false" class="text-[10px] font-black uppercase tracking-widest opacity-50">Dismiss</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
