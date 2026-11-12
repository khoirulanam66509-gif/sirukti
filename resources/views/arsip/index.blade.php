<x-app-layout>
    <x-slot name="pageTitle">Arsip & Rekapitulasi</x-slot>
    <x-slot name="title">Arsip & Rekapitulasi — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink">Arsip & Rekapitulasi Laporan</h1>
                <p class="text-xs sm:text-sm text-ink-muted">
                    Rekapitulasi berkas permohonan surat administrasi Kampung Rukti Harjo periode <strong>{{ $namaBulan }} {{ $tahun }}</strong>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('arsip.ekspor-csv', request()->query()) }}"
                    class="btn-outline min-h-[44px] flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-semibold text-success border-success/40 bg-success-soft/40 hover:bg-success-soft active:scale-95 transition rounded-xl"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ekspor Excel/CSV</span>
                </a>
                <a
                    href="{{ route('arsip.cetak', request()->query()) }}"
                    target="_blank"
                    class="btn-outline min-h-[44px] flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-semibold text-primary border-primary hover:bg-primary-soft active:scale-95 transition rounded-xl"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'arsip', filterOpen: false, deleteModalOpen: false, deleteActionUrl: '', deleteNoPengajuan: '' }">

        <!-- Flash Status Message -->
        @if (session('status'))
            <div class="p-4 rounded-xl bg-success-soft border border-success/30 flex items-start gap-3">
                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-success-ink font-medium">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Tab Navigasi Utama: Arsip vs Rekapitulasi vs Log Aktivitas -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="grid grid-cols-3 sm:flex items-center gap-1.5 p-1 bg-canvas rounded-2xl border border-border-soft shadow-inner w-full sm:w-auto">
                <button
                    type="button"
                    @click="activeTab = 'arsip'"
                    :class="activeTab === 'arsip' ? 'bg-surface text-primary shadow-sm font-bold border border-border-soft' : 'text-ink-muted hover:text-ink font-medium'"
                    class="min-h-[44px] px-2.5 sm:px-4 py-2 rounded-xl text-[11px] sm:text-sm transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0 hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span>Daftar Arsip Surat</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary font-extrabold">{{ $pengajuans->total() }}</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'rekap'"
                    :class="activeTab === 'rekap' ? 'bg-surface text-primary shadow-sm font-bold border border-border-soft' : 'text-ink-muted hover:text-ink font-medium'"
                    class="min-h-[44px] px-2.5 sm:px-4 py-2 rounded-xl text-[11px] sm:text-sm transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0 hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Rekapitulasi Bulanan</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'audit'"
                    :class="activeTab === 'audit' ? 'bg-surface text-primary shadow-sm font-bold border border-border-soft' : 'text-ink-muted hover:text-ink font-medium'"
                    class="min-h-[44px] px-2.5 sm:px-4 py-2 rounded-xl text-[11px] sm:text-sm transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0 hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Log Aktivitas</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-primary/10 text-primary font-extrabold">{{ isset($auditLogs) ? $auditLogs->count() : 0 }}</span>
                </button>
            </div>

            <!-- Mobile Collapsible Filter Toggle -->
            <div class="block lg:hidden">
                <button
                    type="button"
                    @click="filterOpen = !filterOpen"
                    class="w-full py-2.5 px-4 rounded-xl bg-surface border border-border-soft text-ink text-xs font-bold flex items-center justify-between shadow-card active:scale-98 transition"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter &amp; Pencarian Arsip</span>
                        @if(request('q') || request('status') || request('id_layanan') || request('bulan') || request('tahun'))
                            <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 text-primary text-[11px]">
                        <span x-text="filterOpen ? 'Sembunyikan' : 'Buka Filter'">Buka Filter</span>
                        <svg class="w-3.5 h-3.5 transform transition-transform" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>

        <!-- Filter Bar Card (Desktop Always Visible, Mobile Collapsible) -->
        <div
            :class="filterOpen ? 'block' : 'hidden lg:block'"
            class="bg-surface rounded-card border border-border-soft p-4 sm:p-5 shadow-card transition-all"
        >
            <form method="GET" action="{{ route('arsip.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Filter Bulan -->
                    <div>
                        <label for="filter-bulan" class="block text-xs font-semibold text-ink-muted mb-1">Bulan</label>
                        <select id="filter-bulan" name="bulan" class="w-full text-xs sm:text-sm rounded-xl border-border-soft focus:ring-primary focus:border-primary">
                            @foreach(\App\Http\Controllers\ArsipLaporanController::$daftarBulan as $num => $nama)
                                <option value="{{ $num }}" {{ (int)$bulan === $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label for="filter-tahun" class="block text-xs font-semibold text-ink-muted mb-1">Tahun</label>
                        <select id="filter-tahun" name="tahun" class="w-full text-xs sm:text-sm rounded-xl border-border-soft focus:ring-primary focus:border-primary">
                            @for($y = (int)date('Y') + 1; $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Filter Layanan -->
                    <div>
                        <label for="filter-layanan" class="block text-xs font-semibold text-ink-muted mb-1">Layanan</label>
                        <select id="filter-layanan" name="id_layanan" class="w-full text-xs sm:text-sm rounded-xl border-border-soft focus:ring-primary focus:border-primary">
                            <option value="">Semua Layanan</option>
                            @foreach($layananList as $l)
                                <option value="{{ $l->id_layanan }}" {{ request('id_layanan') == $l->id_layanan ? 'selected' : '' }}>
                                    {{ $l->kode }} - {{ $l->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label for="filter-status" class="block text-xs font-semibold text-ink-muted mb-1">Status</label>
                        <select id="filter-status" name="status" class="w-full text-xs sm:text-sm rounded-xl border-border-soft focus:ring-primary focus:border-primary">
                            <option value="">Semua Status</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="menunggu_ttd" {{ request('status') === 'menunggu_ttd' ? 'selected' : '' }}>Menunggu TTD</option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="kurang_berkas" {{ request('status') === 'kurang_berkas' ? 'selected' : '' }}>Kurang Berkas</option>
                            <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Filter Cari Teks -->
                    <div>
                        <label for="filter-q" class="block text-xs font-semibold text-ink-muted mb-1">Cari Pemohon / Nomor</label>
                        <input
                            type="text"
                            id="filter-q"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="NIK, Nama, No. Surat..."
                            class="w-full text-xs sm:text-sm rounded-xl border-border-soft focus:ring-primary focus:border-primary"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border-soft/60">
                    <a
                        href="{{ route('arsip.index') }}"
                        class="px-3.5 py-2 text-xs font-semibold text-ink-muted hover:text-ink transition"
                    >
                        Reset Filter
                    </a>
                    <button
                        type="submit"
                        class="btn-primary px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:scale-95 transition"
                    >
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: DAFTAR ARSIP SURAT                 -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'arsip'" class="space-y-6">

            <!-- Daftar Arsip Pengajuan (Mobile: Elevated Cards, Desktop: Tabel) -->
            <div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-border-soft/80 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-ink">Daftar Arsip Pengajuan Surat</h2>
                        <p class="text-xs text-ink-muted">Total ditemukan: <strong>{{ $pengajuans->total() }}</strong> berkas pada filter terpilih</p>
                    </div>
                </div>

                <!-- Tampilan Kartu Mobile Elevated (360px - 767px) -->
                <div class="block md:hidden p-3.5 space-y-3">
                    @forelse($pengajuans as $p)
                        <div class="bg-surface rounded-2xl p-4 border border-border-soft shadow-card hover:shadow-featured transition space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <x-badge-layanan :kode="$p->layanan?->kode" size="sm" />
                                    <div class="min-w-0">
                                        <span class="font-bold text-ink text-sm font-mono block truncate">{{ $p->no_pengajuan }}</span>
                                        <p class="text-xs text-ink-muted font-medium truncate">{{ $p->layanan?->nama_layanan }}</p>
                                    </div>
                                </div>
                                <x-badge-status-pengajuan :status="$p->status" />
                            </div>

                            <div class="text-xs text-ink-muted space-y-1.5 pt-2 border-t border-border-soft/60">
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-muted">Pemohon:</span>
                                    <span class="font-semibold text-ink text-right">{{ $p->warga?->pengguna?->nama ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-muted">NIK:</span>
                                    <span class="font-mono text-ink text-right">{{ samarkan_nik($p->warga?->nik) }}</span>
                                </div>
                                @if($p->surat?->no_surat)
                                    <div class="flex items-center justify-between bg-primary-soft/40 px-2.5 py-1 rounded-lg border border-primary/20">
                                        <span class="text-primary font-semibold text-[11px]">No. Surat:</span>
                                        <span class="font-mono text-primary font-bold text-[11px]">{{ $p->surat->no_surat }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between text-[11px] pt-1 text-ink-muted">
                                    <span>Diajukan: {{ $p->tgl_pengajuan ? $p->tgl_pengajuan->format('d/m/Y H:i') : '-' }}</span>
                                    @if($p->tgl_selesai)
                                        <span class="text-success font-medium">Selesai: {{ $p->tgl_selesai->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-border-soft/60">
                                @if($role === 'kasi')
                                    <a
                                        href="{{ route('kasi.pengajuan.show', $p) }}"
                                        class="flex-1 text-center py-2.5 text-xs font-bold rounded-xl bg-canvas text-ink hover:bg-border-soft transition border border-border-soft active:scale-95 shadow-sm"
                                    >
                                        Rincian
                                    </a>
                                @else
                                    <a
                                        href="{{ route('kepala.persetujuan.show', $p) }}"
                                        class="flex-1 text-center py-2.5 text-xs font-bold rounded-xl bg-canvas text-ink hover:bg-border-soft transition border border-border-soft active:scale-95 shadow-sm"
                                    >
                                        Rincian
                                    </a>
                                @endif

                                @if($p->status === 'selesai')
                                    <a
                                        href="{{ route('surat.unduh', $p) }}"
                                        target="_blank"
                                        class="flex-1 text-center py-2.5 text-xs font-bold rounded-xl bg-success-soft text-success hover:bg-success/20 transition border border-success/30 active:scale-95 shadow-sm flex items-center justify-center gap-1.5"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                @endif

                                @if($role === 'kasi')
                                    <button
                                        type="button"
                                        @click="deleteModalOpen = true; deleteActionUrl = '{{ route('arsip.destroy', $p) }}'; deleteNoPengajuan = '{{ $p->no_pengajuan }}'"
                                        class="p-2.5 text-xs font-bold rounded-xl text-danger hover:bg-danger-soft transition border border-transparent hover:border-danger/20 active:scale-95"
                                        title="Hapus Arsip (Soft Delete)"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-ink-muted bg-surface rounded-2xl border border-border-soft">
                            Tidak ada arsip pengajuan yang cocok dengan filter yang dipilih.
                        </div>
                    @endforelse
                </div>

                <!-- Tampilan Tabel Desktop (md+ 768px+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-canvas border-b border-border-soft text-ink-muted text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">No. Pengajuan</th>
                                <th class="px-4 py-3">Pemohon</th>
                                <th class="px-4 py-3">Layanan</th>
                                <th class="px-4 py-3">No. Surat Resmi</th>
                                <th class="px-4 py-3">Tgl Pengajuan</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-soft">
                            @forelse($pengajuans as $p)
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        {{ $p->no_pengajuan }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-ink">{{ $p->warga?->pengguna?->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-ink-muted">NIK: {{ samarkan_nik($p->warga?->nik) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-badge-layanan :kode="$p->layanan?->kode" size="sm" />
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-primary">
                                        {{ $p->surat?->no_surat ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-ink-muted text-xs">
                                        {{ $p->tgl_pengajuan ? $p->tgl_pengajuan->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <x-badge-status-pengajuan :status="$p->status" />
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($role === 'kasi')
                                                <a
                                                    href="{{ route('kasi.pengajuan.show', $p) }}"
                                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-canvas text-ink hover:bg-border-soft transition"
                                                    title="Lihat Detail Permohonan"
                                                >
                                                    Detail
                                                </a>
                                            @else
                                                <a
                                                    href="{{ route('kepala.persetujuan.show', $p) }}"
                                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-canvas text-ink hover:bg-border-soft transition"
                                                    title="Lihat Detail Permohonan"
                                                >
                                                    Detail
                                                </a>
                                            @endif

                                            @if($p->status === 'selesai')
                                                <a
                                                    href="{{ route('surat.unduh', $p) }}"
                                                    target="_blank"
                                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-success-soft text-success hover:bg-success/20 transition flex items-center gap-1"
                                                    title="Unduh Berkas Surat Resmi"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    <span>PDF</span>
                                                </a>
                                            @endif

                                            @if($role === 'kasi')
                                                <button
                                                    type="button"
                                                    @click="deleteModalOpen = true; deleteActionUrl = '{{ route('arsip.destroy', $p) }}'; deleteNoPengajuan = '{{ $p->no_pengajuan }}'"
                                                    class="p-1.5 text-ink-muted hover:text-danger rounded-lg hover:bg-danger-soft transition"
                                                    title="Hapus Arsip (Soft Delete)"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-ink-muted">
                                        Tidak ada data arsip pengajuan pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                @if($pengajuans->hasPages())
                    <div class="px-5 py-4 border-t border-border-soft">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: REKAPITULASI BULANAN               -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'rekap'" class="space-y-6" style="display: none;">

            <!-- Kartu KPI Ringkasan Periode (Grid 2 kolom mobile, 5 kartu di desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
                <!-- 1. Total Pengajuan -->
                <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card">
                    <span class="text-xs font-semibold text-ink-muted">Total Pengajuan</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-ink mt-1">{{ $kpi['total'] }}</p>
                    <span class="text-[11px] text-ink-muted block mt-0.5">Periode {{ $namaBulan }} {{ $tahun }}</span>
                </div>

                <!-- 2. Selesai -->
                <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card">
                    <span class="text-xs font-semibold text-success-ink">Surat Selesai</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-success mt-1">{{ $kpi['selesai'] }}</p>
                    <span class="text-[11px] text-ink-muted block mt-0.5">Diterbitkan</span>
                </div>

                <!-- 3. Berjalan -->
                <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card">
                    <span class="text-xs font-semibold text-primary">Sedang Berjalan</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-primary mt-1">{{ $kpi['berjalan'] }}</p>
                    <span class="text-[11px] text-ink-muted block mt-0.5">Dalam proses</span>
                </div>

                <!-- 4. Ditolak -->
                <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card">
                    <span class="text-xs font-semibold text-danger">Ditolak</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-danger mt-1">{{ $kpi['ditolak'] }}</p>
                    <span class="text-[11px] text-ink-muted block mt-0.5">Tidak memenuhi</span>
                </div>

                <!-- 5. Rata-rata Waktu Selesai -->
                <div class="col-span-2 md:col-span-1 bg-surface rounded-card p-4 border border-border-soft shadow-card">
                    <span class="text-xs font-semibold text-ink-muted">Rata-rata Selesai</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-accent mt-1">
                        {{ $kpi['avg_jam'] }} <span class="text-xs font-semibold text-ink-muted">jam</span>
                    </p>
                    <span class="text-[11px] text-ink-muted block mt-0.5">Kecepatan pelayanan</span>
                </div>
            </div>

            <!-- Visualisasi Bar Proporsi Layanan (O3) -->
            <div class="bg-surface rounded-card border border-border-soft p-5 shadow-card space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-ink">Visualisasi Proporsi Pengajuan per Layanan</h2>
                        <p class="text-xs text-ink-muted">Perbandingan status penyelesaian surat pada periode {{ $namaBulan }} {{ $tahun }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 text-success"><span class="w-2.5 h-2.5 rounded-full bg-success"></span> Selesai</span>
                        <span class="inline-flex items-center gap-1.5 text-primary"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Berjalan</span>
                        <span class="inline-flex items-center gap-1.5 text-danger"><span class="w-2.5 h-2.5 rounded-full bg-danger"></span> Ditolak</span>
                    </div>
                </div>

                @php
                    $maxTotalLayanan = max(1, collect($rekapLayanan)->max('total') ?? 1);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    @foreach($rekapLayanan as $r)
                        @php
                            $totalBaris = (int) $r->total;
                            $pctSelesai = $totalBaris > 0 ? round(((int) $r->selesai / $totalBaris) * 100) : 0;
                            $pctBerjalan = $totalBaris > 0 ? round(((int) $r->berjalan / $totalBaris) * 100) : 0;
                            $pctDitolak = $totalBaris > 0 ? max(0, 100 - $pctSelesai - $pctBerjalan) : 0;
                        @endphp
                        <div class="p-3.5 rounded-2xl bg-canvas/70 border border-border-soft space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <x-badge-layanan :kode="$r->kode" size="sm" />
                                    <span class="text-xs font-bold text-ink truncate">{{ $r->nama_layanan }}</span>
                                </div>
                                <span class="text-xs font-extrabold text-ink shrink-0">{{ $totalBaris }} berkas</span>
                            </div>

                            <div class="w-full h-3 rounded-full bg-border-soft overflow-hidden flex">
                                @if($totalBaris > 0)
                                    @if($r->selesai > 0)
                                        <div class="h-full bg-success transition-all" style="width: {{ $pctSelesai }}%" title="Selesai: {{ $r->selesai }}"></div>
                                    @endif
                                    @if($r->berjalan > 0)
                                        <div class="h-full bg-primary transition-all" style="width: {{ $pctBerjalan }}%" title="Berjalan: {{ $r->berjalan }}"></div>
                                    @endif
                                    @if($r->ditolak > 0)
                                        <div class="h-full bg-danger transition-all" style="width: {{ $pctDitolak }}%" title="Ditolak: {{ $r->ditolak }}"></div>
                                    @endif
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-ink-muted font-medium">
                                <span>Selesai: <strong class="text-success">{{ $r->selesai }}</strong> ({{ $pctSelesai }}%)</span>
                                <span>Berjalan: <strong class="text-primary">{{ $r->berjalan }}</strong></span>
                                <span>Ditolak: <strong class="text-danger">{{ $r->ditolak }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Rekap Stored Procedure per Layanan -->
            <div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-border-soft/80 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-ink">Statistik per Jenis Layanan</h2>
                        <p class="text-xs text-ink-muted">Dihitung otomatis via Stored Procedure <code>sp_rekap_periode</code> periode {{ $namaBulan }} {{ $tahun }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-canvas border-b border-border-soft text-ink-muted text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Layanan</th>
                                <th class="px-4 py-3 text-center">Total</th>
                                <th class="px-4 py-3 text-center text-success">Selesai</th>
                                <th class="px-4 py-3 text-center text-primary">Berjalan</th>
                                <th class="px-4 py-3 text-center text-danger">Ditolak</th>
                                <th class="px-4 py-3 text-center">Rata-rata Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-soft">
                            @forelse($rekapLayanan as $idx => $r)
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="px-4 py-3 text-center text-ink-muted">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        <x-badge-layanan :kode="$r->kode" size="sm" />
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-ink">{{ $r->total }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-success">{{ $r->selesai }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-primary">{{ $r->berjalan }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-danger">{{ $r->ditolak }}</td>
                                    <td class="px-4 py-3 text-center text-ink-muted">
                                        {{ $r->rata_jam_selesai ? $r->rata_jam_selesai . ' jam' : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-ink-muted">
                                        Tidak ada data statistik untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 3: LOG AKTIVITAS PETUGAS (O6)         -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'audit'" class="space-y-6" style="display: none;">
            <div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-border-soft/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-ink">Jejak Audit Aktivitas Petugas</h2>
                        <p class="text-xs text-ink-muted">Rekam jejak otomatis perubahan data penduduk, persyaratan, pengaturan kampung, tanda tangan, dan pengelolaan arsip (50 aktivitas terbaru).</p>
                    </div>
                </div>

                <!-- Tampilan Kartu Mobile (360px - 767px) -->
                <div class="block md:hidden p-3.5 space-y-3">
                    @forelse($auditLogs ?? [] as $log)
                        <div class="bg-surface rounded-2xl p-4 border border-border-soft shadow-card space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary-soft text-primary">
                                        {{ $log->modul }}
                                    </span>
                                    <h3 class="text-sm font-bold text-ink mt-1">{{ $log->aksi }}</h3>
                                </div>
                                <span class="text-[11px] font-mono text-ink-muted shrink-0">
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>

                            @if($log->target_label)
                                <p class="text-xs text-ink bg-canvas px-3 py-2 rounded-xl border border-border-soft font-medium">
                                    Target: <strong class="text-ink">{{ $log->target_label }}</strong>
                                </p>
                            @endif

                            <div class="flex items-center justify-between text-xs text-ink-muted pt-1 border-t border-border-soft/60">
                                <span>Pelaku: <strong class="text-ink">{{ $log->pengguna?->nama ?? 'Sistem' }}</strong> ({{ strtoupper($log->pengguna?->role ?? '-') }})</span>
                                <span class="font-mono text-[11px]">{{ $log->ip_address ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-ink-muted bg-surface rounded-2xl border border-border-soft">
                            Belum ada catatan aktivitas petugas pada basis data.
                        </div>
                    @endforelse
                </div>

                <!-- Tampilan Tabel Desktop (md+ 768px+) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-canvas border-b border-border-soft text-ink-muted text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Petugas</th>
                                <th class="px-4 py-3">Modul</th>
                                <th class="px-4 py-3">Aksi Aktivitas</th>
                                <th class="px-4 py-3">Objek / Target</th>
                                <th class="px-4 py-3 text-right">Alamat IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-soft">
                            @forelse($auditLogs ?? [] as $log)
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-ink-muted whitespace-nowrap">
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-ink">{{ $log->pengguna?->nama ?? 'Sistem' }}</div>
                                        <div class="text-[11px] text-ink-muted uppercase font-bold">{{ $log->pengguna?->role ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-soft text-primary">
                                            {{ $log->modul }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        {{ $log->aksi }}
                                    </td>
                                    <td class="px-4 py-3 text-ink-muted">
                                        {{ $log->target_label ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-xs text-ink-muted">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-ink-muted">
                                        Belum ada catatan aktivitas petugas pada basis data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Soft Delete (Alpine.js) Khusus Kasi -->
        <div
            x-show="deleteModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display: none;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="deleteModalOpen = false"
                class="bg-surface rounded-card p-6 max-w-md w-full shadow-featured border border-border-soft space-y-4"
            >
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-danger-soft text-danger flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-ink">Hapus Arsip Pengajuan?</h3>
                        <p class="text-xs text-ink-muted mt-1">
                            Anda akan menghapus arsip pengajuan <strong class="text-ink" x-text="deleteNoPengajuan"></strong>.
                            Tindakan ini bersifat <em>soft delete</em>; berkas fisik dan data transaksi tetap tersimpan aman di basis data namun disembunyikan dari daftar aktif dan rekapitulasi.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border-soft/60">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="btn-outline px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl text-ink-muted hover:text-ink"
                    >
                        Batal
                    </button>
                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="btn-danger px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-danger text-white hover:bg-danger/90 active:scale-95 transition"
                        >
                            Ya, Hapus Arsip
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
