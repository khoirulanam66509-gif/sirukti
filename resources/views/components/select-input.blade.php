@props(['disabled' => false])

<div class="relative w-full">
    <select @disabled($disabled) {{ $attributes->merge(['class' => 'h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border border-border-soft focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out disabled:bg-gray-100 disabled:cursor-not-allowed w-full appearance-none pr-10']) }}>
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-ink-muted">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>
