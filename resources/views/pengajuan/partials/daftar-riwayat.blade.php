<!-- Daftar Pengajuan: Kartu di Mobile (< md) & Tabel di Desktop (>= md) -->
<div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden">
    @if($daftarPengajuan->isEmpty())
        <div class="p-8 sm:p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-canvas border border-border-soft flex items-center justify-center mx-auto text-ink-muted">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-ink">Belum Ada Permohonan Surat</h3>
                <p class="text-xs sm:text-sm text-ink-muted max-w-sm mx-auto">
                    @if($searchQuery || $statusFilter)
                        Tidak ditemukan permohonan yang sesuai dengan filter atau kata kunci pencarian Anda.
                    @else
                        Anda belum pernah membuat pengajuan surat. Ajukan permohonan surat administrasi pertama Anda secara mudah.
                    @endif
                </p>
            </div>
            @if($searchQuery || $statusFilter)
                <a href="{{ route('riwayat') }}" data-reset-filter="true" class="inline-flex items-center gap-2 text-xs text-primary font-bold hover:underline cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter & Pencarian</span>
                </a>
            @else
                <a href="{{ route('pengajuan.buat') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-btn bg-primary text-white text-xs font-bold shadow-sm hover:bg-primary-hover">
                    Buat Pengajuan Baru
                </a>
            @endif
        </div>
    @else
        <!-- 1. Tampilan Kartu untuk Mobile (< md) -->
        <div class="md:hidden p-4 space-y-3.5 bg-canvas/30">
            @foreach($daftarPengajuan as $pengajuan)
                <div class="p-4 rounded-2xl bg-surface border border-border-soft hover:border-primary/40 hover:shadow-card transition duration-200 space-y-3.5 shadow-xs">
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-badge-layanan :kode="$pengajuan->layanan?->kode" variant="icon" size="md" />
                            <div class="min-w-0">
                                <h4 class="font-bold text-ink text-sm truncate">
                                    {{ $pengajuan->layanan?->nama_layanan ?? 'Surat Administrasi' }}
                                </h4>
                                <span class="font-mono text-xs font-bold text-primary block mt-0.5">
                                    {{ $pengajuan->no_pengajuan }}
                                </span>
                            </div>
                        </div>
                        <x-badge-status-pengajuan :status="$pengajuan->status" />
                    </div>

                    <div class="flex items-center justify-between text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                        <span>Diajukan: <strong>{{ tgl_indo($pengajuan->tgl_pengajuan) }}</strong></span>
                        @if($pengajuan->status === 'selesai' && $pengajuan->surat)
                            <span class="font-mono font-semibold text-primary truncate max-w-[150px]" title="{{ $pengajuan->surat->no_surat }}">
                                {{ $pengajuan->surat->no_surat }}
                            </span>
                        @endif
                    </div>

                    <!-- Interactive Action Buttons (Target Sentuh >= 44px) -->
                    <div class="flex items-center gap-2 pt-1">
                        @if($pengajuan->status === 'selesai')
                            <a
                                href="{{ route('surat.unduh', $pengajuan) }}"
                                class="flex-1 min-h-[44px] px-4 rounded-btn bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-xs transition inline-flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh PDF</span>
                            </a>
                            <a
                                href="{{ route('pengajuan.show', $pengajuan) }}"
                                class="min-h-[44px] px-4 rounded-btn bg-canvas border border-border-soft text-ink hover:bg-border-soft text-xs font-semibold transition inline-flex items-center justify-center"
                            >
                                Detail
                            </a>
                        @elseif($pengajuan->status === 'kurang_berkas')
                            <a
                                href="{{ route('pengajuan.show', $pengajuan) }}"
                                class="flex-1 min-h-[44px] px-4 rounded-btn bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 shadow-xs transition inline-flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Perbaiki Berkas</span>
                            </a>
                            <a
                                href="{{ route('pengajuan.show', $pengajuan) }}"
                                class="min-h-[44px] px-4 rounded-btn bg-canvas border border-border-soft text-ink hover:bg-border-soft text-xs font-semibold transition inline-flex items-center justify-center"
                            >
                                Detail
                            </a>
                        @else
                            <a
                                href="{{ route('pengajuan.show', $pengajuan) }}"
                                class="w-full min-h-[44px] px-4 rounded-btn bg-canvas border border-border-soft text-ink hover:bg-border-soft hover:text-primary text-xs font-bold transition inline-flex items-center justify-between"
                            >
                                <span>Lihat Detail Pengajuan</span>
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. Tampilan Tabel untuk Desktop (>= md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-canvas text-ink-muted text-xs uppercase tracking-wider border-b border-border-soft">
                        <th class="py-3.5 px-6 font-bold">No. Pengajuan</th>
                        <th class="py-3.5 px-6 font-bold">Jenis Surat</th>
                        <th class="py-3.5 px-6 font-bold">Tanggal Pengajuan</th>
                        <th class="py-3.5 px-6 font-bold">Tahap / Status</th>
                        <th class="py-3.5 px-6 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-soft text-sm">
                    @foreach($daftarPengajuan as $pengajuan)
                        <tr class="hover:bg-canvas transition">
                            <td class="py-4 px-6 font-mono font-bold text-primary">
                                {{ $pengajuan->no_pengajuan }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-ink">
                                <div class="flex items-center gap-2.5">
                                    <x-badge-layanan :kode="$pengajuan->layanan?->kode" variant="icon" size="sm" />
                                    <div>
                                        <span>{{ $pengajuan->layanan?->nama_layanan }}</span>
                                        @if($pengajuan->status === 'selesai' && $pengajuan->surat)
                                            <span class="block text-[11px] font-mono text-ink-muted mt-0.5">
                                                No. Surat: {{ $pengajuan->surat->no_surat }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-ink-muted">
                                {{ tgl_indo($pengajuan->tgl_pengajuan, true) }}
                            </td>
                            <td class="py-4 px-6">
                                <x-badge-status-pengajuan :status="$pengajuan->status" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    @if($pengajuan->status === 'selesai')
                                        <a
                                            href="{{ route('surat.unduh', $pengajuan) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-xs transition"
                                            title="Unduh PDF Resmi"
                                        >
                                            <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                    @elseif($pengajuan->status === 'kurang_berkas')
                                        <a
                                            href="{{ route('pengajuan.show', $pengajuan) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 shadow-xs transition"
                                            title="Perbaiki berkas yang kurang"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Perbaiki</span>
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('pengajuan.show', $pengajuan) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface border border-border-soft hover:bg-canvas text-xs font-bold text-ink transition shadow-sm"
                                    >
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($daftarPengajuan->hasPages())
            <div class="p-4 border-t border-border-soft bg-canvas">
                {{ $daftarPengajuan->links() }}
            </div>
        @endif
    @endif
</div>
