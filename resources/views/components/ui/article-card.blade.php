{{-- resources/views/components/ui/article-card.blade.php --}}
@props([
    'article',
    'showExcerpt' => true,
    'titleMaxWords' => null,
    'excerptLength' => null,
    'imageAspectRatio' => '16/9',
    'config' => []
])

@php
    $titleMaxWords = $titleMaxWords ?? (int) config('global.editorial.limits.card.title_max_words', 15);
    $excerptLength = $excerptLength ?? (int) config('global.editorial.limits.card.excerpt_max', 160);

    $config = array_merge([
        'titleMaxWords' => $titleMaxWords,
        'showExcerpt' => $showExcerpt,
        'excerptLength' => $excerptLength,
        'imageAspectRatio' => $imageAspectRatio,
    ], $config);
    
    $titleId = 'article-title-' . $article->id;
    $rawTitle = $article->title;

    // En los listados truncar después de 15 palabras con '...'
    $cardTitle = \Illuminate\Support\Str::words($rawTitle, $config['titleMaxWords'], '...');
    
    $truncatedExcerpt = $config['showExcerpt'] && $article->excerpt
        ? (mb_strlen($article->excerpt) > $config['excerptLength'] 
            ? \Illuminate\Support\Str::limit($article->excerpt, $config['excerptLength'], '...')
            : $article->excerpt)
        : null;
@endphp

<article 
    class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-4 flex flex-col gap-3 relative overflow-hidden group hover:border-cyan-500 dark:hover:border-cyan-400 focus-within:outline-2 focus-within:outline-cyan-500 focus-within:outline-offset-2"
    aria-labelledby="{{ $titleId }}"
    x-data="{ isLoaded: false }"
>
    <div class="relative w-full pb-[56.25%] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
        <img
            src="{{ $article->image ?? $article->image_url ?? '/placeholder.webp' }}"
            alt="{{ $article->alt_text ?? $article->image_alt ?? $rawTitle }}"
            loading="lazy"
            @load="isLoaded = true"
            :class="{ 'opacity-100': isLoaded }"
            class="absolute top-0 left-0 w-full h-full object-cover opacity-0 transition-opacity duration-300 focus:outline-2 focus:outline-cyan-500 focus:outline-offset-2"
        />
    </div>
    
    <div class="flex flex-col gap-2 flex-1">
        <h3 
            id="{{ $titleId }}"
            class="text-lg font-semibold leading-tight text-gray-900 dark:text-gray-100 m-0 group-hover:text-cyan-500 transition-colors"
        >
            {{ $cardTitle }}
        </h3>
        
        @if($truncatedExcerpt)
            <p class="text-base leading-normal text-gray-600 dark:text-gray-400 m-0 line-clamp-3">
                {{ $truncatedExcerpt }}
            </p>
        @endif
        
        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
            <time datetime="{{ $article->published_at ? $article->published_at->toIso8601String() : '' }}">
                {{ $article->published_at ? $article->published_at->format('d/m/Y') : '' }}
            </time>
            @if($article->category)
                <span class="bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 px-2 py-0.5 rounded-full font-medium uppercase text-[0.75rem] tracking-wider">
                    {{ $article->category->name }}
                </span>
            @endif
        </div>
    </div>
</article>
