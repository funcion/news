<x-layouts.app>
    <!-- Real-time Notifier -->
    <div x-data="{ 
            newArticle: null, 
            showBanner: false,
            init() {
                if (window.Echo) {
                    window.Echo.channel('public-news')
                        .listen('ArticlePublished', (e) => {
                            console.log('New article broadcast received:', e);
                            this.newArticle = e;
                            this.showBanner = true;
                            // Auto-hide after 30 seconds
                            setTimeout(() => this.showBanner = false, 30000);
                        });
                }
            }
         }"
         x-show="showBanner"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 -translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-full"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4 pointer-events-none">
        <div class="pointer-events-auto bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border border-cyan-500/30 rounded-lg shadow-2xl shadow-cyan-500/20 p-4 flex items-center gap-4 overflow-hidden">
            <template x-if="newArticle">
                 <div class="flex items-center gap-4 w-full">
                    <img :src="newArticle.image_url" class="h-14 w-14 rounded-lg object-cover shadow-sm bg-gray-100 dark:bg-gray-800">
                    <div class="flex-1 min-w-0">
                        <span class="inline-flex items-center rounded-lg bg-cyan-100 dark:bg-cyan-900/40 px-2 py-1 text-xs font-bold text-cyan-700 dark:text-cyan-300 ring-1 ring-inset ring-cyan-700/10 mb-1">
                            JUST PUBLISHED
                        </span>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate" 
                            x-text="{{ app()->getLocale() === 'es' ? 'newArticle.title_es' : 'newArticle.title_en' }}">
                        </h4>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="newArticle.published_at"></p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button @click="window.location.reload()" class="p-2 rounded-lg bg-cyan-500 text-white hover:bg-cyan-600 transition-colors shadow-lg shadow-cyan-500/30">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                        <button @click="showBanner = false" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                 </div>
            </template>
        </div>
    </div>
    <x-slot:title>
        @if(isset($category))
            {{ $category->name }} | {{ config('app.name') }}
        @elseif(isset($tag))
            #{{ $tag->name }} | {{ config('app.name') }}
        @else
            Latest News | {{ config('app.name') }}
        @endif
    </x-slot>

    <!-- Page Header (Magazine Style) -->
    <div class="mb-14 pb-8 border-b border-gray-100 dark:border-white/5 relative">
        <div class="absolute -left-10 top-0 bottom-8 w-1 bg-cyan-500 rounded-lg opacity-0 lg:opacity-100"></div>
        @if(isset($category))
            <p class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.4em] mb-4">Browsing Category</p>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.1]">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
        @elseif(isset($tag))
            <p class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.4em] mb-4">Topic</p>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.1]">
                #{{ $tag->name }}
            </h1>
        @else
            <p class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.4em] mb-4">The Editorial</p>
            <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-slate-900 dark:text-white leading-[1.1]">
                The Future of AI <br class="hidden md:block"> & Technology.
            </h1>
            <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed font-medium">
                Deep dives and real-time insights into the world of artificial intelligence, curated for the modern professional.
            </p>
        @endif
    </div>

    <!-- Feed Grid with Featured Item -->
    @if($articles->isEmpty())
        <div class="text-center py-16 bg-gray-50 dark:bg-white/[0.02] rounded-lg border border-dashed border-gray-200 dark:border-white/10">
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-400">Archives are empty</h3>
            <p class="mt-2 text-xs text-slate-500">Expect new insights very soon.</p>
        </div>
    @else
        <!-- Featured Hero (First Article) -->
        @php $featured = $articles->first(); @endphp
        <article class="relative group mb-10 overflow-hidden rounded-lg bg-white dark:bg-slate-900/40 border border-gray-100 dark:border-white/5 transition-all duration-500">
            <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $featured->slug_es : $featured->slug_en) }}" class="flex flex-col lg:flex-row min-h-[320px]">
                <div class="lg:w-1/2 relative overflow-hidden">
                    <img src="{{ $featured->image_url ?? '/placeholder.webp' }}" 
                         alt="{{ $featured->image_alt }}" 
                         class="w-full h-full object-cover">
                </div>
                <div class="lg:w-1/2 p-6 md:p-8 flex flex-col justify-center">
                    <span class="inline-block px-2.5 py-1 bg-cyan-500 text-[9px] font-black text-white rounded-lg uppercase tracking-widest mb-4 w-fit">Featured</span>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white leading-tight mb-4 tracking-tighter group-hover:text-cyan-500 transition-colors">
                        {{ $featured->title }}
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed mb-6 line-clamp-2">
                        {{ $featured->excerpt }}
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $featured->author?->name ?? 'Staff' }}</span>
                    </div>
                </div>
            </a>
        </article>

        <!-- Dynamic Grid (Compact) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($articles->skip(1) as $article)
                <article class="flex flex-col group">
                    <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $article->slug_es : $article->slug_en) }}" 
                       class="block overflow-hidden rounded-lg aspect-[16/9] bg-gray-100 dark:bg-slate-900 border border-gray-100 dark:border-white/5 mb-4 group-hover:border-cyan-500/30 transition-all">
                        <img src="{{ $article->image_url ?? '/placeholder.webp' }}" 
                             alt="{{ $article->image_alt }}" 
                             class="w-full h-full object-cover">
                    </a>
                    
                    <div class="flex items-center gap-3 text-[9px] font-black uppercase text-slate-400 mb-3">
                        <span class="text-cyan-500">{{ $article->category?->name }}</span>
                        <div class="w-1 h-1 rounded-lg bg-slate-200 dark:bg-slate-800"></div>
                        <span>{{ $article->published_at?->diffForHumans() }}</span>
                    </div>

                    <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight mb-3 tracking-tighter group-hover:text-cyan-500 transition-colors">
                        <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $article->slug_es : $article->slug_en) }}">
                            {{ $article->title }}
                        </a>
                    </h3>
                    
                    <p class="text-slate-500 dark:text-slate-400 text-[12px] leading-relaxed line-clamp-2 mb-4">
                        {{ $article->excerpt }}
                    </p>

                    <div class="mt-auto flex items-center gap-2 pt-4 border-t border-gray-50 dark:border-white/5">
                         <span class="text-[9px] font-bold uppercase text-slate-400">{{ $article->author?->name ?? 'Reporter' }}</span>
                         <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase ml-auto">{{ $article->reading_time ?? 5 }} min</span>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Elegant Pagination -->
        <div class="mt-20 border-t border-gray-100 dark:border-white/5 pt-8">
            {{ $articles->links() }}
        </div>
    @endif

    <x-slot:sidebar>
        <!-- Trending Widget (More Compact) -->
        <div class="relative">
            <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-8 flex items-center gap-3">
                <span class="w-1.5 h-1.5 bg-cyan-500 rounded-lg"></span>
                Trending Topics
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($trendingTags ?? [] as $ttag)
                    <a href="{{ route('tags.show', $ttag->slug) }}" class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-100 dark:border-white/5 rounded-lg text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest hover:border-cyan-500 hover:text-cyan-500 transition-all shadow-sm shadow-slate-200/50 dark:shadow-none">
                        #{{ $ttag->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Newsletter (Premium) -->
        <div class="p-8 rounded-lg bg-slate-900 text-white relative overflow-hidden group border border-white/5">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-cyan-500/10 rounded-lg blur-3xl group-hover:bg-cyan-500/20 transition-all"></div>
            <h3 class="text-2xl font-black tracking-tighter mb-4 relative z-10">AI Insights Weekly</h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8 relative z-10">Get the most important tech updates directly to your inbox. No fluff, just value.</p>
            <form class="relative z-10 flex flex-col gap-4">
                <input type="email" placeholder="Email address" class="w-full bg-white/5 border border-white/10 rounded-lg px-5 py-4 text-sm focus:bg-white/10 focus:ring-1 focus:ring-cyan-500 outline-none transition-all placeholder:text-slate-600">
                <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-600 text-[10px] font-black uppercase tracking-widest py-4 rounded-lg transition-all shadow-lg shadow-cyan-500/20">Subscribe Now</button>
            </form>
        </div>
    </x-slot>
</x-layouts.app>
