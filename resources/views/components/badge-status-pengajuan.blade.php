@props(['status'])

@php
$config = match ($status) {
    'menunggu' => [
        'bg' => 'bg-[#FEF3C7]',
        'text' => 'text-[#B7791F]',
        'dot' => 'bg-[#D97706]',
        'label' => 'Menunggu Verifikasi',
    ],
    'kurang_berkas' => [
        'bg' => 'bg-[#FFEDD5]',
        'text' => 'text-[#C2410C]',
        'dot' => 'bg-[#EA580C]',
        'label' => 'Kurang Berkas',
    ],
    'diproses' => [
        'bg' => 'bg-[#DBEAFE]',
        'text' => 'text-[#1D4ED8]',
        'dot' => 'bg-[#2563EB]',
        'label' => 'Sedang Diproses',
    ],
    'menunggu_ttd' => [
        'bg' => 'bg-[#EDE9FE]',
        'text' => 'text-[#6D28D9]',
        'dot' => 'bg-[#7C3AED]',
        'label' => 'Menunggu TTD',
    ],
    'selesai' => [
        'bg' => 'bg-[#DCFCE7]',
        'text' => 'text-[#15803D]',
        'dot' => 'bg-[#16A34A]',
        'label' => 'Selesai',
    ],
    'ditolak' => [
        'bg' => 'bg-[#FEE2E2]',
        'text' => 'text-[#B91C1C]',
        'dot' => 'bg-[#DC2626]',
        'label' => 'Ditolak',
    ],
    default => [
        'bg' => 'bg-gray-100',
        'text' => 'text-gray-700',
        'dot' => 'bg-gray-500',
        'label' => ucfirst((string) $status),
    ],
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs md:text-sm font-semibold tracking-wide {$config['bg']} {$config['text']}"]) }}>
    <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
