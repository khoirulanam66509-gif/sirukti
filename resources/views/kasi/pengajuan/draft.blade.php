<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Pratinjau Draft Surat</x-slot>
    <x-slot name="title">Pratinjau Draft — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs font-semibold text-ink-muted">
                    <a href="{{ route('kasi.pengajuan.index') }}" class="hover:text-primary transition">Antrean</a>
                    <span>/</span>
                    <a href="{{ route('kasi.pengajuan.show', $pengajuan) }}" class="font-mono hover:text-primary transition">{{ $pengajuan->no_pengajuan }}</a>
                    <span>/</span>
                    <span class="text-ink">Pratinjau Draft</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                        Pratinjau Draft: {{ $pengajuan->layanan->nama_layanan }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        Draft Belum Diterbitkan
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('kasi.pengajuan.show', $pengajuan) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border-soft bg-surface hover:bg-canvas text-xs font-bold text-ink transition shadow-sm">
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Detail</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ showPreviewMobile: false }" class="space-y-6">

        <!-- Kartu Ringkasan Dokumen & Aksi PDF Mobile-Optimized -->
        <div class="p-5 rounded-card bg-surface border border-border-soft shadow-card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0 border border-primary/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-ink">Dokumen Draft Surat Kertas A4</h3>
                            <x-badge-layanan :kode="$pengajuan->layanan->kode" size="sm" />
                        </div>
                        <p class="text-xs text-ink-muted mt-0.5">
                            Simulasi tata letak surat resmi sebelum ditandatangani oleh Kepala Kampung.
                        </p>
                    </div>
                </div>

                <!-- Action Button Buka Layar Penuh -->
                <div class="flex items-center gap-2 self-stretch sm:self-auto">
                    <a href="{{ route('kasi.pengajuan.draft.pdf', $pengajuan) }}"
                       target="_blank"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold transition shadow-sm hover:bg-primary-hover active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Buka PDF Layar Penuh</span>
                    </a>
                </div>
            </div>

            <!-- Detail Grid Ringkas Dokumen (Mobile & Desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-border-soft/60 text-xs">
                <div>
                    <span class="text-ink-muted block text-[11px]">No. Registrasi:</span>
                    <span class="font-mono font-bold text-ink">{{ $pengajuan->no_pengajuan }}</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">Pemohon:</span>
                    <span class="font-semibold text-ink truncate block">{{ $pengajuan->warga->pengguna->nama }}</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">NIK:</span>
                    <span class="font-mono text-ink">{{ samarkan_nik($pengajuan->warga->nik) }}</span>
                </div>
                <div>
                    <span class="text-ink-muted block text-[11px]">Tanggal Pengajuan:</span>
                    <span class="text-ink font-medium">{{ $pengajuan->tgl_pengajuan ? $pengajuan->tgl_pengajuan->format('d/m/Y') : '-' }}</span>
                </div>
            </div>

            <!-- Toggle Pratinjau Mobile (< md) -->
            <div class="block md:hidden pt-2 border-t border-border-soft/60">
                <button
                    type="button"
                    @click="showPreviewMobile = !showPreviewMobile"
                    class="w-full py-2.5 px-4 rounded-xl bg-canvas border border-border-soft text-ink text-xs font-semibold flex items-center justify-center gap-2 active:scale-98 transition"
                >
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span x-text="showPreviewMobile ? 'Sembunyikan Lembar Pratinjau PDF' : 'Tampilkan Lembar Pratinjau di Halaman Ini'">Tampilkan Lembar Pratinjau di Halaman Ini</span>
                    <svg class="w-3.5 h-3.5 transform transition-transform" :class="showPreviewMobile ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Frame Kertas A4 Pratinjau PDF (Desktop selalu tampil, Mobile dikontrol toggle) -->
        <div :class="showPreviewMobile ? 'block' : 'hidden md:block'" class="bg-surface rounded-card p-3 sm:p-6 border border-border-soft shadow-card flex justify-center transition-all">
            <div class="w-full max-w-4xl bg-canvas rounded-2xl border border-border-soft overflow-hidden shadow-inner flex flex-col items-center">
                <iframe src="{{ route('kasi.pengajuan.draft.pdf', $pengajuan) }}"
                        class="w-full h-[650px] sm:h-[850px] border-0 rounded-2xl"
                        title="Pratinjau Draft Surat Resmi"></iframe>
            </div>
        </div>

        <!-- Sticky Bottom Action Bar -->
        <div class="fixed bottom-0 inset-x-0 lg:left-64 bg-surface/95 backdrop-blur-md border-t border-border-soft p-4 z-40 shadow-2xl safe-bottom">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-ink-muted">
                    Pemohon: <strong class="text-ink">{{ $pengajuan->warga->pengguna->nama }}</strong> (NIK: {{ $pengajuan->warga->nik }})
                </div>

                <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('kasi.pengajuan.show', $pengajuan) }}"
                       class="flex-1 sm:flex-none text-center px-4 py-2.5 rounded-xl border border-border-soft hover:bg-canvas text-xs font-bold text-ink transition">
                        Kembali ke Detail
                    </a>

                    @if($pengajuan->status === 'diproses')
                        <form method="POST" action="{{ route('kasi.pengajuan.verifikasi', $pengajuan) }}" class="flex-1 sm:flex-none">
                            @csrf
                            <input type="hidden" name="aksi" value="ajukan_ttd">
                            <button type="submit"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold transition shadow-featured flex items-center justify-center gap-1.5">
                                <span>Ajukan ke Kepala</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
