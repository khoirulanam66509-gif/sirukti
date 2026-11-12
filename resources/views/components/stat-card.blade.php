@props([
    'title',
    'value',
    'featured' => false,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-card p-5 md:p-6 transition-all duration-200 ' . ($featured ? 'bg-gradient-to-br from-[#2F5D3A] to-[#3C7A4C] text-white shadow-featured' : 'bg-surface border border-border-soft shadow-card text-ink')]) }}>
    <div class="flex items-center justify-between gap-3 mb-3">
        <div class="w-11 h-11 rounded-[14px] flex items-center justify-center flex-shrink-0 {{ $featured ? 'bg-white/20 text-white' : 'bg-primary-light text-primary' }}">
            {{ $icon ?? '' }}
        </div>
        @if(isset($badge))
            <div>{{ $badge }}</div>
        @endif
    </div>
    <div class="space-y-1">
        <div class="text-3xl font-extrabold tracking-tight font-sans {{ $featured ? 'text-white' : 'text-ink' }}">
            {{ $value }}
        </div>
        <div class="text-xs md:text-sm font-medium {{ $featured ? 'text-white/80' : 'text-ink-muted' }}">
            {{ $title }}
        </div>
        @if($description)
            <div class="text-xs pt-1 {{ $featured ? 'text-white/70' : 'text-ink-muted/80' }}">
                {{ $description }}
            </div>
        @endif
    </div>
</div>
