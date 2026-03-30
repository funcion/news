<x-layouts.app>
    <x-slot:title>
        {{ $article->meta_title ?? $article->title }} | {{ config('app.name') }}
    </x-slot>

    <x-slot:metaDescription>{{ $article->meta_description ?? $article->excerpt }}</x-slot>
    
    @if($article->meta_keywords)
        <x-slot:metaKeywords>{{ is_array($article->meta_keywords) ? implode(', ', $article->meta_keywords) : $article->meta_keywords }}</x-slot>
    @endif

    <x-slot:head>
        <!-- JSON-LD Structured Data -->
        @if(isset($article->ai_metadata['json_ld']))
            <script type="application/ld+json">
                {!! json_encode($article->ai_metadata['json_ld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
        @endif
        
        <!-- Open Graph -->
        <meta property="og:title" content="{{ $article->meta_title ?? $article->title }}" />
        <meta property="og:description" content="{{ $article->excerpt }}" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{{ url()->current() }}" />
        @if($article->image_url)
            <meta property="og:image" content="{{ $article->image_url }}" />
        @endif
    </x-slot>

    <!-- Article Content -->
    <article class="bg-white dark:bg-[#111827] rounded-3xl p-6 sm:p-10 lg:p-14 shadow-sm border border-gray-100 dark:border-gray-800">
        
        <div class="mb-10 text-center mx-auto max-w-3xl">
            @if($article->category)
                <a href="{{ route('categories.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? ($article->category->slug_es ?? $article->category->slug) : ($article->category->slug_en ?? $article->category->slug)) }}" class="inline-block rounded-full bg-cyan-50 dark:bg-cyan-900/30 px-4 py-1.5 font-semibold text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-800/40 transition-colors uppercase tracking-wider text-xs mb-6">
                    {{ $article->category->name }}
                </a>
            @endif

            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl leading-tight mb-6">
                {{ $article->title }}
            </h1>

            <p class="text-xl text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                {{ $article->excerpt }}
            </p>

            <div class="flex items-center justify-center gap-x-6 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-x-3">
                    <img src="{{ $article->author?->avatar_url ?? 'https://ui-avatars.com/api/?name=AI&background=0284c7&color=fff' }}" alt="{{ $article->author?->name }}" class="h-12 w-12 rounded-full ring-2 ring-gray-100 dark:ring-gray-800 object-cover">
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $article->author?->name ?? 'AI Reporter' }}</p>
                        <p>{{ $article->published_at?->format('M d, Y') }} &middot; {{ $article->reading_time ?? 5 }} min read</p>
                    </div>
                </div>
            </div>
        </div>

        @if($article->image_url)
            <figure class="mb-12 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                <img src="{{ $article->image_url }}" alt="{{ $article->image_alt ?? $article->title }}" class="w-full object-cover">
            </figure>
        @endif

        <div class="prose prose-lg prose-cyan dark:prose-invert mx-auto max-w-4xl prose-headings:font-bold prose-img:rounded-xl prose-a:text-cyan-500 hover:prose-a:text-cyan-600 dark:hover:prose-a:text-cyan-400">
            {!! $article->content !!}
        </div>

        <!-- Article Tags -->
        @if($article->tags && $article->tags->count() > 0)
            <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800 max-w-4xl mx-auto flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gray-900 dark:text-white mr-2">Tags:</span>
                @foreach($article->tags as $atag)
                    <a href="{{ route('tags.show', $atag->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-cyan-50 hover:text-cyan-600 dark:hover:bg-cyan-900/30 dark:hover:text-cyan-400 transition-colors">
                        #{{ $atag->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </article>

    <x-slot:sidebar>
        <!-- Author Widget -->
        <div class="bg-white dark:bg-[#111827] rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-gray-800 shadow-sm text-center">
            <img src="{{ $article->author?->avatar_url ?? 'https://ui-avatars.com/api/?name=AI&background=0284c7&color=fff' }}" alt="{{ $article->author?->name }}" class="h-24 w-24 mx-auto rounded-full ring-4 ring-cyan-50 dark:ring-cyan-900/20 mb-4 object-cover">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $article->author?->name ?? 'AI Reporter' }}</h3>
            <p class="text-sm text-cyan-500 mb-4 font-medium">{{ $article->author?->voice_style ?? 'Tech Journalist' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                {{ $article->author?->bio ?? 'Analyzing technology trends through an autonomous lens.' }}
            </p>
        </div>

        @if($relatedArticles->count() > 0)
            <!-- Related Articles -->
            <div class="bg-white dark:bg-[#111827] rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <h3 class="text-sm font-bold tracking-wider uppercase text-gray-900 dark:text-white mb-6">Related News</h3>
                <div class="flex flex-col gap-6">
                    @foreach($relatedArticles as $related)
                        <div class="group flex gap-4 items-start">
                            <div class="w-20 h-20 shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                <img src="{{ $related->image_url ?? '/placeholder.webp' }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-cyan-500 transition-colors">
                                    <a href="{{ route('articles.show', \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() === 'es' ? $related->slug_es : $related->slug_en) }}">
                                        {{ $related->title }}
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $related->published_at?->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-slot>
</x-layouts.app>
