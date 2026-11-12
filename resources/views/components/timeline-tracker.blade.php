@props(['logs' => []])

<div {{ $attributes->merge(['class' => 'flow-root']) }}>
    <ul role="list" class="-mb-8">
        @forelse($logs as $index => $log)
            @php
                $isLast = $loop->last;
            @endphp
            <li>
                <div class="relative pb-8">
                    @if(!$isLast)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-border-soft" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex items-start space-x-3">
                        <div>
                            <div class="h-8 w-8 rounded-full flex items-center justify-center ring-4 {{ $isLast ? 'bg-primary text-white ring-primary-light shadow-sm' : 'bg-primary/80 text-white ring-white' }}">
                                @if($isLast)
                                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="min-w-0 flex-1 pt-0.5">
                            <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                                <div class="text-sm font-bold text-ink">
                                    <x-badge-status-pengajuan :status="$log->status_baru" />
                                </div>
                                <div class="text-xs text-ink-muted">
                                    {{ tgl_indo($log->created_at, true) }}
                                </div>
                            </div>
                            @if($log->catatan)
                                <p class="text-sm text-ink bg-canvas p-3 rounded-xl border border-border-soft mt-1.5">
                                    {{ $log->catatan }}
                                </p>
                            @endif
                            @if($log->pengguna)
                                <p class="text-xs text-ink-muted mt-1">
                                    Oleh: <span class="font-semibold">{{ $log->pengguna->nama }}</span> ({{ ucfirst($log->pengguna->role) }})
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="text-sm text-ink-muted text-center py-4">Belum ada riwayat aktivitas.</li>
        @endforelse
    </ul>
</div>
