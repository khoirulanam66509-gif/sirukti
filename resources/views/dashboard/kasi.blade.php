<x-app-layout>
    <x-slot name="pageTitle">Beranda Pelayanan</x-slot>
    <x-slot name="title">Beranda Kasi — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-wide uppercase">
                        Portal Operasional
                    </span>
                    <span class="text-ink-muted text-xs">•</span>
                    <span class="text-xs text-ink-muted font-medium">Kasi Pelayanan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                    Beranda Pelayanan
                </h1>
                <p class="text-xs sm:text-sm text-ink-muted mt-0.5">
                    Ikhtisar antrean dan verifikasi permohonan surat keterangan warga Kampung Rukti Harjo
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold px-3.5 py-2 rounded-full bg-surface border border-border-soft text-ink shadow-xs">
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ tgl_indo(now()) }}</span>
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

        <!-- BANNER SAMBUTAN PETUGAS -->
        <div class="bg-surface rounded-card p-5 shadow-card border border-border-soft flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-secondary/15 flex items-center justify-center shrink-0 text-secondary">
                    <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-ink text-base">Selamat bertugas, {{ $user->nama }}</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-accent-light text-amber-900 text-[11px] font-bold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                            Siaga Pelayanan
                        </span>
                    </div>
                    <p class="text-xs text-ink-muted mt-0.5">
                        Terdapat <strong class="text-primary font-bold">{{ $menungguVerifikasi }} permohonan</strong> warga yang menunggu verifikasi berkas saat ini.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('kasi.pengajuan.index') }}" class="min-h-[44px] px-5 rounded-btn bg-primary text-white font-bold text-xs hover:bg-primary-hover shadow-sm transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Buka Antrean Lengkap</span>
                </a>
            </div>
        </div>

        <!-- 5 KPI SUMMARY CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5">
            <!-- KARTU 1 (HIGHLIGHTED / KARTU SOROTAN UTAMA HIJAU PRIMER) -->
            <div class="col-span-2 lg:col-span-1 relative overflow-hidden rounded-card bg-gradient-to-br from-primary to-[#3C7A4C] p-5 text-white shadow-featured flex flex-col justify-between min-h-[160px] transition-transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-accent text-ink text-[10px] font-extrabold">
                        Prioritas
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-extrabold tracking-tight">{{ $menungguVerifikasi }}</div>
                    <div class="text-xs font-bold mt-0.5">Menunggu Verifikasi</div>
                    <div class="text-[11px] text-white/75 mt-0.5">Perlu ditindaklanjuti</div>
                </div>
            </div>

            <!-- KARTU 2: Kurang Berkas -->
            <div class="rounded-card bg-surface p-5 shadow-card border border-border-soft flex flex-col justify-between min-h-[160px] transition-transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-canvas text-ink-muted text-[10px] font-semibold">
                        Perbaikan
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-extrabold text-ink tracking-tight">{{ $kurangBerkas }}</div>
                    <div class="text-xs font-bold text-ink mt-0.5">Kurang Berkas</div>
                    <div class="text-[11px] text-ink-muted mt-0.5">Menunggu unggah ulang</div>
                </div>
            </div>

            <!-- KARTU 3: Sedang Diproses -->
            <div class="rounded-card bg-surface p-5 shadow-card border border-border-soft flex flex-col justify-between min-h-[160px] transition-transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-canvas text-ink-muted text-[10px] font-semibold">
                        Diproses
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-extrabold text-ink tracking-tight">{{ $diproses }}</div>
                    <div class="text-xs font-bold text-ink mt-0.5">Sedang Diproses</div>
                    <div class="text-[11px] text-ink-muted mt-0.5">Penyusunan draf surat</div>
                </div>
            </div>

            <!-- KARTU 4: Menunggu TTD -->
            <div class="rounded-card bg-surface p-5 shadow-card border border-border-soft flex flex-col justify-between min-h-[160px] transition-transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-canvas text-ink-muted text-[10px] font-semibold">
                        Meja Kepala
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-extrabold text-ink tracking-tight">{{ $menungguTtd }}</div>
                    <div class="text-xs font-bold text-ink mt-0.5">Menunggu TTD</div>
                    <div class="text-[11px] text-ink-muted mt-0.5">Verifikasi Kakam</div>
                </div>
            </div>

            <!-- KARTU 5: Selesai Bulan Ini -->
            <div class="rounded-card bg-surface p-5 shadow-card border border-border-soft flex flex-col justify-between min-h-[160px] transition-transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-canvas text-ink-muted text-[10px] font-semibold">
                        Bulan Ini
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-extrabold text-ink tracking-tight">{{ $selesaiBulanIni }}</div>
                    <div class="text-xs font-bold text-ink mt-0.5">Selesai Terbit</div>
                    <div class="text-[11px] text-ink-muted mt-0.5">Surat resmi terbit</div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE / CARDS: ANTREAN PERMOHONAN MASUK -->
        <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-6 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-ink">Antrean Pengajuan Surat Terbaru</h3>
                    <p class="text-xs text-ink-muted">Permohonan masuk yang memerlukan tindakan dan telaah berkas</p>
                </div>
                <a href="{{ route('kasi.pengajuan.index') }}" class="text-xs font-bold text-primary hover:underline">
                    Lihat Semua Antrean →
                </a>
            </div>

            <!-- Mobile View: Cards (< md) -->
            <div class="block md:hidden space-y-3.5">
                @forelse ($antreanTerbaru as $p)
                    <div class="bg-surface rounded-2xl p-4 border border-border-soft shadow-xs hover:shadow-card hover:border-primary/40 transition duration-200 space-y-3">
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-badge-layanan :kode="$p->layanan?->kode" variant="icon" size="sm" />
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-ink truncate">{{ $p->warga?->pengguna?->nama ?? '-' }}</h4>
                                    <span class="font-mono text-xs font-bold text-primary block mt-0.5">{{ $p->no_pengajuan }}</span>
                                </div>
                            </div>
                            <x-badge-status-pengajuan :status="$p->status" />
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                            <div>
                                <span class="block text-[11px] text-ink-muted">Layanan</span>
                                <span class="font-medium text-ink truncate block mt-0.5">{{ $p->layanan?->nama_layanan }}</span>
                            </div>
                            <div>
                                <span class="block text-[11px] text-ink-muted">Dusun</span>
                                <span class="font-medium text-ink truncate block mt-0.5">{{ $p->warga?->dusun?->nama_dusun ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                            <span>Diajukan: <strong>{{ tgl_indo($p->tgl_pengajuan) }}</strong></span>
                            <a href="{{ route('kasi.pengajuan.show', $p) }}" class="min-h-[44px] px-4 rounded-btn bg-primary text-white font-bold text-xs hover:bg-primary-hover shadow-xs transition inline-flex items-center gap-1.5">
                                <span>Verifikasi</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-ink-muted">
                        Tidak ada antrean surat yang menunggu tindakan saat ini.
                    </div>
                @endforelse
            </div>

            <!-- Desktop View: Table (>= md) -->
            <div class="hidden md:block overflow-x-auto -mx-6">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-canvas/80 text-ink-muted text-xs uppercase tracking-wider font-semibold border-y border-border-soft">
                            <th class="py-3.5 pl-6 pr-4">No. Registrasi &amp; Pemohon</th>
                            <th class="py-3.5 px-4">Jenis Layanan</th>
                            <th class="py-3.5 px-4">Dusun</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Tgl Pengajuan</th>
                            <th class="py-3.5 pr-6 pl-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-soft text-sm">
                        @forelse ($antreanTerbaru as $p)
                            <tr class="hover:bg-canvas/40 transition">
                                <td class="py-4 pl-6 pr-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-ink leading-snug">{{ $p->warga?->pengguna?->nama ?? '-' }}</span>
                                        <span class="text-xs text-ink-muted font-mono">{{ $p->no_pengajuan }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-medium text-ink">
                                    <div class="flex items-center gap-2">
                                        <x-badge-layanan :kode="$p->layanan?->kode" variant="icon" size="sm" />
                                        <span>{{ $p->layanan?->nama_layanan }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-xs text-ink-muted">
                                    {{ $p->warga?->dusun?->nama_dusun ?? '-' }}
                                </td>
                                <td class="py-4 px-4">
                                    <x-badge-status-pengajuan :status="$p->status" />
                                </td>
                                <td class="py-4 px-4 text-xs text-ink-muted">
                                    {{ tgl_indo($p->tgl_pengajuan) }}
                                </td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    <a href="{{ route('kasi.pengajuan.show', $p) }}" class="min-h-[36px] px-3.5 py-1.5 rounded-full bg-primary text-white font-bold text-xs hover:bg-primary-hover transition inline-flex items-center gap-1.5 shadow-xs">
                                        <span>Periksa</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-ink-muted text-sm">
                                    Tidak ada antrean surat yang menunggu tindakan saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
