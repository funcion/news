@props([
    'size' => 'md',
    'class' => '',
])

@php
    $dim = match($size) {
        'sm' => 28,
        'lg' => 38,
        default => 34,
    };
    $iconDim = match($size) {
        'sm' => 16,
        'lg' => 22,
        default => 20,
    };
    $textSize = match($size) {
        'sm' => 'text-lg',
        'lg' => 'text-2xl',
        default => 'text-xl',
    };
@endphp

<a href="{{ url('/') }}" class="inline-flex items-center group {{ $class }}" aria-label="{{ __('ui.site_name') }}">
    <!-- Perfect Square Icon Box with explicit aspect-ratio 1:1 and margin-right -->
    <div class="bg-cyan-500 rounded-lg flex items-center justify-center transform group-hover:rotate-6 transition-transform shadow-lg shadow-cyan-500/20 shrink-0" 
         style="width: {{ $dim }}px; height: {{ $dim }}px; min-width: {{ $dim }}px; min-height: {{ $dim }}px; max-width: {{ $dim }}px; max-height: {{ $dim }}px; aspect-ratio: 1 / 1; margin-right: 0.75rem;">
        <svg width="{{ $iconDim }}" height="{{ $iconDim }}" style="width: {{ $iconDim }}px; height: {{ $iconDim }}px; display: block;" class="text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
        </svg>
    </div>
    <div class="flex flex-col justify-center leading-none">
        <span class="font-black {{ $textSize }} tracking-tighter text-slate-950 dark:text-white uppercase leading-none">{{ __('ui.site_name') }}</span>
        <span class="text-[9px] font-bold text-cyan-500 uppercase tracking-[0.2em] ml-0.5 mt-0.5">{{ __('Magazine') }}</span>
    </div>
</a>