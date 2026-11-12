<div id="kontainer-warga">
    <!-- Mobile View: Cards (< md) -->
    <div class="md:hidden space-y-3 p-4 bg-canvas/30">
        @forelse ($wargaList as $w)
            <div class="rounded-2xl border border-border-soft p-4 bg-surface space-y-3 shadow-xs hover:border-primary/40 hover:shadow-card transition duration-200">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                            {{ strtoupper(substr($w->pengguna?->nama ?? 'W', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-ink text-sm truncate">{{ $w->pengguna?->nama ?? '-' }}</h4>
                            <span class="text-xs text-ink-muted font-mono block mt-0.5">NIK: {{ samarkan_nik($w->nik) }}</span>
                            @if($w->pengguna?->username && $w->pengguna->username !== $w->nik)
                                <span class="text-[11px] text-primary font-mono block">@<span>{{ $w->pengguna->username }}</span></span>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold shrink-0 {{ $w->pengguna?->status_aktif ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#FEE2E2] text-[#B91C1C]' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $w->pengguna?->status_aktif ? 'bg-[#16A34A]' : 'bg-[#DC2626]' }}"></span>
                        {{ $w->pengguna?->status_aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="text-xs text-ink-muted space-y-1.5 pt-2 border-t border-border-soft/60">
                    <div class="flex items-center justify-between">
                        <span class="text-ink-muted">Wilayah:</span>
                        <span class="font-medium text-ink">{{ $w->dusun?->nama_dusun ?? '-' }} {{ $w->rt ? '(RT '.$w->rt.'/RW '.($w->rw ?? '-').')' : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-ink-muted">WhatsApp:</span>
                        <div class="inline-flex items-center gap-1.5 font-medium text-ink font-mono">
                            <svg class="w-3.5 h-3.5 text-[#25D366] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                            </svg>
                            <span>{{ $w->pengguna?->no_hp ?? '-' }}</span>
                        </div>
                    </div>
                    @if ($w->pengguna?->wajib_ganti_password)
                        <div class="flex items-center justify-between text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200/60">
                            <span class="text-[11px] font-semibold">Keamanan:</span>
                            <span class="text-[11px] font-bold">Wajib Ganti Sandi</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-border-soft/60">
                    <a href="{{ route('kasi.warga.edit', $w) }}" class="flex-1 min-h-[40px] px-3 rounded-xl bg-canvas hover:bg-surface text-ink font-semibold text-xs border border-border-soft inline-flex items-center justify-center gap-1.5 transition active:scale-95 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Koreksi</span>
                    </a>
                    <button
                        type="button"
                        @click="openResetModal({{ $w->id_warga }}, '{{ addslashes($w->pengguna?->nama ?? '') }}', '{{ samarkan_nik($w->nik) }}', '{{ addslashes($w->dusun?->nama_dusun ?? '') }}')"
                        class="flex-1 min-h-[40px] px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs border border-amber-200 inline-flex items-center justify-center gap-1.5 transition active:scale-95 shadow-sm"
                    >
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Reset Sandi</span>
                    </button>
                    <form method="POST" action="{{ route('kasi.warga.toggle-status', $w) }}" class="inline">
                        @csrf
                        <button type="submit" class="min-h-[40px] px-3.5 rounded-xl text-xs font-semibold border transition active:scale-95 shadow-sm {{ $w->pengguna?->status_aktif ? 'bg-danger-soft hover:bg-danger/20 text-danger border-danger/30' : 'bg-success-soft hover:bg-success/20 text-success border-success/30' }}" title="{{ $w->pengguna?->status_aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                            {{ $w->pengguna?->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-ink-muted text-sm bg-surface rounded-2xl border border-border-soft p-6 space-y-2">
                <p>Belum ada data warga yang cocok dengan kriteria pencarian.</p>
                @if($q || ($dusunSelected ?? '') !== '' || ($statusSelected ?? 'semua') !== 'semua')
                    <button type="button"
                            data-reset-filter="true"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition cursor-pointer">
                        Reset Filter
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Desktop View: Table (>= md) -->
    <div class="hidden md:block overflow-x-auto -mx-6">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-canvas/80 text-ink-muted text-xs uppercase tracking-wider font-semibold border-y border-border-soft">
                    <th class="py-3.5 pl-6 pr-4">Nama Lengkap &amp; NIK</th>
                    <th class="py-3.5 px-4">Nomor WhatsApp</th>
                    <th class="py-3.5 px-4">Dusun &amp; RT/RW</th>
                    <th class="py-3.5 px-4">Status Akun</th>
                    <th class="py-3.5 px-4">Tgl Registrasi</th>
                    <th class="py-3.5 pr-6 pl-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-soft text-sm">
                @forelse ($wargaList as $w)
                    <tr class="hover:bg-canvas/40 transition">
                        <td class="py-4 pl-6 pr-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($w->pengguna?->nama ?? 'W', 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-ink leading-snug">{{ $w->pengguna?->nama ?? '-' }}</span>
                                    <span class="text-xs text-ink-muted font-mono tracking-wider">NIK: {{ samarkan_nik($w->nik) }}</span>
                                    @if($w->pengguna?->username && $w->pengguna->username !== $w->nik)
                                        <span class="text-[11px] text-primary font-mono">@<span>{{ $w->pengguna->username }}</span></span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-ink">
                            <div class="flex items-center gap-1.5 text-xs font-medium">
                                <!-- Ikon WhatsApp Berwarna Hijau (#25D366) Sesuai R3 -->
                                <svg class="w-4 h-4 text-[#25D366] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                </svg>
                                <span>{{ $w->pengguna?->no_hp ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col text-xs">
                                <span class="font-semibold text-ink">{{ $w->dusun?->nama_dusun ?? '-' }}</span>
                                <span class="text-ink-muted">RT {{ $w->rt ?? '000' }} / RW {{ $w->rw ?? '000' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $w->pengguna?->status_aktif ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#FEE2E2] text-[#B91C1C]' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $w->pengguna?->status_aktif ? 'bg-[#16A34A]' : 'bg-[#DC2626]' }}"></span>
                                    {{ $w->pengguna?->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                @if ($w->pengguna?->wajib_ganti_password)
                                    <span class="text-[10px] text-amber-700 font-semibold px-2 py-0.5 rounded bg-amber-50">
                                        Wajib Ganti Sandi
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs text-ink-muted">
                            {{ tgl_indo($w->created_at) }}
                        </td>
                        <td class="py-4 pr-6 pl-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('kasi.warga.edit', $w) }}" class="min-h-[36px] px-3 rounded-lg bg-canvas hover:bg-surface text-ink font-semibold text-xs border border-border-soft inline-flex items-center gap-1 transition active:scale-95 shadow-xs" title="Lihat & Koreksi Profil">
                                    <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Koreksi</span>
                                </a>

                                <button
                                    type="button"
                                    @click="openResetModal({{ $w->id_warga }}, '{{ addslashes($w->pengguna?->nama ?? '') }}', '{{ samarkan_nik($w->nik) }}', '{{ addslashes($w->dusun?->nama_dusun ?? '') }}')"
                                    class="min-h-[36px] px-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs border border-amber-200 inline-flex items-center gap-1 transition active:scale-95 shadow-xs"
                                    title="Reset Password Warga"
                                >
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    <span class="hidden sm:inline">Reset</span>
                                </button>

                                <form method="POST" action="{{ route('kasi.warga.toggle-status', $w) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="min-h-[36px] px-2.5 rounded-lg text-xs font-semibold border transition active:scale-95 shadow-xs {{ $w->pengguna?->status_aktif ? 'bg-danger-soft hover:bg-danger/20 text-danger border-danger/30' : 'bg-success-soft hover:bg-success/20 text-success border-success/30' }}" title="{{ $w->pengguna?->status_aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                        {{ $w->pengguna?->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-ink-muted text-sm">
                            Belum ada data warga yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($wargaList->hasPages())
        <div class="pt-5 border-t border-border-soft flex justify-end pagination-ajax-area">
            {{ $wargaList->links() }}
        </div>
    @endif
</div>
