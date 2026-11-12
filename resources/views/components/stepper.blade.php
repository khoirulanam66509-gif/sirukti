@props([
    'currentStep' => 1,
    'steps' => [
        1 => 'Pilih Layanan',
        2 => 'Isi Data Surat',
        3 => 'Unggah Berkas',
        4 => 'Tinjau & Kirim',
    ],
])

@php
$totalSteps = count($steps);
$progressPercent = intval(($currentStep / $totalSteps) * 100);
$currentTitle = $steps[$currentStep] ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'w-full mb-6']) }}>
    <!-- Mobile Stepper (Bar Progres + Teks Ringkas) -->
    <div class="md:hidden bg-surface rounded-2xl p-4 border border-border-soft shadow-card">
        <div class="flex items-center justify-between text-xs font-semibold text-ink-muted mb-2">
            <span>Langkah {{ $currentStep }} dari {{ $totalSteps }}</span>
            <span class="text-primary font-bold">{{ $progressPercent }}%</span>
        </div>
        <div class="w-full bg-border-soft h-2 rounded-full overflow-hidden mb-2">
            <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
        </div>
        <div class="text-sm font-bold text-ink">
            {{ $currentTitle }}
        </div>
    </div>

    <!-- Desktop Stepper (Horizontal Stepper Lengkap) -->
    <div class="hidden md:flex items-center justify-between bg-surface rounded-card p-6 border border-border-soft shadow-card">
        @foreach($steps as $stepNumber => $stepLabel)
            @php
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent = $stepNumber == $currentStep;
            @endphp
            <div class="flex items-center flex-1 {{ $loop->last ? 'flex-initial' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-200 flex-shrink-0
                        {{ $isCompleted ? 'bg-primary text-white shadow-sm' : ($isCurrent ? 'bg-primary text-white ring-4 ring-primary/20 shadow-md' : 'bg-gray-100 text-ink-muted border border-border-soft') }}">
                        @if($isCompleted)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-semibold uppercase tracking-wider {{ $isCurrent ? 'text-primary' : 'text-ink-muted' }}">
                            Langkah {{ $stepNumber }}
                        </span>
                        <span class="text-sm font-bold {{ $isCurrent ? 'text-ink' : ($isCompleted ? 'text-ink' : 'text-ink-muted') }}">
                            {{ $stepLabel }}
                        </span>
                    </div>
                </div>

                @if(!$loop->last)
                    <div class="flex-1 mx-4 h-0.5 {{ $isCompleted ? 'bg-primary' : 'bg-border-soft' }}"></div>
                @endif
            </div>
        @endforeach
    </div>
</div>
