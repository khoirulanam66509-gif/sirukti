@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm md:text-base text-ink mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
