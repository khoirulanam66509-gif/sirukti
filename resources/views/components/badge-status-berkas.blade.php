@props(['status'])

@php
$config = match ($status) {
    'sesuai' => [
        'bg' => 'bg-[#DCFCE7]',
        'text' => 'text-[#15803D]',
        'dot' => 'bg-[#16A34A]',
        'label' => 'Sesuai',
    ],
    'perlu_perbaikan' => [
        'bg' => 'bg-[#FFEDD5]',
        'text' => 'text-[#C2410C]',
        'dot' => 'bg-[#EA580C]',
        'label' => 'Perlu Perbaikan',
    ],
    default => [
        'bg' => 'bg-gray-100',
        'text' => 'text-gray-700',
        'dot' => 'bg-gray-400',
        'label' => 'Menunggu Verifikasi',
    ],
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {$config['bg']} {$config['text']}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
