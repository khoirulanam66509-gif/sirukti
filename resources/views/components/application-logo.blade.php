@props([
    'dark' => false,
    'size' => 'md',
    'showSub' => true,
])

@php
    $imgSizeClass = match($size) {
        'sm' => 'h-8 w-auto max-w-[32px]',
        'lg' => 'h-12 w-auto max-w-[48px]',
        default => 'h-10 w-auto max-w-[40px]',
    };

    $titleClass = match($size) {
        'sm' => 'text-base',
        'lg' => 'text-xl',
        default => 'text-lg',
    };

    $subClass = match($size) {
        'sm' => 'text-[10px]',
        'lg' => 'text-xs',
        default => 'text-[11px]',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 group']) }}>
    <div class="relative flex-shrink-0 flex items-center justify-center p-0.5">
        <img
            src="{{ asset('images/logo-lampung-tengah.png') }}"
            alt="Lambang Kabupaten Lampung Tengah"
            class="{{ $imgSizeClass }} object-contain filter drop-shadow-sm transition-transform duration-200 group-hover:scale-105"
            loading="eager"
        >
    </div>
    <div class="flex flex-col leading-tight justify-center select-none">
        <div class="flex items-center gap-1.5">
            <span class="font-extrabold tracking-tight {{ $titleClass }} {{ $dark ? 'text-white' : 'text-ink' }} font-sans">
                SIRUKTI
            </span>
            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold tracking-wider uppercase {{ $dark ? 'bg-accent/20 text-accent border border-accent/30' : 'bg-primary-light text-primary border border-primary/20' }}">
                Resmi
            </span>
        </div>
        @if($showSub)
            <span class="{{ $subClass }} font-bold tracking-wider {{ $dark ? 'text-emerald-200/95' : 'text-ink-muted' }} uppercase">
                Kampung Rukti Harjo
            </span>
            <span class="text-[9px] font-medium tracking-tight {{ $dark ? 'text-emerald-300/70' : 'text-ink-muted/80' }}">
                Kec. Seputih Raman, Lampung Tengah
            </span>
        @endif
    </div>
</div>
