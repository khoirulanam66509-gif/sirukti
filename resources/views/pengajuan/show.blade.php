<x-app-layout>
    <x-slot name="pageTitle">Detail Pengajuan #{{ $pengajuan->no_pengajuan }}</x-slot>
    <x-slot name="title">Detail Pengajuan #{{ $pengajuan->no_pengajuan }} — SIRUKTI</x-slot>

    @php
        $berkasPerluPerbaikan = $pengajuan->berkas->where('status_verifikasi', 'perlu_perbaikan');
        $adaPerluPerbaikan = $berkasPerluPerbaikan->isNotEmpty() && $pengajuan->status === 'kurang_berkas';
        $isSelesai = $pengajuan->status === 'selesai';
        $profilKampung = \App\Models\Pengaturan::ambil('profil_kampung', []);
        $waKantor = preg_replace('/[^0-9]/', '', $profilKampung['whatsapp'] ?? '6281234567890');
        $waText = urlencode("Halo Petugas Balai Kampung Rukti Harjo, saya ingin menanyakan perihal permohonan surat {$pengajuan->layanan?->nama_layanan} dengan nomor pengajuan #{$pengajuan->no_pengajuan}.");
    @endphp

    <div
        x-data="{
            activeTab: '{{ $adaPerluPerbaikan ? 'berkas' : 'status' }}',
            currentStatus: '{{ $pengajuan->status }}',
            isPolling: {{ in_array($pengajuan->status, ['selesai', 'ditolak'], true) ? 'false' : 'true' }},
            statusUpdated: false,
            statusLabelNew: '',
            statusPollTimer: null,
            init() {
                if (this.isPolling) {
                    this.statusPollTimer = setInterval(() => {
                        this.periksaStatusTerkini();
                    }, 30000);
                }
            },
            async periksaStatusTerkini() {
                try {
                    const response = await fetch('{{ route('pengajuan.status-json', $pengajuan) }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (data && data.status && data.status !== this.currentStatus) {
                        this.currentStatus = data.status;
                        this.statusLabelNew = data.status_label || data.status;
                        this.statusUpdated = true;
                        if (this.statusPollTimer) {
                            clearInterval(this.statusPollTimer);
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 2200);
                    }
                } catch (e) {
                    console.debug('Polling status permohonan:', e);
                }
            }
        }"
        class="space-y-6 max-w-6xl mx-auto"
    >
        <!-- Toast Notifikasi Pembaruan Status Otomatis -->
        <div
            x-show="statusUpdated"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-12px]"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed top-20 right-4 left-4 sm:left-auto sm:max-w-md z-50 p-4 rounded-2xl bg-primary text-white shadow-2xl border border-primary-hover flex items-center gap-3.5"
        >
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 animate-spin text-accent" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
            <div class="flex-1 text-xs space-y-0.5">
                <p class="font-bold text-sm">Status Permohonan Diperbarui!</p>
                <p class="text-white/90">
                    Status berubah menjadi <strong x-text="statusLabelNew"></strong>. Halaman sedang disegarkan...
                </p>
            </div>
        </div>
        <!-- Top Navigation & Breadcrumbs -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs">
                    <a href="{{ route('riwayat') }}" class="text-ink-muted hover:text-primary flex items-center gap-1 font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Riwayat Pengajuan</span>
                    </a>
                    <span class="text-ink-muted">/</span>
                    <span class="text-ink-muted font-mono font-medium">#{{ $pengajuan->no_pengajuan }}</span>
                </nav>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <x-badge-layanan :kode="$pengajuan->layanan?->kode" variant="icon" size="sm" />
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">
                        {{ $pengajuan->layanan?->nama_layanan ?? 'Surat Administrasi' }}
                    </h1>
                    <x-badge-status-pengajuan :status="$pengajuan->status" />
                </div>
            </div>

            <!-- Contextual Actions: Diajukan Badge & Hubungi Balai WA & Batalkan -->
            <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
                @if(!in_array($pengajuan->status, ['selesai', 'ditolak'], true))
                    <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-surface border border-border-soft text-[11px] font-semibold text-ink-muted shadow-xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Auto-refresh (30d)</span>
                    </div>
                @endif

                <div class="flex items-center gap-2 text-xs text-ink-muted bg-surface border border-border-soft px-3.5 py-2 rounded-xl shadow-xs">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Diajukan: <strong>{{ tgl_indo($pengajuan->tgl_pengajuan, true) }}</strong></span>
                </div>

                <a
                    href="https://wa.me/{{ $waKantor }}?text={{ $waText }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition text-xs font-bold shadow-xs min-h-[44px]"
                    title="Hubungi Petugas Balai via WhatsApp"
                >
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/>
                    </svg>
                    <span>Hubungi Balai</span>
                </a>

                @can('batalkan', $pengajuan)
                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'konfirmasi-batalkan')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition text-xs font-bold shadow-xs min-h-[44px]"
                        title="Batalkan permohonan surat ini"
                    >
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batalkan</span>
                    </button>
                @endcan
            </div>
        </div>

        <!-- Warning Alert Banner (Status: Kurang Berkas) -->
        @if($adaPerluPerbaikan)
            <div class="relative overflow-hidden rounded-card bg-amber-50 border border-amber-300 p-4 sm:p-5 shadow-card">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L1 21h22L12 2zm1 14h-2v-2h2v2zm0-4h-2V9h2v3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-amber-950">Berkas Perlu Diperbaiki</h2>
                            <span class="bg-amber-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                Tindakan Wajib
                            </span>
                        </div>
                        <p class="text-xs text-amber-900 leading-relaxed">
                            Silakan unggah berkas pengganti pada kolom yang ditandai di bawah agar permohonan dapat segera dilanjutkan.
                        </p>
                        @if($pengajuan->catatan_petugas)
                            <div class="p-2.5 bg-white/90 rounded-xl border border-amber-200/90 mt-2 text-xs text-amber-950">
                                <strong>Catatan Petugas:</strong> &ldquo;{{ $pengajuan->catatan_petugas }}&rdquo;
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Rejection / Cancellation Alert Banner (Status: Ditolak / Dibatalkan) -->
        @if($pengajuan->status === 'ditolak')
            <div class="relative overflow-hidden rounded-card bg-red-50 border border-red-200 p-4 sm:p-5 shadow-card">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 border border-red-200 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-red-950">Permohonan Ditutup / Dibatalkan</h2>
                            <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                Selesai
                            </span>
                        </div>
                        <p class="text-xs text-red-900 leading-relaxed">
                            {{ $pengajuan->catatan_petugas ?? 'Permohonan surat ini telah ditutup atau dibatalkan.' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Banner (Status: Selesai & Terbit) -->
        @if($isSelesai)
            <div class="relative overflow-hidden rounded-card bg-emerald-50 border border-emerald-300 p-4 sm:p-5 shadow-card">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <h2 class="text-base font-bold text-emerald-950">Surat Resmi Telah Terbit</h2>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Dokumen telah ditandatangani secara sah dan siap diunduh.
                            </p>
                            @if($pengajuan->surat)
                                <p class="text-xs font-mono font-bold text-primary pt-0.5">
                                    No. Surat: {{ $pengajuan->surat->no_surat }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="w-full sm:w-auto shrink-0">
                        <a
                            href="{{ route('surat.unduh', $pengajuan) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 min-h-[44px] rounded-btn bg-primary text-white font-bold text-sm shadow-featured hover:bg-primary-hover active:scale-95 transition"
                        >
                            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Surat (PDF)</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Segmented Tabs Navigasi Mobile (< lg) -->
        <div class="lg:hidden sticky top-16 z-20 bg-canvas/95 backdrop-blur-md pt-1 pb-2">
            <div class="grid grid-cols-3 p-1 bg-surface border border-border-soft rounded-2xl shadow-xs gap-1 text-xs font-bold">
                <button
                    type="button"
                    @click="activeTab = 'status'"
                    :class="activeTab === 'status' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="min-h-[44px] py-2 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="truncate">Riwayat</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'berkas'"
                    :class="activeTab === 'berkas' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="min-h-[44px] py-2 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate">Berkas</span>
                    @if($adaPerluPerbaikan)
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                    @endif
                </button>
                <button
                    type="button"
                    @click="activeTab = 'pemohon'"
                    :class="activeTab === 'pemohon' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="min-h-[44px] py-2 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="truncate">Pemohon</span>
                </button>
            </div>
        </div>

        <!-- Form Wrapper jika ada berkas yang perlu diunggah ulang -->
        <form
            id="form-unggah-ulang"
            method="POST"
            action="{{ route('pengajuan.unggah-ulang', $pengajuan) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <!-- Two-Column Workspace -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left Column: Timeline & Data Pemohon (5 Cols Desktop) -->
                <div
                    class="lg:col-span-5 space-y-6"
                    :class="(activeTab === 'status' || activeTab === 'pemohon') ? 'block' : 'hidden lg:block'"
                >
                    <!-- Timeline Progres Card -->
                    <div
                        class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card space-y-4"
                        :class="activeTab === 'status' ? 'block' : 'hidden lg:block'"
                    >
                        <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-primary-light text-primary flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-ink">Riwayat &amp; Progres Surat</h3>
                            </div>
                            <span class="text-xs font-semibold text-ink-muted">
                                {{ $pengajuan->log->count() }} Tahap Tercatat
                            </span>
                        </div>

                        <!-- Vertical Timeline Tracker Component -->
                        <x-timeline-tracker :logs="$pengajuan->log" />

                        @can('batalkan', $pengajuan)
                            <div class="mt-4 pt-3 border-t border-border-soft flex items-center justify-between gap-3 text-xs">
                                <div class="text-ink-muted">
                                    <span class="font-bold text-ink">Ingin membatalkan surat ini?</span>
                                    <p class="text-[11px]">Pembatalan hanya dapat dilakukan saat status masih Menunggu Verifikasi.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="$dispatch('open-modal', 'konfirmasi-batalkan')"
                                    class="min-h-[44px] px-3.5 py-2 rounded-lg bg-surface border border-red-200 text-red-700 hover:bg-red-50 font-bold transition text-xs shrink-0 shadow-xs"
                                >
                                    Batalkan Pengajuan
                                </button>
                            </div>
                        @endcan
                    </div>

                    <!-- Identitas Pemohon Card -->
                    <div
                        class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card space-y-3"
                        :class="activeTab === 'pemohon' ? 'block' : 'hidden lg:block'"
                    >
                        <div class="flex items-center gap-2.5 pb-2 border-b border-border-soft">
                            <div class="w-8 h-8 rounded-xl bg-canvas border border-border-soft text-primary flex items-center justify-center font-bold">
                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-ink">Identitas Pemohon</h3>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-baseline justify-between border-b border-border-soft pb-1.5">
                                <span class="text-ink-muted">Nama:</span>
                                <span class="font-bold text-ink text-sm">{{ $userPemohon?->nama ?? '-' }}</span>
                            </div>
                            <div class="flex items-baseline justify-between border-b border-border-soft pb-1.5">
                                <span class="text-ink-muted">NIK:</span>
                                <span class="font-mono font-bold text-ink text-sm">{{ $warga?->nik ?? '-' }}</span>
                            </div>
                            <div class="flex items-baseline justify-between border-b border-border-soft pb-1.5">
                                <span class="text-ink-muted">No. KK:</span>
                                <span class="font-mono text-ink">{{ $warga?->no_kk ?? '-' }}</span>
                            </div>
                            <div class="flex items-baseline justify-between border-b border-border-soft pb-1.5">
                                <span class="text-ink-muted">Nomor WhatsApp:</span>
                                <span class="font-mono font-bold text-ink">{{ $userPemohon?->no_hp ?? '-' }}</span>
                            </div>
                            <div class="pt-1">
                                <span class="text-ink-muted block mb-0.5">Alamat Domisili:</span>
                                <span class="font-medium text-ink leading-relaxed">
                                    {{ $warga?->alamat }}, RT {{ $warga?->rt }} / RW {{ $warga?->rw }}, {{ $warga?->dusun?->nama_dusun }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Bantuan Layanan Balai (WhatsApp) -->
                    <div
                        class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card flex items-center justify-between gap-3"
                        :class="(activeTab === 'status' || activeTab === 'pemohon') ? 'flex' : 'hidden lg:flex'"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-ink">Bantuan Pelayanan</h4>
                                <p class="text-[11px] text-ink-muted">Tanyakan progres ke Balai Kampung</p>
                            </div>
                        </div>

                        <a
                            href="https://wa.me/{{ $waKantor }}?text={{ $waText }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="min-h-[44px] px-4 rounded-btn bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-xs transition inline-flex items-center justify-center gap-1.5 shrink-0"
                        >
                            <span>Chat WA</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Isian Formulir & Berkas Persyaratan (7 Cols Desktop) -->
                <div
                    class="lg:col-span-7 space-y-6"
                    :class="activeTab === 'berkas' ? 'block' : 'hidden lg:block'"
                >

                    <!-- Card 1: Rincian Isian Formulir -->
                    <div class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                            <h3 class="text-base font-bold text-ink flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Rincian Isian Permohonan</span>
                            </h3>
                        </div>

                        <div class="bg-canvas border border-border-soft rounded-2xl p-4 text-xs space-y-2.5">
                            @php
                                $dataIsian = $pengajuan->data_isian ?? [];
                                $fieldLabels = [
                                    'keperluan' => 'Keperluan Surat',
                                    'alamat_domisili' => 'Alamat Domisili',
                                    'lama_tinggal_tahun' => 'Lama Menetap',
                                    'tujuan' => 'Tujuan Permohonan',
                                    'penghasilan_per_bulan' => 'Penghasilan per Bulan',
                                    'jumlah_tanggungan' => 'Jumlah Tanggungan',
                                    'nama_usaha' => 'Nama Usaha',
                                    'jenis_usaha' => 'Jenis Usaha',
                                    'alamat_usaha' => 'Lokasi Usaha',
                                    'lama_usaha_tahun' => 'Lama Usaha Berjalan',
                                ];
                            @endphp

                            @forelse($dataIsian as $key => $val)
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 py-1.5 border-b border-border-soft last:border-0">
                                    <span class="text-ink-muted capitalize font-medium">
                                        {{ $fieldLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}
                                    </span>
                                    <span class="font-bold text-ink text-sm sm:text-right">
                                        @if($key === 'penghasilan_per_bulan')
                                            {{ rupiah($val) }}
                                        @elseif(str_contains($key, 'lama') || str_contains($key, 'tahun'))
                                            {{ $val }} Tahun
                                        @elseif($key === 'jumlah_tanggungan')
                                            {{ $val }} Jiwa
                                        @else
                                            {{ $val }}
                                        @endif
                                    </span>
                                </div>
                            @empty
                                <p class="text-ink-muted text-center py-2">Tidak ada data isian khusus.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Card 2: Berkas Persyaratan Terlampir -->
                    <div class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                            <div>
                                <h3 class="text-base font-bold text-ink flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <span>Berkas Persyaratan Dokumen</span>
                                </h3>
                                <p class="text-xs text-ink-muted mt-0.5">Daftar lampiran berkas yang diserahkan untuk proses verifikasi desa.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($pengajuan->berkas as $berkas)
                                @php
                                    $perluPerbaikanItem = $berkas->status_verifikasi === 'perlu_perbaikan' && $pengajuan->status === 'kurang_berkas';
                                @endphp

                                <div class="rounded-2xl border p-4 transition {{ $perluPerbaikanItem ? 'bg-amber-50/70 border-amber-300' : 'bg-canvas border-border-soft' }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="w-11 h-11 rounded-xl bg-surface border border-border-soft flex items-center justify-center shrink-0 shadow-sm">
                                                @if(str_starts_with($berkas->mime, 'image/'))
                                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                                                    </svg>
                                                @endif
                                            </div>

                                            <div class="truncate text-xs">
                                                <h4 class="font-bold text-ink text-sm truncate">
                                                    {{ $berkas->syarat?->nama_syarat ?? 'Persyaratan' }}
                                                </h4>
                                                <p class="text-ink-muted truncate">
                                                    {{ $berkas->nama_file_asli }} • {{ $berkas->ukuran_kb }} KB
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                                            <x-badge-status-berkas :status="$berkas->status_verifikasi" />

                                            <a
                                                href="{{ route('pengajuan.berkas', [$pengajuan, $berkas]) }}"
                                                target="_blank"
                                                class="min-h-[44px] px-3.5 py-2 rounded-lg bg-surface border border-border-soft hover:bg-canvas text-xs font-bold text-ink transition shadow-xs inline-flex items-center justify-center"
                                            >
                                                Lihat
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Catatan Perbaikan Petugas & Slot Unggah Ulang -->
                                    @if($perluPerbaikanItem)
                                        <div class="mt-3 pt-3 border-t border-amber-200/80 space-y-2.5">
                                            @if($berkas->catatan)
                                                <div class="p-2.5 rounded-xl bg-amber-100/70 border border-amber-200 text-xs text-amber-950 flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <div>
                                                        <strong>Alasan Perbaikan:</strong> {{ $berkas->catatan }}
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-surface p-3 rounded-xl border border-amber-300 min-h-[44px]">
                                                <label class="text-xs font-bold text-ink">
                                                    Unggah Foto Pengganti <span class="text-error">*</span>
                                                </label>
                                                <input
                                                    type="file"
                                                    name="berkas[{{ $berkas->id_berkas }}]"
                                                    accept="image/jpeg,image/png,application/pdf"
                                                    required
                                                    class="text-xs text-ink file:mr-2 file:py-2 file:px-3.5 file:min-h-[38px] file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-hover cursor-pointer"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Tombol Kirim Ulang Berkas Pengajuan -->
                        @if($adaPerluPerbaikan)
                            <div class="pt-4 border-t border-border-soft flex flex-col sm:flex-row items-center justify-between gap-3">
                                <p class="text-xs text-ink-muted">
                                    Pastikan seluruh berkas yang diminta telah dipilih penggantinya sebelum menekan tombol kirim ulang.
                                </p>
                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-7 min-h-[48px] rounded-btn bg-primary text-white font-bold text-sm shadow-featured hover:bg-primary-hover active:scale-95 transition flex items-center justify-center gap-2 shrink-0"
                                >
                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Kirim Ulang Permohonan</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        @can('batalkan', $pengajuan)
            <!-- Modal Konfirmasi Pembatalan Pengajuan oleh Warga -->
            <x-modal-dialog name="konfirmasi-batalkan" title="Batalkan Permohonan Surat" maxWidth="md">
                <form method="POST" action="{{ route('pengajuan.batalkan', $pengajuan) }}" class="space-y-4">
                    @csrf
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-900 leading-relaxed">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            Apakah Anda yakin ingin membatalkan permohonan <strong>#{{ $pengajuan->no_pengajuan }}</strong>? Status permohonan akan ditutup dan dicatat dalam riwayat. Anda dapat mengajukan permohonan baru kembali setelah ini.
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="alasan-batal" class="text-xs font-bold text-ink">
                            Alasan Pembatalan <span class="text-ink-muted font-normal">(Opsional)</span>
                        </label>
                        <textarea
                            id="alasan-batal"
                            name="alasan"
                            rows="3"
                            maxlength="255"
                            placeholder="Contoh: Salah memilih jenis permohonan / terjadi perubahan data keperluan..."
                            class="w-full text-xs rounded-xl border border-border-soft p-3 text-ink focus:border-red-500 focus:ring-2 focus:ring-red-500/20 bg-surface"
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <button
                            type="button"
                            @click="$dispatch('close-modal', 'konfirmasi-batalkan')"
                            class="min-h-[44px] px-4 py-2 rounded-xl bg-canvas hover:bg-gray-200 border border-border-soft text-xs font-bold text-ink transition"
                        >
                            Kembali
                        </button>
                        <button
                            type="submit"
                            class="min-h-[44px] px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 active:scale-95 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Ya, Batalkan Permohonan</span>
                        </button>
                    </div>
                </form>
            </x-modal-dialog>
        @endcan
    </div>
</x-app-layout>
