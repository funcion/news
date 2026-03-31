{{-- resources/views/components/ui/article-card.blade.php --}}
@props([
    'article',
    'maxTitleLength' => 100,
    'showExcerpt' => true,
    'excerptLength' => 150,
    'imageAspectRatio' => '16/9',
    'config' => []
])

@php
    // Configuración dinámica desde props o contexto
    $config = array_merge([
        'maxTitleLength' => $maxTitleLength,
        'showExcerpt' => $showExcerpt,
        'excerptLength' => $excerptLength,
        'imageAspectRatio' => $imageAspectRatio,
    ], $config);
    
    $titleId = 'article-title-' . $article->id;
    $truncatedTitle = strlen($article->title) > $config['maxTitleLength'] 
        ? substr($article->title, 0, $config['maxTitleLength']) . '...'
        : $article->title;
    
    $truncatedExcerpt = $config['showExcerpt'] && $article->excerpt
        ? (strlen($article->excerpt) > $config['excerptLength'] 
            ? substr($article->excerpt, 0, $config['excerptLength']) . '...'
            : $article->excerpt)
        : null;
@endphp

<article 
    class="article-card"
    aria-labelledby="{{ $titleId }}"
    style="--aspect-ratio: {{ $config['imageAspectRatio'] }}"
    x-data="{ isLoaded: false }"
>
    <div class="article-card__image-container">
        <img
            src="{{ $article->image }}"
            alt="{{ $article->alt_text ?? 'Imagen de ' . $article->title }}"
            loading="lazy"
            @load="isLoaded = true"
            :class="{ 'article-card__image--loaded': isLoaded }"
            class="article-card__image"
        />
    </div>
    
    <div class="article-card__content">
        <h3 
            id="{{ $titleId }}"
            class="article-card__title"
        >
            {{ $truncatedTitle }}
        </h3>
        
        @if($truncatedExcerpt)
            <p class="article-card__excerpt">
                {{ $truncatedExcerpt }}
            </p>
        @endif
        
        <div class="article-card__meta">
            <time datetime="{{ $article->published_at->toIso8601String() }}">
                {{ $article->published_at->format('d/m/Y') }}
            </time>
            <span class="article-card__category">
                {{ $article->category->name }}
            </span>
        </div>
    </div>
</article>