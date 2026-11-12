@props(['kode' => '', 'nama' => null, 'size' => 'md', 'showIcon' => true, 'variant' => 'badge'])

@php
$kodeUpper = strtoupper(trim((string) $kode));

$theme = match ($kodeUpper) {
    'SKCK' => [
        'name' => 'Pengantar SKCK',
        'bg' => 'bg-emerald-50',
        'text' => 'text-emerald-700',
        'border' => 'border-emerald-200',
        'icon_bg' => 'bg-emerald-100 text-emerald-700',
        'solid_bg' => 'bg-emerald-600 text-white',
    ],
    'DOM' => [
        'name' => 'Keterangan Domisili',
        'bg' => 'bg-blue-50',
        'text' => 'text-blue-700',
        'border' => 'border-blue-200',
        'icon_bg' => 'bg-blue-100 text-blue-700',
        'solid_bg' => 'bg-blue-600 text-white',
    ],
    'SKTM' => [
        'name' => 'Keterangan Tidak Mampu',
        'bg' => 'bg-amber-50',
        'text' => 'text-amber-800',
        'border' => 'border-amber-200',
        'icon_bg' => 'bg-amber-100 text-amber-800',
        'solid_bg' => 'bg-amber-600 text-white',
    ],
    'SKU' => [
        'name' => 'Keterangan Usaha',
        'bg' => 'bg-purple-50',
        'text' => 'text-purple-700',
        'border' => 'border-purple-200',
        'icon_bg' => 'bg-purple-100 text-purple-700',
        'solid_bg' => 'bg-purple-600 text-white',
    ],
    default => [
        'name' => 'Surat Administrasi',
        'bg' => 'bg-slate-100',
        'text' => 'text-slate-700',
        'border' => 'border-slate-200',
        'icon_bg' => 'bg-slate-200 text-slate-700',
        'solid_bg' => 'bg-slate-600 text-white',
    ],
};

$label = $nama ?? $theme['name'];
@endphp

@if($variant === 'icon')
    <!-- Hanya Icon Box Lingkaran/Kotak (Ikon dokumen surat seragam, berbeda warna background & ikon) -->
    <div {{ $attributes->merge(['class' => "rounded-xl flex items-center justify-center shrink-0 {$theme['icon_bg']} " . ($size === 'sm' ? 'w-8 h-8' : ($size === 'lg' ? 'w-12 h-12' : 'w-10 h-10'))]) }}>
        <svg class="{{ $size === 'sm' ? 'w-4 h-4' : ($size === 'lg' ? 'w-6 h-6' : 'w-5 h-5') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </div>
@else
    <!-- Badge / Pill dengan Text (Ikon dokumen surat seragam) -->
    <span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {$theme['bg']} {$theme['text']} {$theme['border']}"]) }}>
        @if($showIcon)
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif
        <span>{{ $label }}</span>
    </span>
@endif
