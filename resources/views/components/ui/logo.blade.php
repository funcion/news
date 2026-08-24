@props([
    'size' => 'md',
    'class' => '',
])

@php
    $boxSize = match($size) {
        'sm' => 'w-7 h-7',
        'lg' => 'w-10 h-10',
        default => 'w-8 h-8',
    };
    $iconPixels = match($size) {
        'sm' => '16',
        'lg' => '24',
        default => '20',
    };
    $textSize = match($size) {
        'sm' => 'text-lg',
        'lg' => 'text-2xl',
        default => 'text-xl',
    };
@endphp

<a href="{{ url('/') }}" class="inline-flex items-center gap-3.5 group {{ $class }}" aria-label="{{ __('ui.site_name') }}">
    <div class="{{ $boxSize }} bg-cyan-500 rounded-lg flex items-center justify-center transform group-hover:rotate-6 transition-transform shadow-lg shadow-cyan-500/20 shrink-0" style="min-width: {{ $iconPixels === '24' ? '40px' : ($iconPixels === '16' ? '28px' : '32px') }};">
        <svg width="{{ $iconPixels }}" height="{{ $iconPixels }}" style="width: {{ $iconPixels }}px; height: {{ $iconPixels }}px; display: block;" class="text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
        </svg>
    </div>
    <div class="flex flex-col -gap-0.5" style="margin-left: 2px;">
        <span class="font-black {{ $textSize }} tracking-tighter text-slate-950 dark:text-white uppercase leading-none">{{ __('ui.site_name') }}</span>
        <span class="text-[9px] font-bold text-cyan-500 uppercase tracking-[0.2em] ml-0.5 mt-0.5">{{ __('Magazine') }}</span>
    </div>
</a>