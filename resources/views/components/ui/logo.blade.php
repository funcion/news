@props([
    'size' => 'md',
    'class' => '',
])

@php
    $dim = match($size) {
        'sm' => 30,
        'lg' => 44,
        default => 38,
    };
    $textSize = match($size) {
        'sm' => 'text-lg',
        'lg' => 'text-2xl',
        default => 'text-xl',
    };
@endphp

<a href="{{ url('/') }}" class="inline-flex items-center group {{ $class }}" aria-label="{{ __('ui.site_name') }}">
    <!-- Glodaxia Brand Logo Icon (Glodaxia-sm.png) -->
    <img src="{{ asset('images/Glodaxia-sm.png') }}" 
         alt="{{ __('ui.site_name') }}" 
         width="{{ $dim }}" height="{{ $dim }}" loading="eager" decoding="async" class="shrink-0 transform group-hover:scale-105 transition-transform duration-200 object-contain" 
         style="width: {{ $dim }}px; height: {{ $dim }}px; min-width: {{ $dim }}px; min-height: {{ $dim }}px; max-width: {{ $dim }}px; max-height: {{ $dim }}px; aspect-ratio: 1 / 1; margin-right: 0.75rem;" />
    
    <div class="flex flex-col justify-center leading-none">
        <span class="font-black {{ $textSize }} tracking-tighter text-slate-950 dark:text-white uppercase leading-none">{{ __('ui.site_name') }}</span>
        <span class="text-[9px] font-bold text-cyan-500 uppercase tracking-[0.2em] ml-0.5 mt-0.5">{{ __('Magazine') }}</span>
    </div>
</a>