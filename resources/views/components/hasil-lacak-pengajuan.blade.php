@props(['pengajuan'])

@php
    $status = $pengajuan->status;
    $step = match($status) {
        'menunggu' => 1,
        'diproses' => 2,
        'kurang_berkas' => 2,
        'menunggu_ttd' => 3,
        'selesai' => 4,
        'ditolak' => 2,
        default => 1,
    };
@endphp

<div class="bg-surface rounded-card p-5 sm:p-7 border border-border-soft shadow-card space-y-6 text-left">
    <!-- Header Hasil Pelacakan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-border-soft pb-4">
        <div class="flex items-start gap-3">
            <x-badge-layanan :kode="$pengajuan->layanan?->kode" size="md" />
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm sm:text-base font-extrabold text-ink">{{ $pengajuan->no_pengajuan }}</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-ink mt-0.5">{{ $pengajuan->layanan?->nama_layanan }}</h3>
            </div>
        </div>
        <div class="self-start sm:self-auto">
            <x-badge-status-pengajuan :status="$status" />
        </div>
    </div>

    <!-- Stepper Progres Status -->
    <div class="py-2">
        <div class="relative grid grid-cols-4 gap-1 items-start">
            <div class="absolute left-0 top-4 -translate-y-1/2 w-full h-1 bg-border-soft -z-0"></div>
            <div class="absolute left-0 top-4 -translate-y-1/2 h-1 bg-primary transition-all duration-500 -z-0"
                 style="width: {{ $status === 'selesai' ? '100%' : ($step === 3 ? '66%' : ($step === 2 ? '33%' : '0%')) }};"></div>

            <!-- Step 1: Diajukan -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-primary text-white ring-4 ring-primary-soft' : 'bg-surface border-2 border-border-soft text-ink-muted' }}">
                    1
                </div>
                <span class="text-[11px] font-semibold text-ink mt-1.5 text-center">Diajukan</span>
            </div>

            <!-- Step 2: Verifikasi Kasi -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? ($status === 'kurang_berkas' ? 'bg-amber-500 text-white ring-4 ring-amber-100' : ($status === 'ditolak' ? 'bg-rose-500 text-white ring-4 ring-rose-100' : 'bg-primary text-white ring-4 ring-primary-soft')) : 'bg-surface border-2 border-border-soft text-ink-muted' }}">
                    2
                </div>
                <span class="text-[11px] font-semibold {{ $status === 'kurang_berkas' ? 'text-amber-700' : ($status === 'ditolak' ? 'text-rose-700' : 'text-ink') }} mt-1.5 text-center">
                    {{ $status === 'kurang_berkas' ? 'Kurang Berkas' : ($status === 'ditolak' ? 'Ditolak' : 'Verifikasi Kasi') }}
                </span>
            </div>

            <!-- Step 3: Pengesahan Kepala -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-primary text-white ring-4 ring-primary-soft' : 'bg-surface border-2 border-border-soft text-ink-muted' }}">
                    3
                </div>
                <span class="text-[11px] font-semibold text-ink mt-1.5 text-center">TTD Kepala</span>
            </div>

            <!-- Step 4: Selesai -->
            <div class="flex flex-col items-center relative z-10">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $status === 'selesai' ? 'bg-success text-white ring-4 ring-success-soft' : 'bg-surface border-2 border-border-soft text-ink-muted' }}">
                    ✓
                </div>
                <span class="text-[11px] font-semibold text-ink mt-1.5 text-center">Surat Terbit</span>
            </div>
        </div>
    </div>

    <!-- Alert / Banner Khusus Berdasarkan Status -->
    @if($status === 'selesai')
        <div class="p-4 rounded-xl bg-success-soft border border-success/30 flex items-start gap-3">
            <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="space-y-1 text-xs sm:text-sm">
                <p class="font-bold text-success-ink">Surat Resmi Telah Diterbitkan &amp; Ditandatangani Secara Sah!</p>
                <p class="text-ink-muted">
                    No. Surat Resmi: <strong class="font-mono text-primary">{{ $pengajuan->surat?->no_surat ?? '-' }}</strong>.
                    Silakan masuk ke akun warga Anda untuk mengunduh dokumen PDF asli.
                </p>
            </div>
        </div>
    @elseif($status === 'kurang_berkas')
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="space-y-1 text-xs sm:text-sm">
                <p class="font-bold text-amber-900">Terdapat Berkas yang Perlu Diperbaiki!</p>
                <p class="text-amber-800">
                    Petugas verifikator meminta perbaikan berkas persyaratan. Silakan masuk ke akun warga Anda untuk mengunggah ulang berkas yang dimaksud.
                </p>
            </div>
        </div>
    @elseif($status === 'ditolak')
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="space-y-1 text-xs sm:text-sm">
                <p class="font-bold text-rose-900">Permohonan Surat Tidak Dapat Diproses</p>
                <p class="text-rose-800">
                    Permohonan surat ditolak karena persyaratan tidak terpenuhi. Silakan hubungi kantor kampung atau ajukan kembali dengan data yang valid.
                </p>
            </div>
        </div>
    @endif

    <!-- Data Rincian Dokumen & Pemohon (Privasi Aman) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm bg-canvas/70 p-4 rounded-xl border border-border-soft">
        <div>
            <span class="text-ink-muted text-xs block">Nama Pemohon:</span>
            <span class="font-bold text-ink">{{ $pengajuan->warga?->pengguna?->nama ?? '-' }}</span>
        </div>
        <div>
            <span class="text-ink-muted text-xs block">NIK Pemohon (Privasi):</span>
            <span class="font-mono font-bold text-ink">{{ samarkan_nik($pengajuan->warga?->nik) }}</span>
        </div>
        <div>
            <span class="text-ink-muted text-xs block">Waktu Pengajuan:</span>
            <span class="text-ink font-medium">{{ $pengajuan->tgl_pengajuan ? $pengajuan->tgl_pengajuan->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
        </div>
        <div>
            <span class="text-ink-muted text-xs block">Pembaruan Terakhir:</span>
            <span class="text-ink font-medium">{{ $pengajuan->updated_at ? $pengajuan->updated_at->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
        </div>
    </div>

    <!-- Catatan Terakhir dari Log (Bila Ada) -->
    @php
        $lastLogWithNote = $pengajuan->statusLogs->whereNotNull('catatan')->last();
    @endphp
    @if($lastLogWithNote && !empty($lastLogWithNote->catatan))
        <div class="p-3.5 rounded-xl bg-canvas border border-border-soft text-xs space-y-1">
            <span class="font-bold text-ink">Catatan Petugas Verifikasi:</span>
            <p class="text-ink-muted italic">"{{ $lastLogWithNote->catatan }}"</p>
        </div>
    @endif

    <!-- Action Button -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5 pt-2 border-t border-border-soft/60">
        @auth
            @php
                $userRole = auth()->user()?->role;
                $isOwnerWarga = $userRole === 'warga' && $pengajuan->warga?->id_pengguna === auth()->id();
                $detailUrl = match(true) {
                    $isOwnerWarga => route('pengajuan.show', $pengajuan),
                    $userRole === 'kasi' => route('kasi.pengajuan.show', $pengajuan),
                    $userRole === 'kepala' => route('kepala.persetujuan.show', $pengajuan),
                    default => route('dashboard'),
                };
            @endphp
            <a href="{{ $detailUrl }}" class="min-h-[44px] px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs sm:text-sm text-center shadow-sm hover:bg-primary-hover transition active:scale-95 inline-flex items-center justify-center">
                Lihat Rincian di Beranda
            </a>
        @else
            <a href="{{ route('login') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs sm:text-sm text-center shadow-sm hover:bg-primary-hover transition active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>Masuk ke Akun untuk Rincian Penuh</span>
            </a>
        @endauth
    </div>
</div>
