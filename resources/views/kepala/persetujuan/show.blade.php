<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Tinjau Persetujuan #{{ $pengajuan->no_pengajuan }}</x-slot>
    <x-slot name="title">Persetujuan #{{ $pengajuan->no_pengajuan }} — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs font-semibold text-ink-muted">
                    <a href="{{ route('kepala.persetujuan.index') }}" class="hover:text-primary transition">Persetujuan</a>
                    <span>/</span>
                    <span class="font-mono text-ink">{{ $pengajuan->no_pengajuan }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                        Tinjauan Persetujuan: {{ $pengajuan->layanan->nama_layanan }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                        {{ $pengajuan->status === 'selesai' ? 'Surat Telah Diterbitkan' : 'Menunggu TTD Anda' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('kepala.persetujuan.index') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border-soft bg-surface hover:bg-canvas text-xs font-bold text-ink transition shadow-sm">
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        showModalKembalikan: {{ $errors->has('catatan') ? 'true' : 'false' }},
        showModalSetujui: false,
        showPreviewMobile: false,
        init() {
            if (this.showModalKembalikan || this.showModalSetujui) {
                document.body.classList.add('overflow-y-hidden');
            }
            this.$watch('showModalKembalikan', value => {
                if (value || this.showModalSetujui) {
                    document.body.classList.add('overflow-y-hidden');
                } else {
                    document.body.classList.remove('overflow-y-hidden');
                }
            });
            this.$watch('showModalSetujui', value => {
                if (value || this.showModalKembalikan) {
                    document.body.classList.add('overflow-y-hidden');
                } else {
                    document.body.classList.remove('overflow-y-hidden');
                }
            });
        }
    }" class="space-y-6">

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

        <!-- Error Message Alert -->
        @if ($errors->any())
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kendala:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Peringatan TTD Kepala Kosong -->
        @if(empty($user->file_ttd) && $pengajuan->status !== 'selesai')
            <div class="p-4 rounded-card bg-amber-50 border border-amber-300 text-amber-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <h3 class="text-sm font-bold">Spesimen Tanda Tangan Anda Belum Tersedia</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Silakan unggah tanda tangan digital terlebih dahulu pada menu profil sebelum menyetujui surat.
                        </p>
                    </div>
                </div>
                <a href="{{ route('kepala.profil.edit') }}"
                   class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-sm whitespace-nowrap">
                    Unggah Tanda Tangan Sekarang
                </a>
            </div>
        @endif

        <!-- Informasi Cap Stempel -->
        @if(!empty($user->file_ttd) && empty($user->file_stempel) && $pengajuan->status !== 'selesai')
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 text-xs shadow-xs">
                <div class="flex items-center gap-2.5">
                    <span class="text-base">ℹ️</span>
                    <span>Cap stempel resmi kampung belum diunggah. Surat akan diterbitkan dengan tanda tangan digital tanpa cap dinas.</span>
                </div>
                <a href="{{ route('kepala.profil.edit') }}" class="font-bold text-blue-700 hover:underline shrink-0">
                    Kelola Stempel Kampung →
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Kolom Kiri: Data Pemohon, Isian & Berkas Persyaratan (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 1. Identitas Pemohon -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-3.5">
                    <h2 class="text-base font-bold text-ink font-heading flex items-center justify-between border-b border-border-soft pb-2.5">
                        <span>Data Pemohon Surat</span>
                        <span class="text-xs font-mono font-bold text-primary">{{ $pengajuan->no_pengajuan }}</span>
                    </h2>

                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between py-1 border-b border-border-soft/60">
                            <span class="text-ink-muted">Nama Lengkap</span>
                            <span class="font-bold text-ink text-right">{{ $pengajuan->warga->pengguna->nama }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-soft/60">
                            <span class="text-ink-muted">NIK</span>
                            <span class="font-mono font-bold text-ink text-right">{{ $pengajuan->warga->nik }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-soft/60">
                            <span class="text-ink-muted">No. Kartu Keluarga</span>
                            <span class="font-mono text-ink text-right">{{ $pengajuan->warga->no_kk ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-soft/60">
                            <span class="text-ink-muted">Tempat/Tgl Lahir</span>
                            <span class="text-ink text-right">
                                {{ $pengajuan->warga->tempat_lahir ?: '-' }},
                                {{ $pengajuan->warga->tgl_lahir ? tgl_indo($pengajuan->warga->tgl_lahir) : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-soft/60">
                            <span class="text-ink-muted">Dusun / RT / RW</span>
                            <span class="text-ink text-right">
                                {{ $pengajuan->warga->dusun?->nama_dusun ?? 'Dusun' }}
                                (RT {{ $pengajuan->warga->rt }} / RW {{ $pengajuan->warga->rw }})
                            </span>
                        </div>
                        <div class="py-1">
                            <span class="text-ink-muted block mb-0.5">Alamat Lengkap</span>
                            <span class="text-ink font-medium">{{ $pengajuan->warga->alamat ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Rincian Data Isian -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-3">
                    <h3 class="text-sm font-bold text-ink font-heading border-b border-border-soft pb-2.5">
                        Isian Permohonan ({{ $pengajuan->layanan->kode }})
                    </h3>

                    <div class="space-y-2 text-xs">
                        @foreach($pengajuan->data_isian ?? [] as $k => $v)
                            <div class="flex items-start justify-between gap-3 py-1.5 border-b border-border-soft/40">
                                <span class="text-ink-muted">{{ ucwords(str_replace('_', ' ', $k)) }}</span>
                                <span class="font-bold text-ink text-right max-w-[65%] break-words">
                                    @if(str_contains($k, 'penghasilan') || str_contains($k, 'modal'))
                                        {{ is_numeric($v) ? rupiah((int)$v) : $v }}
                                    @elseif(str_contains($k, 'tahun') && is_numeric($v))
                                        {{ $v }} Tahun
                                    @elseif($k === 'jumlah_tanggungan' && is_numeric($v))
                                        {{ $v }} Jiwa
                                    @else
                                        {{ is_array($v) ? json_encode($v) : $v }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Berkas Pendukung (Status Sesuai) -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-3">
                    <h3 class="text-sm font-bold text-ink font-heading border-b border-border-soft pb-2.5 flex items-center justify-between">
                        <span>Berkas Persyaratan</span>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            ✓ Telah Diverifikasi Kasi
                        </span>
                    </h3>

                    <div class="space-y-2.5">
                        @foreach($pengajuan->berkas as $b)
                            <div class="p-3 rounded-xl bg-canvas border border-border-soft flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-bold text-ink">{{ $b->syarat?->nama_syarat ?? 'Persyaratan' }}</div>
                                    <div class="text-[11px] text-ink-muted font-mono mt-0.5">{{ $b->nama_file_asli }}</div>
                                </div>
                                <a href="{{ route('pengajuan.berkas', [$pengajuan, $b->id_berkas]) }}"
                                   target="_blank"
                                   class="px-2.5 py-1.5 rounded-lg bg-surface hover:bg-canvas border border-border-soft text-primary font-bold transition shadow-sm text-[11px]">
                                    Buka File
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Pratinjau Lembar Surat Kertas A4 (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-border-soft pb-3">
                        <div class="flex items-center gap-2.5">
                            <x-badge-layanan :kode="$pengajuan->layanan->kode" size="sm" />
                            <div>
                                <h2 class="text-base font-bold text-ink font-heading">
                                    Lembar Pratinjau Surat Resmi (A4)
                                </h2>
                                <p class="text-xs text-ink-muted">Format cetak resmi dokumen dengan spesimen tanda tangan</p>
                            </div>
                        </div>
                        <a href="{{ route('kepala.persetujuan.preview', $pengajuan) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-bold transition shadow-sm hover:bg-primary-hover active:scale-95 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Buka PDF Layar Penuh</span>
                        </a>
                    </div>

                    <!-- Toggle Pratinjau di Layar Mobile (< lg) -->
                    <div class="block lg:hidden">
                        <button
                            type="button"
                            @click="showPreviewMobile = !showPreviewMobile"
                            class="w-full py-2.5 px-4 rounded-xl bg-canvas border border-border-soft text-ink text-xs font-semibold flex items-center justify-center gap-2 active:scale-98 transition"
                        >
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span x-text="showPreviewMobile ? 'Sembunyikan Lembar Pratinjau PDF' : 'Tampilkan Lembar Pratinjau di Sini'">Tampilkan Lembar Pratinjau di Sini</span>
                            <svg class="w-3.5 h-3.5 transform transition-transform" :class="showPreviewMobile ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Frame Dokumen Surat PDF (Desktop selalu tampil lg:block, Mobile dikontrol toggle) -->
                    <div :class="showPreviewMobile ? 'block' : 'hidden lg:block'" class="w-full bg-canvas rounded-2xl border border-border-soft overflow-hidden shadow-inner flex flex-col items-center transition-all">
                        <iframe src="{{ route('kepala.persetujuan.preview', $pengajuan) }}"
                                class="w-full h-[650px] sm:h-[800px] border-0 rounded-2xl"
                                title="Pratinjau Surat Resmi Kepala Kampung"></iframe>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Action Bar -->
        @if($pengajuan->status === 'menunggu_ttd')
            <div class="fixed bottom-0 inset-x-0 lg:left-64 bg-surface/95 backdrop-blur-md border-t border-border-soft p-4 z-40 shadow-2xl safe-bottom">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-ink-muted">
                        Pastikan seluruh data pada lembar surat telah sesuai sebelum melakukan pengesahan digital.
                    </div>

                    <div class="flex items-center gap-2.5 sm:gap-3 w-full sm:w-auto justify-end">
                        <!-- Tombol Kembalikan ke Kasi -->
                        <button type="button"
                                @click="showModalKembalikan = true"
                                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl border border-border-soft hover:bg-canvas text-ink text-xs font-bold transition shadow-sm text-center">
                            Kembalikan
                        </button>

                        <!-- Tombol Setujui & TTD -->
                        @if(!empty($user->file_ttd))
                            <button type="button"
                                    @click="showModalSetujui = true"
                                    class="flex-1 sm:flex-none px-5 sm:px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-featured flex items-center justify-center gap-2 text-center cursor-pointer active:scale-95">
                                <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Setujui &amp; TTD</span>
                            </button>
                        @else
                            <a href="{{ route('kepala.profil.edit') }}"
                               class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5 text-center">
                                <span>Unggah TTD Dahulu</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Dialog Konfirmasi Setujui & TTD -->
        <template x-teleport="body">
            <div x-show="showModalSetujui"
                 x-cloak
                 x-data="{ isApproving: false }"
                 @keydown.escape.window="if (!isApproving) showModalSetujui = false"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-setujui-title">
                <!-- Backdrop -->
                <div x-show="showModalSetujui"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="if (!isApproving) showModalSetujui = false"
                     class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>

                <!-- Panel Modal -->
                <div class="relative z-10 min-h-full flex items-end sm:items-center justify-center p-0 sm:p-4 text-center"
                     @click.self="if (!isApproving) showModalSetujui = false">
                    <div x-show="showModalSetujui"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                         class="w-full sm:max-w-md bg-surface rounded-t-3xl sm:rounded-card border border-border-soft p-6 text-center shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">

                        <!-- Ikon Pengesahan Digital dengan Animasi Halus -->
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-emerald-100/80 border border-emerald-200 text-emerald-700 flex items-center justify-center shadow-xs relative">
                            <span class="absolute inset-0 rounded-2xl bg-emerald-400/20 animate-ping" style="animation-duration: 2.5s;"></span>
                            <svg class="w-7 h-7 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>

                        <!-- Judul & Deskripsi -->
                        <div class="space-y-1.5">
                            <h3 id="modal-setujui-title" class="text-base sm:text-lg font-extrabold text-ink font-heading">
                                Setujui &amp; Tandatangani Surat?
                            </h3>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Surat akan diberi nomor resmi, dibubuhi tanda tangan digital &amp; QR verifikasi, serta langsung diterbitkan untuk pemohon.
                            </p>
                        </div>

                        <!-- Ringkasan Dokumen Singkat -->
                        <div class="p-3.5 rounded-2xl bg-canvas border border-border-soft text-left text-xs space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-ink-muted">Jenis Surat</span>
                                <span class="font-bold text-ink text-right">{{ $pengajuan->layanan->nama_layanan }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-ink-muted">Pemohon</span>
                                <span class="font-bold text-ink text-right">{{ $pengajuan->warga->pengguna->nama }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-ink-muted">No. Pengajuan</span>
                                <span class="font-mono font-bold text-primary text-right">{{ $pengajuan->no_pengajuan }}</span>
                            </div>
                        </div>

                        <!-- Tombol Aksi Batal & Ya Setujui -->
                        <form method="POST" action="{{ route('kepala.persetujuan.setujui', $pengajuan) }}"
                              @submit="isApproving = true"
                              class="grid grid-cols-2 gap-3 pt-1">
                            @csrf
                            <button type="button"
                                    :disabled="isApproving"
                                    @click="if (!isApproving) showModalSetujui = false"
                                    class="w-full px-4 py-2.5 rounded-xl border border-border-soft hover:bg-canvas text-ink-muted hover:text-ink text-xs font-bold transition disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="isApproving"
                                    class="w-full px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-featured inline-flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed min-h-[44px] cursor-pointer">
                                <template x-if="isApproving">
                                    <svg class="w-4 h-4 animate-spin text-accent shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!isApproving">
                                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <span x-text="isApproving ? 'Menerbitkan...' : 'Ya, Setujui & TTD'">Ya, Setujui &amp; TTD</span>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </template>

        <!-- Modal Dialog Kembalikan ke Kasi -->
        <template x-teleport="body">
            <div x-show="showModalKembalikan"
                 x-cloak
                 x-data="{ isSubmitting: false, catatanRevisi: @js(old('catatan', '')) }"
                 @keydown.escape.window="if (!isSubmitting) showModalKembalikan = false"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog"
                 aria-modal="true">
                <!-- Backdrop -->
                <div x-show="showModalKembalikan"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="if (!isSubmitting) showModalKembalikan = false"
                     class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>

                <!-- Panel Modal -->
                <div class="relative z-10 min-h-full flex items-end sm:items-center justify-center p-0 sm:p-4 text-center"
                     @click.self="if (!isSubmitting) showModalKembalikan = false">
                    <div x-show="showModalKembalikan"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                         class="w-full sm:max-w-lg bg-surface rounded-t-3xl sm:rounded-card border border-border-soft p-6 text-left shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">

                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-amber-100 text-amber-700 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-ink font-heading">
                                    Kembalikan Permohonan ke Kasi
                                </h3>
                                <p class="text-xs text-ink-muted mt-1">
                                    Berikan catatan koreksi spesifik agar petugas verifikator Kasi dapat memeriksa atau memperbaiki kekeliruan dokumen.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('kepala.persetujuan.kembalikan', $pengajuan) }}" class="space-y-4"
                              @submit="isSubmitting = true">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1.5">
                                    Catatan Koreksi / Revisi <span class="text-rose-600">*</span>
                                </label>
                                <textarea name="catatan"
                                          rows="3"
                                          x-model="catatanRevisi"
                                          required
                                          placeholder="Contoh: Harap periksa kembali alamat domisili pemohon, RT yang tertera tidak sesuai dengan data dusun."
                                          class="w-full p-3 rounded-xl border {{ $errors->has('catatan') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border-soft' }} bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-xs text-ink placeholder-ink-muted"></textarea>
                                @error('catatan')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Preset Catatan Cepat -->
                                <div class="mt-2 space-y-1">
                                    <span class="text-[11px] font-semibold text-ink-muted block">Pilihan Catatan Cepat:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button"
                                                @click="catatanRevisi = 'Format data surat / nomor permohonan belum sesuai ketentuan.'"
                                                class="px-2.5 py-1 rounded-lg bg-canvas hover:bg-amber-100 text-[11px] font-medium text-ink-muted hover:text-amber-900 border border-border-soft transition">
                                            Data belum sesuai
                                        </button>
                                        <button type="button"
                                                @click="catatanRevisi = 'Lampiran berkas persyaratan pemohon belum lengkap atau buram.'"
                                                class="px-2.5 py-1 rounded-lg bg-canvas hover:bg-amber-100 text-[11px] font-medium text-ink-muted hover:text-amber-900 border border-border-soft transition">
                                            Lampiran belum lengkap
                                        </button>
                                        <button type="button"
                                                @click="catatanRevisi = 'Perlu konfirmasi ulang data keperluan kepada pemohon / RT setempat.'"
                                                class="px-2.5 py-1 rounded-lg bg-canvas hover:bg-amber-100 text-[11px] font-medium text-ink-muted hover:text-amber-900 border border-border-soft transition">
                                            Perlu konfirmasi pemohon
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button"
                                        :disabled="isSubmitting"
                                        @click="if (!isSubmitting) showModalKembalikan = false"
                                        class="px-4 py-2.5 rounded-xl border border-border-soft hover:bg-canvas text-ink-muted hover:text-ink text-xs font-bold transition disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px]">
                                    Batal
                                </button>
                                <button type="submit"
                                        :disabled="isSubmitting"
                                        class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed min-h-[44px]">
                                    <template x-if="isSubmitting">
                                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="isSubmitting ? 'Mengirim Catatan...' : 'Kirim Catatan Koreksi'">Kirim Catatan Koreksi</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
