@props([
    'name',
    'show' => false,
    'maxWidth' => 'md',
    'title' => null,
])

@php
$maxWidthClasses = match ($maxWidth) {
    'sm' => 'md:max-w-sm',
    'md' => 'md:max-w-md',
    'lg' => 'md:max-w-lg',
    'xl' => 'md:max-w-xl',
    '2xl' => 'md:max-w-2xl',
    default => 'md:max-w-md',
};
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)].filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return (this.focusables().indexOf(document.activeElement) - 1 + this.focusables().length + 1) % (this.focusables().length + 1) },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-0 md:px-4 py-0 md:py-6 z-50 flex flex-col justify-end md:justify-center items-center"
    style="display: {{ $show ? 'flex' : 'none' }};"
>
    <!-- Backdrop Overlay -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all backdrop-blur-[2px]"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-[#1E2A22]/40"></div>
    </div>

    <!-- Modal Box (Desktop) / Bottom Sheet (Mobile) -->
    <div
        x-show="show"
        class="relative w-full {{ $maxWidthClasses }} bg-surface rounded-t-[28px] md:rounded-card border border-border-soft shadow-modal overflow-hidden transform transition-all max-h-[90vh] flex flex-col"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
        x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
    >
        <!-- Mobile drag indicator -->
        <div class="md:hidden pt-3 pb-1 flex justify-center">
            <div class="w-12 h-1.5 rounded-full bg-border-soft"></div>
        </div>

        @if($title)
            <div class="px-6 pt-4 pb-3 border-b border-border-soft flex items-center justify-between">
                <h3 class="text-lg font-bold text-ink">{{ $title }}</h3>
                <button type="button" @click="show = false" class="text-ink-muted hover:text-ink p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="p-6 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
