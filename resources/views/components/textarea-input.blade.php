@props(['disabled' => false, 'rows' => 3])

<textarea @disabled($disabled) rows="{{ $rows }}" {{ $attributes->merge(['class' => 'p-4 bg-surface text-ink text-base rounded-btn border border-border-soft focus:border-primary focus:ring-4 focus:ring-primary/15 transition duration-150 ease-in-out placeholder:text-ink-muted/60 disabled:bg-gray-100 disabled:cursor-not-allowed w-full']) }}>{{ $slot }}</textarea>
