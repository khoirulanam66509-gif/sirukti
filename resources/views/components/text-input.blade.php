@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-[50px] min-h-[50px] px-4 py-2 bg-surface text-ink text-base rounded-btn border border-border-soft focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 disabled:bg-gray-100 disabled:cursor-not-allowed w-full']) }}>
