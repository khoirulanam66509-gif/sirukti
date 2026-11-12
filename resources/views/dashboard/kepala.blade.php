<x-app-layout>
    <x-slot name="pageTitle">Beranda Kepala</x-slot>
    <x-slot name="title">Beranda Kepala — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Portal Otorisasi Eksekutif Desa
                    </span>
                    <span class="text-ink-muted text-xs">•</span>
                    <span class="text-xs text-ink-muted font-medium">Kepala Kampung Rukti Harjo</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                    Persetujuan Naskah Dinas
                </h1>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Pantau dan tandatangani permohonan surat keterangan warga yang telah diverifikasi lengkap oleh Kasi Pelayanan.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('kepala.profil.edit') }}" class="min-h-[44px] px-4 py-2 rounded-btn bg-surface text-ink text-xs font-semibold shadow-xs border border-border-soft hover:bg-canvas transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Kelola Tanda Tangan</span>
                </a>
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

        @if (session('error'))
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- 2 KPI CARDS IN PROMINENT TOP ROW -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">
            <!-- Card 1: Menunggu TTD (Hero Primary Green) -->
            <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-primary to-[#3C7A4C] text-white p-6 sm:p-7 shadow-featured flex flex-col justify-between min-h-[180px]">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center text-white backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-accent text-ink text-xs font-extrabold shadow-sm">
                        Siap Ditandatangani
                    </span>
                </div>
                <div class="mt-6">
                    <div class="text-4xl font-extrabold tracking-tight">{{ $menungguTtd }}</div>
                    <div class="text-sm font-bold mt-1">Menunggu Tanda Tangan Digital</div>
                    <div class="text-xs text-white/80 mt-0.5">Permohonan terverifikasi siap disahkan</div>
                </div>
            </div>

            <!-- Card 2: Selesai Bulan Ini -->
            <div class="rounded-card bg-surface p-6 sm:p-7 shadow-card border border-border-soft flex flex-col justify-between min-h-[180px]">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-canvas text-ink-muted text-xs font-semibold">
                        Bulan Berjalan
                    </span>
                </div>
                <div class="mt-6">
                    <div class="text-4xl font-extrabold text-ink tracking-tight">{{ $selesaiBulanIni }}</div>
                    <div class="text-sm font-bold text-ink mt-1">Surat Disetujui Bulan Ini</div>
                    <div class="text-xs text-ink-muted mt-0.5">Total surat resmi terbit tahun {{ now()->year }}: {{ $totalTahunIni }} berkas</div>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE / CARDS: DAFTAR MENUNGGU TTD -->
        <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-6 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-border-soft">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-ink">Permohonan Menunggu Pengesahan</h3>
                    <p class="text-xs text-ink-muted">Daftar berkas yang telah diverifikasi Kasi dan siap diterbitkan</p>
                </div>
                <a href="{{ route('kepala.persetujuan.index') }}" class="text-xs font-bold text-primary hover:underline">
                    Buka Halaman Persetujuan →
                </a>
            </div>

            <!-- Mobile View: Cards (< md) -->
            <div class="block md:hidden space-y-3.5">
                @forelse ($daftarMenungguTtd as $p)
                    <div class="bg-surface rounded-2xl p-4 border border-border-soft shadow-xs hover:shadow-card hover:border-primary/40 transition duration-200 space-y-3">
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-badge-layanan :kode="$p->layanan?->kode" variant="icon" size="sm" />
                                <div class="min-w-0">
                                    <h4 class="font-bold text-sm text-ink truncate">{{ $p->warga?->pengguna?->nama ?? '-' }}</h4>
                                    <span class="font-mono text-xs font-bold text-primary block mt-0.5">{{ $p->no_pengajuan }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                Siap TTD
                            </span>
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
                            <a href="{{ route('kepala.persetujuan.show', $p) }}" class="min-h-[44px] px-4 rounded-btn bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shadow-xs">
                                <span>Tinjau &amp; Sahkan</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-ink-muted">
                        Tidak ada berkas yang menunggu tanda tangan saat ini.
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
                            <th class="py-3.5 px-4">Tgl Pengajuan</th>
                            <th class="py-3.5 pr-6 pl-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-soft text-sm">
                        @forelse ($daftarMenungguTtd as $p)
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
                                <td class="py-4 px-4 text-xs text-ink-muted">
                                    {{ tgl_indo($p->tgl_pengajuan) }}
                                </td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    <a href="{{ route('kepala.persetujuan.show', $p) }}" class="min-h-[36px] px-4 py-1.5 rounded-full bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shadow-xs">
                                        <span>Tinjau &amp; Sahkan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-ink-muted text-sm">
                                    Tidak ada permohonan surat yang menunggu tanda tangan digital saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
