<x-layouts.app>
    <x-slot:title>
        @if(isset($category))
            {{ $category->name }} | {{ config('app.name') }}
        @elseif(isset($tag))
            #{{ $tag->name }} | {{ config('app.name') }}
        @else
            Latest News | {{ config('app.name') }}
        @endif
    </x-slot>

    <!-- Page Header -->
    <div class="mb-10 pb-6 border-b border-gray-200 dark:border-gray-800">
        @if(isset($category))
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                    {{ $category->description }}
                </p>
            @endif
        @elseif(isset($tag))
            <h1 class="text-3xl font-bold tracking-tight text-cyan-500 sm:text-4xl">
                #{{ $tag->name }}
            </h1>
            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                Articles tagged with {{ $tag->name }}
            </p>
        @else
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                The Latest in AI & Tech
            </h1>
            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                Stay ahead of the curve with our 100% AI-curated and drafted insights.
            </p>
        @endif
    </div>

    <!-- Feed Grid -->
    @if($articles->isEmpty())
        <div class="text-center py-20 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No articles found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check back later for fresh content.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-10">
            @foreach($articles as $article)
                <article class="flex flex-col items-start justify-between bg-white dark:bg-[#111827] rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 group">
                    <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $article->slug_es : $article->slug_en) }}" class="block w-full overflow-hidden aspect-video relative">
                        <img src="{{ $article->image_url ?? '/placeholder.webp' }}" 
                             alt="{{ $article->image_alt }}" 
                             loading="{{ $loop->iteration <= 4 ? 'eager' : 'lazy' }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
                    </a>
                    <div class="p-6 sm:p-8 flex flex-col flex-1">
                        <div class="flex items-center gap-x-4 text-xs mb-4">
                            <time datetime="{{ $article->published_at?->toIso8601String() }}" class="text-gray-500 dark:text-gray-400">
                                {{ $article->published_at?->diffForHumans() }}
                            </time>
                            @if($article->category)
                                <a href="{{ route('categories.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? ($article->category->slug_es ?? $article->category->slug) : ($article->category->slug_en ?? $article->category->slug)) }}" class="relative z-10 rounded-full bg-gray-50 dark:bg-gray-800 px-3 py-1.5 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    {{ $article->category->name }}
                                </a>
                            @endif
                        </div>
                        <div class="group relative">
                            <h3 class="mt-3 text-xl font-bold tracking-tight text-gray-900 dark:text-white line-clamp-3 group-hover:text-cyan-500 transition-colors">
                                <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $article->slug_es : $article->slug_en) }}">
                                    <span class="absolute inset-0"></span>
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="mt-4 line-clamp-3 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                {{ $article->excerpt }}
                            </p>
                        </div>
                        <div class="relative mt-auto pt-6 flex items-center gap-x-4">
                            <img src="{{ $article->author?->avatar_url ?? 'https://ui-avatars.com/api/?name=AI&background=0284c7&color=fff' }}" alt="{{ $article->author?->name }}" class="h-10 w-10 min-w-10 rounded-full bg-gray-50 border border-gray-200 dark:border-gray-700 object-cover">
                            <div class="text-sm leading-6">
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $article->author?->name ?? 'AI Reporter' }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $article->reading_time ?? 5 }} min read</p>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif

    <x-slot:sidebar>
        <!-- Trending Tags Widget -->
        <div class="bg-white dark:bg-[#111827] rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
            <h3 class="text-sm font-bold tracking-wider uppercase text-gray-900 dark:text-white mb-6">Trending Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($trendingTags ?? [] as $ttag)
                    <a href="{{ route('tags.show', $ttag->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800/80 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-cyan-50 hover:text-cyan-600 dark:hover:bg-cyan-900/30 dark:hover:text-cyan-400 transition-all border border-transparent hover:border-cyan-100 dark:hover:border-cyan-800/50">
                        #{{ $ttag->name }}
                        <span class="ml-2 text-xs text-gray-400 dark:text-gray-500">{{ $ttag->article_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Newsletter Widget -->
        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl p-6 sm:p-8 shadow-lg shadow-cyan-500/20 text-white">
            <h3 class="text-xl font-bold mb-2">Join the Future</h3>
            <p class="text-cyan-50 text-sm mb-6">Get our top AI-curated tech news delivered to your inbox weekly.</p>
            <form class="flex flex-col gap-3">
                <input type="email" placeholder="Your email address" class="w-full rounded-xl border-0 bg-white/10 px-4 py-3 text-white placeholder-cyan-100 focus:bg-white/20 focus:ring-2 focus:ring-white transition-all">
                <button type="submit" class="w-full rounded-xl bg-white text-cyan-600 px-4 py-3 font-bold hover:bg-cyan-50 transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </x-slot>
</x-layouts.app>
