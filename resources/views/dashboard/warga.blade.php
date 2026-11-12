<x-app-layout>
    <x-slot name="pageTitle">Beranda Warga</x-slot>
    <x-slot name="title">Beranda Warga — SIRUKTI</x-slot>
    <x-slot name="header">
        @php
            $jamLayananHeader = \App\Models\Pengaturan::ambil('jam_layanan', ['senin_kamis' => '08.00 - 15.00 WIB']);
            $jamOperasionalTeks = $jamLayananHeader['senin_kamis'] ?? '08.00 - 15.00 WIB';
        @endphp
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface border border-border-soft text-ink-muted text-xs font-medium shadow-xs">
                    <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ tgl_indo(now()) }}</span>
                    <span class="inline-block w-1 h-1 rounded-full bg-border-soft"></span>
                    <span class="text-primary font-bold">Pelayanan Balai Buka ({{ $jamOperasionalTeks }})</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                    Halo, {{ $user->nama }} 👋
                </h1>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Selamat datang di layanan mandiri persuratan digital Kampung Rukti Harjo, Seputih Raman.
                </p>
            </div>
            <!-- Secondary Stat Badge -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="bg-surface rounded-card px-4 py-2.5 shadow-card border border-border-soft flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-[11px] text-ink-muted uppercase tracking-wider font-bold">Surat Selesai</span>
                        <span class="text-lg font-extrabold text-ink leading-tight">{{ $totalSuratSelesai }} Dokumen</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Message Status -->
        @if (session('status'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- PROMINENT PROFILE COMPLETENESS NOTIFICATION STRIP (if not 100%) -->
        @if (! $isProfilLengkap)
            <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs sm:text-sm font-bold text-ink">Kelengkapan Data Kependudukan ({{ $persentaseProfil }}%)</span>
                        </div>
                        <div class="w-full bg-amber-200/60 h-1.5 rounded-full overflow-hidden mt-1.5 max-w-md">
                            <div class="h-full bg-accent hover:bg-accent-hover transition-all duration-700 rounded-full" style="width: {{ $persentaseProfil }}%;"></div>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('profile.edit') }}" class="w-full sm:w-auto min-h-[44px] px-4 py-2 rounded-btn bg-primary text-white font-bold text-xs hover:bg-primary-hover shadow-xs transition inline-flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Lengkapi Profil</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- PRIMARY ACTION HERO CARD -->
        <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-primary via-primary-dark to-[#163a20] p-5 sm:p-7 text-white shadow-featured">
            <!-- Background Ambient Vectors -->
            <div class="absolute -right-16 -top-20 w-64 h-64 rounded-full bg-accent/15 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="max-w-xl space-y-1.5">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-accent text-ink text-[11px] font-extrabold shadow-sm">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Layanan Mandiri 24 Jam</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white leading-tight">
                        Ajukan Surat Keterangan Desa
                    </h2>
                    <p class="text-xs sm:text-sm text-white/85 font-normal leading-relaxed">
                        Permohonan SKTM, Domisili, Pengantar SKCK, atau SKU cepat dengan tanda tangan digital resmi.
                    </p>
                </div>

                <div class="shrink-0 pt-2 sm:pt-0">
                    <a href="{{ route('pengajuan.buat') }}" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-btn bg-accent text-ink font-bold text-xs sm:text-sm hover:bg-accent-hover transition shadow-md active:scale-95">
                        <span>Buat Permohonan Baru</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN TWO-COLUMN DASHBOARD GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: PENGAJUAN AKTIF (7 Cols Desktop) -->
            <div class="lg:col-span-7 space-y-6">

                @if ($pengajuanAktif)
                    <!-- Card Pengajuan Aktif -->
                    <div class="bg-surface rounded-card p-5 sm:p-6 shadow-card border border-border-soft space-y-5">
                        <!-- Header Card -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-border-soft">
                            <div class="flex items-center gap-3">
                                <x-badge-layanan :kode="$pengajuanAktif->layanan?->kode" variant="icon" size="md" />
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                        <span class="text-xs uppercase tracking-wider font-bold text-ink-muted">Pengajuan Aktif</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-ink mt-0.5">
                                        {{ $pengajuanAktif->layanan?->nama_layanan ?? 'Surat Permohonan' }}
                                    </h3>
                                </div>
                            </div>
                            <div>
                                <x-badge-status-pengajuan :status="$pengajuanAktif->status" />
                            </div>
                        </div>

                        <!-- Meta Info Panel -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 rounded-2xl bg-canvas border border-border-soft text-xs">
                            <div>
                                <span class="block text-ink-muted">No. Pengajuan</span>
                                <span class="font-bold text-ink font-mono mt-0.5">{{ $pengajuanAktif->no_pengajuan }}</span>
                            </div>
                            <div>
                                <span class="block text-ink-muted">Waktu Kirim</span>
                                <span class="font-semibold text-ink mt-0.5">{{ tgl_indo($pengajuanAktif->tgl_pengajuan, true) }}</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <span class="block text-ink-muted">Keperluan</span>
                                <span class="font-semibold text-ink truncate mt-0.5 block">
                                    {{ $pengajuanAktif->data_isian['keperluan'] ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Alur Progres Persuratan: 5 Tahap Lengkap -->
                        @php
                            $stepIndex = match ($pengajuanAktif->status) {
                                'menunggu' => 1,
                                'kurang_berkas' => 2,
                                'diproses' => 3,
                                'menunggu_ttd' => 4,
                                'selesai' => 5,
                                default => 1,
                            };
                        @endphp
                        <div class="py-2">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs uppercase tracking-wider font-bold text-ink-muted">
                                    Alur Progres Surat
                                </span>
                                <span class="text-xs font-semibold text-ink-muted">
                                    Tahap {{ $stepIndex }} dari 5: <strong class="{{ $pengajuanAktif->status === 'kurang_berkas' ? 'text-orange-600' : 'text-primary' }}">{{ match ($pengajuanAktif->status) {
                                        'menunggu' => 'Dikirim',
                                        'kurang_berkas' => 'Perlu Perbaikan',
                                        'diproses' => 'Diproses Petugas',
                                        'menunggu_ttd' => 'Menunggu TTD',
                                        'selesai' => 'Selesai',
                                        default => 'Dikirim'
                                    } }}</strong>
                                </span>
                            </div>

                            <!-- 5-Point Milestone Track (Tampil di Mobile & Desktop) -->
                            <div class="relative pt-2 sm:pt-3 pb-1">
                                <!-- Track bar -->
                                <div class="absolute top-6 sm:top-7 left-4 right-4 h-0.5 bg-border-soft -translate-y-1/2 z-0"></div>
                                <div class="absolute top-6 sm:top-7 left-4 h-0.5 bg-primary -translate-y-1/2 z-0 transition-all duration-700" style="width: {{ (($stepIndex - 1) / 4) * 100 }}%;"></div>

                                <div class="relative z-10 grid grid-cols-5 gap-1 text-center text-xs">
                                    <!-- Step 1: Dikirim -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex >= 1 ? 'bg-primary text-white' : 'bg-surface text-ink-muted border border-border-soft' }}">
                                            ✓
                                        </div>
                                        <span class="mt-1 sm:mt-1.5 text-[11px] sm:text-xs font-bold {{ $stepIndex >= 1 ? 'text-primary' : 'text-ink-muted' }}">Dikirim</span>
                                    </div>
                                    <!-- Step 2: Verifikasi -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 2 ? 'bg-primary text-white' : ($stepIndex === 2 ? ($pengajuanAktif->status === 'kurang_berkas' ? 'bg-orange-500 text-white' : 'bg-primary text-white') : 'bg-surface text-ink-muted border border-border-soft') }}">
                                            {{ $stepIndex > 2 ? '✓' : '2' }}
                                        </div>
                                        <span class="mt-1 sm:mt-1.5 text-[11px] sm:text-xs font-bold {{ $stepIndex >= 2 ? 'text-primary' : 'text-ink-muted' }}">
                                            {{ $pengajuanAktif->status === 'kurang_berkas' ? 'Perbaikan' : 'Verifikasi' }}
                                        </span>
                                    </div>
                                    <!-- Step 3: Diproses -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 3 ? 'bg-primary text-white' : ($stepIndex === 3 ? 'bg-primary text-white ring-4 ring-primary/20' : 'bg-surface text-ink-muted border border-border-soft') }}">
                                            {{ $stepIndex > 3 ? '✓' : '3' }}
                                        </div>
                                        <span class="mt-1 sm:mt-1.5 text-[11px] sm:text-xs font-bold {{ $stepIndex >= 3 ? 'text-primary' : 'text-ink-muted' }}">Diproses</span>
                                    </div>
                                    <!-- Step 4: TTD Kepala -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 4 ? 'bg-primary text-white' : ($stepIndex === 4 ? 'bg-purple-600 text-white ring-4 ring-purple-100' : 'bg-surface text-ink-muted border border-border-soft') }}">
                                            {{ $stepIndex > 4 ? '✓' : '4' }}
                                        </div>
                                        <span class="mt-1 sm:mt-1.5 text-[11px] sm:text-xs font-bold {{ $stepIndex >= 4 ? 'text-primary' : 'text-ink-muted' }}">TTD</span>
                                    </div>
                                    <!-- Step 5: Selesai -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex === 5 ? 'bg-emerald-600 text-white' : 'bg-surface text-ink-muted border border-border-soft' }}">
                                            {{ $stepIndex === 5 ? '✓' : '5' }}
                                        </div>
                                        <span class="mt-1 sm:mt-1.5 text-[11px] sm:text-xs font-bold {{ $stepIndex === 5 ? 'text-emerald-700' : 'text-ink-muted' }}">Selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Petugas if any -->
                        @if ($pengajuanAktif->catatan_petugas)
                            <div class="p-3.5 rounded-xl border text-xs flex items-start gap-2.5 {{ $pengajuanAktif->status === 'kurang_berkas' ? 'bg-orange-50 border-orange-200 text-orange-900' : 'bg-canvas border-border-soft text-ink' }}">
                                <svg class="w-4 h-4 shrink-0 mt-0.5 {{ $pengajuanAktif->status === 'kurang_berkas' ? 'text-orange-600' : 'text-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="space-y-0.5">
                                    <span class="font-bold">Catatan Petugas:</span>
                                    <p class="leading-relaxed">{{ $pengajuanAktif->catatan_petugas }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                            @if ($pengajuanAktif->status === 'kurang_berkas')
                                <a href="{{ route('pengajuan.show', $pengajuanAktif) }}" class="w-full sm:flex-1 min-h-[44px] px-4 rounded-btn bg-amber-500 text-white font-bold text-xs hover:bg-amber-600 transition inline-flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Perbaiki Berkas Sekarang</span>
                                </a>
                                <a href="{{ route('pengajuan.show', $pengajuanAktif) }}" class="w-full sm:w-auto min-h-[44px] px-4 rounded-btn bg-canvas text-ink font-semibold text-xs border border-border-soft hover:bg-border-soft transition inline-flex items-center justify-center gap-2">
                                    <span>Lihat Detail</span>
                                </a>
                            @elseif ($pengajuanAktif->status === 'selesai')
                                <a href="{{ route('surat.unduh', $pengajuanAktif) }}" class="w-full sm:flex-1 min-h-[44px] px-4 rounded-btn bg-primary text-white font-bold text-xs hover:bg-primary-hover transition inline-flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span>Unduh Surat (PDF)</span>
                                </a>
                                <a href="{{ route('pengajuan.show', $pengajuanAktif) }}" class="w-full sm:w-auto min-h-[44px] px-4 rounded-btn bg-canvas text-ink font-semibold text-xs border border-border-soft hover:bg-border-soft transition inline-flex items-center justify-center gap-2">
                                    <span>Lihat Detail</span>
                                </a>
                            @else
                                <a href="{{ route('pengajuan.show', $pengajuanAktif) }}" class="w-full sm:flex-1 min-h-[44px] px-4 rounded-btn bg-primary text-white font-bold text-xs hover:bg-primary-hover transition inline-flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Lihat Detail Pengajuan</span>
                                </a>
                            @endif
                            <a href="{{ route('riwayat') }}" class="w-full sm:w-auto min-h-[44px] px-4 rounded-btn bg-canvas text-ink font-semibold text-xs border border-border-soft hover:bg-border-soft transition inline-flex items-center justify-center gap-2">
                                <span>Semua Riwayat</span>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Empty State: Tidak ada pengajuan aktif -->
                    <div class="bg-surface rounded-card p-6 sm:p-8 shadow-card border border-border-soft text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-canvas text-ink-muted flex items-center justify-center mx-auto">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-ink">Tidak Ada Pengajuan Aktif</h3>
                        <p class="text-xs text-ink-muted max-w-md mx-auto leading-relaxed">
                            Saat ini Anda tidak memiliki pengajuan yang sedang diproses. Tekan tombol di atas untuk mengajukan surat keterangan baru.
                        </p>
                    </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: RIWAYAT TERBARU (5 Cols Desktop) -->
            <div class="lg:col-span-5 space-y-6">

                <div class="bg-surface rounded-card p-5 sm:p-6 shadow-card border border-border-soft space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                        <h3 class="text-base font-bold text-ink">Riwayat Pengajuan</h3>
                        <a href="{{ route('riwayat') }}" class="text-xs font-bold text-primary hover:underline">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($riwayatTerbaru as $r)
                            <div class="p-4 rounded-2xl bg-surface border border-border-soft hover:border-primary/40 hover:shadow-card transition duration-200 space-y-3">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <x-badge-layanan :kode="$r->layanan?->kode" variant="icon" size="sm" />
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-ink truncate">{{ $r->layanan?->nama_layanan }}</h4>
                                            <p class="text-[11px] font-mono text-ink-muted truncate">{{ $r->no_pengajuan }}</p>
                                        </div>
                                    </div>
                                    <x-badge-status-pengajuan :status="$r->status" />
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-ink-muted pt-2 border-t border-border-soft/60">
                                    <span>Diajukan: {{ tgl_indo($r->tgl_pengajuan) }}</span>
                                    @if($r->status === 'selesai' && $r->surat)
                                        <span class="font-mono font-semibold text-primary truncate max-w-[130px]" title="{{ $r->surat->no_surat }}">
                                            {{ $r->surat->no_surat }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Interactive Quick Action Buttons -->
                                <div class="flex items-center gap-2 pt-1">
                                    @if($r->status === 'selesai')
                                        <a href="{{ route('surat.unduh', $r) }}" class="flex-1 min-h-[44px] px-3.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-xs transition inline-flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                        <a href="{{ route('pengajuan.show', $r) }}" class="min-h-[44px] px-3.5 rounded-xl bg-canvas border border-border-soft text-ink hover:bg-border-soft text-xs font-semibold transition inline-flex items-center justify-center">
                                            Detail
                                        </a>
                                    @elseif($r->status === 'kurang_berkas')
                                        <a href="{{ route('pengajuan.show', $r) }}" class="flex-1 min-h-[44px] px-3.5 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 shadow-xs transition inline-flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Perbaiki Berkas</span>
                                        </a>
                                        <a href="{{ route('pengajuan.show', $r) }}" class="min-h-[44px] px-3.5 rounded-xl bg-canvas border border-border-soft text-ink hover:bg-border-soft text-xs font-semibold transition inline-flex items-center justify-center">
                                            Detail
                                        </a>
                                    @else
                                        <a href="{{ route('pengajuan.show', $r) }}" class="w-full min-h-[44px] px-3.5 rounded-xl bg-canvas border border-border-soft text-ink hover:bg-border-soft hover:text-primary text-xs font-semibold transition inline-flex items-center justify-between">
                                            <span>Lihat Rincian Pengajuan</span>
                                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-ink-muted">
                                Belum ada riwayat pengajuan surat.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Card Bantuan & Kontak WhatsApp Balai Kampung -->
                <div class="bg-surface rounded-card p-5 shadow-card border border-border-soft space-y-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c4.56 0 8.26 3.7 8.26 8.25 0 2.21-.86 4.29-2.42 5.85a8.216 8.216 0 01-5.84 2.42c-1.44 0-2.86-.38-4.12-1.11l-.3-.17-3.12.82.83-3.04-.19-.31a8.17 8.17 0 01-1.25-4.46c0-4.55 3.71-8.25 8.28-8.25m4.73 11.66c-.2-.1-.18-.1-.41-.21-.23-.12-1.36-.67-1.57-.75-.21-.08-.36-.12-.51.1-.15.23-.59.75-.72.9-.13.15-.26.17-.49.06-.23-.12-.97-.36-1.85-1.14-.68-.61-1.14-1.36-1.28-1.59-.13-.23-.01-.35.1-.47.1-.11.23-.28.35-.42.12-.14.15-.23.23-.39.08-.15.04-.29-.02-.41-.06-.11-.51-1.23-.7-1.69-.19-.44-.38-.38-.52-.39h-.44c-.15 0-.41.06-.62.29-.21.23-.82.8-.82 1.95 0 1.15.84 2.27.96 2.42.12.15 1.65 2.52 4 3.53.56.24 1 .38 1.34.49.56.18 1.07.15 1.47.09.45-.07 1.36-.56 1.55-1.1.19-.54.19-1 .13-1.1-.06-.11-.2-.17-.4-.28z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-ink">Bantuan Layanan Warga</h4>
                            <p class="text-[11px] text-ink-muted">Petugas siap melayani pertanyaan seputar persuratan digital</p>
                        </div>
                    </div>
                    <p class="text-xs text-ink-muted leading-relaxed">
                        Jika Anda membutuhkan bantuan terkait persyaratan atau berkas ditolak, silakan hubungi Balai Kampung Rukti Harjo pada jam kerja.
                    </p>
                    @if(!empty($profilKampung['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $profilKampung['whatsapp']) }}?text={{ urlencode('Halo Petugas Balai Kampung Rukti Harjo, saya ingin menanyakan perihal layanan persuratan warga.') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-full min-h-[44px] px-4 py-2.5 rounded-btn bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c4.56 0 8.26 3.7 8.26 8.25 0 2.21-.86 4.29-2.42 5.85a8.216 8.216 0 01-5.84 2.42c-1.44 0-2.86-.38-4.12-1.11l-.3-.17-3.12.82.83-3.04-.19-.31a8.17 8.17 0 01-1.25-4.46c0-4.55 3.71-8.25 8.28-8.25m4.73 11.66c-.2-.1-.18-.1-.41-.21-.23-.12-1.36-.67-1.57-.75-.21-.08-.36-.12-.51.1-.15.23-.59.75-.72.9-.13.15-.26.17-.49.06-.23-.12-.97-.36-1.85-1.14-.68-.61-1.14-1.36-1.28-1.59-.13-.23-.01-.35.1-.47.1-.11.23-.28.35-.42.12-.14.15-.23.23-.39.08-.15.04-.29-.02-.41-.06-.11-.51-1.23-.7-1.69-.19-.44-.38-.38-.52-.39h-.44c-.15 0-.41.06-.62.29-.21.23-.82.8-.82 1.95 0 1.15.84 2.27.96 2.42.12.15 1.65 2.52 4 3.53.56.24 1 .38 1.34.49.56.18 1.07.15 1.47.09.45-.07 1.36-.56 1.55-1.1.19-.54.19-1 .13-1.1-.06-.11-.2-.17-.4-.28z"/>
                            </svg>
                            <span>Hubungi Balai via WhatsApp</span>
                        </a>
                    @endif
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
