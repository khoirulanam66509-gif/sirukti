@props([
    'title' => 'Belum Ada Data',
    'description' => 'Data tidak ditemukan atau belum pernah dibuat.',
])

<div {{ $attributes->merge(['class' => 'bg-surface rounded-card border border-border-soft p-8 text-center flex flex-col items-center justify-center my-4']) }}>
    <div class="w-16 h-16 rounded-2xl bg-primary-light text-primary flex items-center justify-center mb-4">
        {{ $icon ?? '' }}
        @if(!isset($icon))
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif
    </div>
    <h3 class="text-lg font-bold text-ink mb-1">{{ $title }}</h3>
    <p class="text-sm text-ink-muted max-w-sm mb-6">{{ $description }}</p>
    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif
</div>
