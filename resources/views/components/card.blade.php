@props(['variant' => 'standard'])

@php
$baseClasses = 'rounded-card transition-all duration-200';
$variantClasses = match ($variant) {
    'featured' => 'bg-gradient-to-br from-[#2F5D3A] to-[#3C7A4C] text-white shadow-featured border border-transparent p-5 md:p-6',
    'muted' => 'bg-[#F8FAF6] border border-dashed border-[#CBD5C8] p-5 md:p-6 text-ink',
    default => 'bg-surface border border-border-soft shadow-card p-5 md:p-6 text-ink',
};
@endphp

<div {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</div>
