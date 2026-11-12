@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <!-- Mobile View: Kartu Bertumpuk (< md) -->
    <div class="md:hidden space-y-3">
        {{ $mobile }}
    </div>

    <!-- Desktop View: Tabel Standar (>= md) -->
    <div class="hidden md:block overflow-hidden bg-surface rounded-card border border-border-soft shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-canvas border-b border-border-soft text-xs font-bold uppercase tracking-wider text-ink-muted">
                        @foreach($headers as $header)
                            <th scope="col" class="py-3.5 px-4 first:pl-6 last:pr-6">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-soft text-sm text-ink">
                    {{ $desktop }}
                </tbody>
            </table>
        </div>
    </div>
</div>
