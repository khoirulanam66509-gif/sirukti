<x-app-layout>
    <x-slot name="pageTitle">Daftar Persetujuan</x-slot>
    <x-slot name="title">Daftar Persetujuan — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                    Persetujuan Surat Resmi
                </h1>
                <p class="text-sm text-ink-muted mt-1">
                    Tinjau kelengkapan berkas dan lakukan penandatanganan digital untuk menerbitkan surat warga
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                <span>Meja Kepala Kampung</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

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

        <!-- Peringatan Bila Kepala Belum Mengunggah TTD -->
        @if(empty($user->file_ttd))
            <div class="p-4 rounded-card bg-amber-50 border border-amber-300 text-amber-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-amber-100 text-amber-700 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">Spesimen Tanda Tangan Digital Belum Tersedia</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Anda belum mengunggah gambar tanda tangan digital. Unggah tanda tangan Anda sekarang agar dapat menandatangani surat warga.
                        </p>
                    </div>
                </div>
                <a href="{{ route('kepala.profil.edit') }}"
                   class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-sm whitespace-nowrap self-start sm:self-auto">
                    Unggah Tanda Tangan
                </a>
            </div>
        @endif

        <!-- 1. KPI Cards Ringkasan & Tab Navigasi -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <!-- Menunggu TTD -->
            <a href="{{ route('kepala.persetujuan.index', ['tab' => 'menunggu_ttd', 'layanan' => $layananFilter, 'cari' => $cari]) }}"
               class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card hover:border-purple-300 transition group block {{ $tabFilter === 'menunggu_ttd' ? 'ring-2 ring-purple-600 bg-purple-50/30' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold {{ $tabFilter === 'menunggu_ttd' ? 'text-purple-900 font-bold' : 'text-ink-muted' }}">Menunggu Persetujuan</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 {{ $tabFilter === 'menunggu_ttd' ? 'animate-pulse' : '' }}"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading text-purple-700">
                    {{ $kpi['menunggu_ttd'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Surat perlu ditandatangani</p>
            </a>

            <!-- Selesai Bulan Ini -->
            <a href="{{ route('kepala.persetujuan.index', ['tab' => 'selesai', 'layanan' => $layananFilter, 'cari' => $cari]) }}"
               class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card hover:border-emerald-300 transition group block {{ $tabFilter === 'selesai' ? 'ring-2 ring-emerald-600 bg-emerald-50/30' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold {{ $tabFilter === 'selesai' ? 'text-emerald-900 font-bold' : 'text-ink-muted' }}">Riwayat Disetujui (Bulan Ini)</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading text-emerald-700">
                    {{ $kpi['selesai_bulan_ini'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Surat resmi terbit</p>
            </a>
        </div>

        <!-- 2. Filter & Pencarian Bar -->
        <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card">
            <form method="GET" action="{{ route('kepala.persetujuan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <input type="hidden" name="tab" value="{{ $tabFilter }}">

                <!-- Input Cari -->
                <div class="md:col-span-8 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           name="cari"
                           value="{{ $cari }}"
                           placeholder="Cari nomor permohonan, nama pemohon, atau NIK..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink placeholder-ink-muted">
                </div>

                <!-- Dropdown Layanan -->
                <div class="md:col-span-3">
                    <select name="layanan"
                            onchange="this.form.submit()"
                            class="w-full py-2.5 px-3 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-medium">
                        <option value="semua" {{ $layananFilter === 'semua' ? 'selected' : '' }}>Semua Layanan</option>
                        @foreach($daftarLayanan as $layanan)
                            <option value="{{ $layanan->kode }}" {{ $layananFilter === $layanan->kode ? 'selected' : '' }}>
                                {{ $layanan->kode }} — {{ $layanan->nama_layanan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="md:col-span-1 flex items-center gap-2">
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white font-semibold text-sm transition shadow-sm text-center">
                        Cari
                    </button>
                    @if($cari || $layananFilter !== 'semua')
                        <a href="{{ route('kepala.persetujuan.index') }}"
                           class="py-2.5 px-3 rounded-xl border border-border-soft hover:bg-canvas text-ink-muted hover:text-ink font-semibold text-sm transition"
                           title="Reset filter">
                            ↺
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Daftar Antrean Persetujuan -->
        <div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden">
            @if($persetujuan->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-full bg-canvas border border-border-soft flex items-center justify-center text-ink-muted">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-ink font-heading">
                        {{ $tabFilter === 'selesai' ? 'Belum Ada Riwayat Surat Disetujui' : 'Tidak Ada Surat Menunggu Persetujuan' }}
                    </h3>
                    <p class="text-sm text-ink-muted max-w-sm mx-auto">
                        @if($cari || $layananFilter !== 'semua')
                            Tidak ada surat yang sesuai dengan kata kunci pencarian atau filter layanan saat ini.
                        @elseif($tabFilter === 'selesai')
                            Surat yang telah disetujui dan diterbitkan akan tercatat di tab ini.
                        @else
                            Semua permohonan surat masuk telah selesai ditinjau dan ditandatangani. Meja persetujuan bersih!
                        @endif
                    </p>
                </div>
            @else
                <!-- Tampilan Mobile: Kartu Bertumpuk (< md) -->
                <div class="md:hidden p-4 space-y-3.5 bg-canvas/30">
                    @foreach($persetujuan as $item)
                        <div class="p-4 rounded-2xl bg-surface border border-border-soft hover:border-primary/40 hover:shadow-card transition duration-200 space-y-3.5 shadow-xs">
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-center gap-3 min-w-0">
                                    <x-badge-layanan :kode="$item->layanan?->kode" variant="icon" size="sm" />
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold text-ink truncate">{{ $item->warga->pengguna->nama }}</h4>
                                        <span class="text-xs font-mono font-bold text-primary block mt-0.5">{{ $item->no_pengajuan }}</span>
                                    </div>
                                </div>
                                <x-badge-status-pengajuan :status="$item->status" />
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                                <div>
                                    <span class="block text-[11px] text-ink-muted">NIK</span>
                                    <span class="font-mono text-ink block mt-0.5">{{ samarkan_nik($item->warga->nik) }}</span>
                                </div>
                                <div>
                                    <span class="block text-[11px] text-ink-muted">Layanan</span>
                                    <span class="font-medium text-ink truncate block mt-0.5">{{ $item->layanan?->nama_layanan }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                                <span>{{ $item->status === 'selesai' ? 'Terbit' : 'Diajukan' }}: <strong>{{ tgl_indo($item->status === 'selesai' ? $item->updated_at : $item->tgl_pengajuan) }}</strong></span>
                                <a href="{{ route('kepala.persetujuan.show', $item) }}"
                                   class="min-h-[44px] px-4 rounded-btn {{ $item->status === 'selesai' ? 'bg-primary hover:bg-primary-hover' : 'bg-purple-700 hover:bg-purple-800' }} text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1.5">
                                    <span>{{ $item->status === 'selesai' ? 'Lihat Surat' : 'Tinjau & TTD' }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tampilan Desktop: Tabel Data (>= md) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border-soft bg-canvas/70 text-xs font-bold text-ink-muted uppercase tracking-wider">
                                <th class="py-3.5 px-4">No. Permohonan</th>
                                <th class="py-3.5 px-4">{{ $tabFilter === 'selesai' ? 'Tanggal Terbit' : 'Tanggal Masuk' }}</th>
                                <th class="py-3.5 px-4">Pemohon</th>
                                <th class="py-3.5 px-4">Layanan Surat</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-soft text-sm">
                            @foreach($persetujuan as $item)
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="py-4 px-4 font-mono font-bold text-primary">
                                        <a href="{{ route('kepala.persetujuan.show', $item) }}" class="hover:underline">
                                            {{ $item->no_pengajuan }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-ink-muted whitespace-nowrap text-xs">
                                        {{ tgl_indo($item->status === 'selesai' ? $item->updated_at : $item->tgl_pengajuan) }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-ink">{{ $item->warga->pengguna->nama }}</div>
                                        <div class="text-xs text-ink-muted font-mono">
                                            {{ samarkan_nik($item->warga->nik) }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <x-badge-layanan :kode="$item->layanan?->kode" variant="icon" size="sm" />
                                            <span class="text-xs text-ink font-semibold">{{ $item->layanan?->nama_layanan }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <x-badge-status-pengajuan :status="$item->status" />
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <a href="{{ route('kepala.persetujuan.show', $item) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl {{ $item->status === 'selesai' ? 'bg-primary hover:bg-primary-hover' : 'bg-purple-700 hover:bg-purple-800' }} text-white text-xs font-bold transition shadow-sm">
                                            <span>{{ $item->status === 'selesai' ? 'Lihat Surat' : 'Tinjau & TTD' }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- 4. Pagination -->
                @if($persetujuan->hasPages())
                    <div class="p-4 border-t border-border-soft bg-canvas/30">
                        {{ $persetujuan->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>
