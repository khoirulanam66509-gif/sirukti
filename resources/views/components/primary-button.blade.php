@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center min-h-[48px] px-5 py-2.5 bg-primary hover:bg-primary-hover active:scale-[0.98] border border-transparent rounded-btn font-semibold text-base text-white tracking-wide shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
