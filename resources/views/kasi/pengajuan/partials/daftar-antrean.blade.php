<div id="kontainer-antrean">
    @if($antrean->isEmpty())
        <div class="p-12 text-center space-y-3">
            <div class="w-16 h-16 mx-auto rounded-full bg-canvas border border-border-soft flex items-center justify-center text-ink-muted">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-ink font-heading">Tidak Ada Permohonan Dalam Antrean</h3>
            <p class="text-sm text-ink-muted max-w-sm mx-auto">
                @if($cari || ($statusFilter ?? 'semua') !== 'semua' || ($layananFilter ?? 'semua') !== 'semua')
                    Tidak ada permohonan yang cocok dengan kriteria filter saat ini. Coba sesuaikan kata kunci pencarian.
                @else
                    Saat ini belum ada permohonan surat warga yang masuk dalam sistem.
                @endif
            </p>
            @if($cari || ($statusFilter ?? 'semua') !== 'semua' || ($layananFilter ?? 'semua') !== 'semua')
                <button type="button"
                        data-reset-filter="true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition mt-2 cursor-pointer">
                    Reset Semua Filter
                </button>
            @endif
        </div>
    @else
        <!-- Tampilan Mobile: Kartu Bertumpuk (< md) -->
        <div class="md:hidden p-4 space-y-3.5 bg-canvas/30">
            @foreach($antrean as $item)
                <div class="p-4 rounded-2xl bg-surface border border-border-soft hover:border-primary/40 hover:shadow-card transition duration-200 space-y-3.5 shadow-xs">
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-badge-layanan :kode="$item->kode_layanan" variant="icon" size="sm" />
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-ink truncate">{{ $item->nama_warga }}</h4>
                                <span class="text-xs font-mono font-bold text-primary block mt-0.5">{{ $item->no_pengajuan }}</span>
                            </div>
                        </div>
                        <x-badge-status-pengajuan :status="$item->status" />
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-ink-muted pt-2 border-t border-border-soft/60">
                        <div>
                            <span class="block text-[11px] text-ink-muted">NIK</span>
                            <span class="font-mono text-ink block mt-0.5">{{ samarkan_nik($item->nik) }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] text-ink-muted">Layanan</span>
                            <span class="font-medium text-ink truncate block mt-0.5">{{ $item->nama_layanan }}</span>
                        </div>
                    </div>

                    <!-- Indikator Berkas & Tombol Aksi -->
                    <div class="flex items-center justify-between pt-2 border-t border-border-soft/60">
                        <div class="flex items-center gap-2 text-xs">
                            @if($item->berkas_perbaikan > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-100 text-orange-800 font-semibold text-[11px] border border-orange-200">
                                    ⚠️ {{ $item->berkas_perbaikan }} Perbaikan
                                </span>
                            @elseif($item->berkas_menunggu > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold text-[11px] border border-amber-200">
                                    ⏳ {{ $item->berkas_menunggu }} Menunggu Cek
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-[11px] border border-emerald-200">
                                    ✓ Berkas Sesuai
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('kasi.pengajuan.show', $item->id_pengajuan) }}"
                           class="min-h-[44px] px-4 rounded-btn bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1.5">
                            <span>Verifikasi</span>
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
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Pemohon</th>
                        <th class="py-3.5 px-4">Layanan</th>
                        <th class="py-3.5 px-4">Status Pengajuan</th>
                        <th class="py-3.5 px-4">Kelengkapan Berkas</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-soft text-sm">
                    @foreach($antrean as $item)
                        <tr class="hover:bg-canvas/50 transition">
                            <!-- No Permohonan -->
                            <td class="py-4 px-4 font-mono font-bold text-primary">
                                <a href="{{ route('kasi.pengajuan.show', $item->id_pengajuan) }}" class="hover:underline">
                                    {{ $item->no_pengajuan }}
                                </a>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-4 px-4 text-ink-muted whitespace-nowrap text-xs">
                                {{ tgl_indo($item->tgl_pengajuan) }}
                            </td>

                            <!-- Pemohon -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-ink">{{ $item->nama_warga }}</div>
                                <div class="text-xs text-ink-muted font-mono">
                                    {{ samarkan_nik($item->nik) }}
                                </div>
                            </td>

                            <!-- Layanan -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <x-badge-layanan :kode="$item->kode_layanan" variant="icon" size="sm" />
                                    <span class="text-xs text-ink font-semibold">{{ $item->nama_layanan }}</span>
                                </div>
                            </td>

                            <!-- Status Pengajuan -->
                            <td class="py-4 px-4">
                                <x-badge-status-pengajuan :status="$item->status" />
                            </td>

                            <!-- Status Berkas -->
                            <td class="py-4 px-4 text-xs">
                                @if($item->berkas_perbaikan > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-100 text-orange-800 font-semibold border border-orange-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                                        {{ $item->berkas_perbaikan }} Perlu Perbaikan
                                    </span>
                                @elseif($item->berkas_menunggu > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        {{ $item->berkas_menunggu }} Menunggu Cek
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Lengkap Sesuai
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('kasi.pengajuan.show', $item->id_pengajuan) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm">
                                    <span>Periksa</span>
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
        @if($antrean->hasPages())
            <div class="p-4 border-t border-border-soft bg-canvas/30 pagination-ajax-area">
                {{ $antrean->links() }}
            </div>
        @endif
    @endif
</div>
