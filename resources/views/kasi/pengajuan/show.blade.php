<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Verifikasi Pengajuan #{{ $pengajuan->no_pengajuan }}</x-slot>
    <x-slot name="title">Verifikasi #{{ $pengajuan->no_pengajuan }} — SIRUKTI</x-slot>
    <x-slot name="header">
        @php
            $rawHpHeader = preg_replace('/[^0-9]/', '', $pengajuan->warga->pengguna->no_hp ?? '');
            if (str_starts_with($rawHpHeader, '0')) {
                $rawHpHeader = '62' . substr($rawHpHeader, 1);
            } elseif (!str_starts_with($rawHpHeader, '62') && !empty($rawHpHeader)) {
                $rawHpHeader = '62' . $rawHpHeader;
            }
            $pesanWaHeader = rawurlencode("Halo Bapak/Ibu {$pengajuan->warga->pengguna->nama}, kami dari Kantor Kampung Rukti Harjo terkait permohonan surat {$pengajuan->layanan->nama_layanan} (No. {$pengajuan->no_pengajuan}).");
        @endphp
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs font-semibold text-ink-muted">
                    <a href="{{ route('kasi.pengajuan.index') }}" class="hover:text-primary transition">Antrean</a>
                    <span>/</span>
                    <div class="inline-flex items-center gap-1.5 font-mono text-ink bg-canvas px-2.5 py-0.5 rounded-lg border border-border-soft">
                        <span>{{ $pengajuan->no_pengajuan }}</span>
                        <button type="button" @click="salinTeks('{{ $pengajuan->no_pengajuan }}', 'Nomor Registrasi')" class="text-ink-muted hover:text-primary transition p-0.5" title="Salin Nomor Registrasi">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                        Verifikasi Permohonan: {{ $pengajuan->layanan->nama_layanan }}
                    </h1>
                    <x-badge-status-pengajuan :status="$pengajuan->status" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(!empty($rawHpHeader))
                    <a
                        href="https://wa.me/{{ $rawHpHeader }}?text={{ $pesanWaHeader }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition shadow-sm"
                        title="Chat WhatsApp Langsung ke Pemohon"
                    >
                        <svg class="w-4 h-4 fill-[#25D366]" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.301-.15-1.782-.88-2.058-.98-.276-.1-.476-.15-.676.15-.2.301-.776.98-.952 1.18-.175.201-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.675-2.085-.175-.3-.019-.463.131-.613.136-.135.301-.351.451-.526.15-.176.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.15-.676-1.631-.926-2.235-.244-.589-.493-.509-.676-.519-.175-.01-.376-.01-.577-.01-.2 0-.526.075-.802.376-.276.301-1.053 1.029-1.053 2.509 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.14 4.542.718.31 1.279.495 1.716.634.721.23 1.378.197 1.9.12.58-.088 1.782-.728 2.033-1.432.25-.703.25-1.305.175-1.431-.075-.126-.275-.201-.576-.351zM12.04 2C6.544 2 2.08 6.464 2.08 11.96c0 1.84.502 3.564 1.374 5.045L2 22l5.142-1.35c1.42.776 3.04 1.218 4.76 1.218 5.496 0 9.96-4.464 9.96-9.96C21.862 6.464 17.436 2 12.04 2zm0 18.234c-1.58 0-3.07-.42-4.364-1.155l-.313-.18-3.044.798.813-2.966-.2-.317a8.214 8.214 0 01-1.258-4.454c0-4.562 3.712-8.274 8.274-8.274 4.562 0 8.274 3.712 8.274 8.274 0 4.562-3.712 8.274-8.274 8.274z"/>
                        </svg>
                        <span class="hidden sm:inline">WhatsApp Pemohon</span>
                        <span class="sm:hidden">WA</span>
                    </a>
                @endif

                @if(in_array($pengajuan->status, ['diproses', 'menunggu_ttd', 'selesai']))
                    <a href="{{ route('kasi.pengajuan.draft', $pengajuan) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-primary/30 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Pratinjau Draft Surat</span>
                    </a>
                @endif

                <a href="{{ route('kasi.pengajuan.index') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border-soft bg-surface hover:bg-canvas text-xs font-bold text-ink transition shadow-sm">
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Antrean</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        activeTab: 'berkas',
        showModalTolak: {{ ($errors->has('catatan_petugas') || old('aksi') === 'tolak') ? 'true' : 'false' }},
        aksiSubmit: 'proses',
        toastOpen: false,
        toastMessage: '',
        salinTeks(teks, label) {
            navigator.clipboard.writeText(teks).then(() => {
                this.toastMessage = label + ' berhasil disalin!';
                this.toastOpen = true;
                setTimeout(() => { this.toastOpen = false; }, 2500);
            });
        },
        submitWithAction(actionName) {
            this.aksiSubmit = actionName;
            $nextTick(() => {
                document.getElementById('input-aksi').value = actionName;
                document.getElementById('form-verifikasi').submit();
            });
        },
        init() {
            if (this.showModalTolak) {
                document.body.classList.add('overflow-y-hidden');
                $nextTick(() => {
                    this.$refs.catatanTolakInput?.focus();
                });
            }
            this.$watch('showModalTolak', value => {
                if (value) {
                    document.body.classList.add('overflow-y-hidden');
                    $nextTick(() => {
                        this.$refs.catatanTolakInput?.focus();
                    });
                } else {
                    document.body.classList.remove('overflow-y-hidden');
                }
            });
        }
    }" class="space-y-6">

        <!-- Toast Pop-up Notifikasi Salin Nomor -->
        <div
            x-show="toastOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            class="fixed bottom-24 sm:bottom-8 right-4 sm:right-8 z-50 flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-[#0D2616] text-white text-xs font-semibold shadow-2xl border border-emerald-800"
        >
            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span x-text="toastMessage">Nomor berhasil disalin!</span>
        </div>

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
                    <span>Terdapat kendala pada verifikasi permohonan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Banner Status Keterangan Khusus -->
        @if($pengajuan->status === 'kurang_berkas')
            <div class="p-4 rounded-card bg-orange-50 border border-orange-200 text-orange-900 flex items-start gap-3 shadow-sm">
                <div class="p-2 rounded-xl bg-orange-100 text-orange-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Status: Menunggu Perbaikan Berkas oleh Warga</h3>
                    <p class="text-xs text-orange-800 mt-0.5">
                        Permohonan ini telah dikembalikan ke pemohon untuk diperbaiki berkas persyaratannya. Warga dapat mengunggah ulang dokumen pengganti melalui portal warga.
                    </p>
                    @if($pengajuan->catatan_petugas)
                        <div class="mt-2 text-xs font-semibold bg-orange-100/70 p-2 rounded-lg border border-orange-200">
                            Catatan Petugas: {{ $pengajuan->catatan_petugas }}
                        </div>
                    @endif
                </div>
            </div>
        @elseif($pengajuan->status === 'diproses')
            <div class="p-4 rounded-card bg-blue-50 border border-blue-200 text-blue-900 flex items-start gap-3 shadow-sm">
                <div class="p-2 rounded-xl bg-blue-100 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Status: Berkas Lengkap & Sedang Diproses</h3>
                    <p class="text-xs text-blue-800 mt-0.5">
                        Seluruh berkas persyaratan telah diverifikasi sesuai. Anda dapat memeriksa draft surat dan mengajukan permohonan ke Kepala Kampung untuk penandatanganan resmi.
                    </p>
                </div>
            </div>
        @elseif($pengajuan->status === 'menunggu_ttd')
            <div class="p-4 rounded-card bg-purple-50 border border-purple-200 text-purple-900 flex items-start gap-3 shadow-sm">
                <div class="p-2 rounded-xl bg-purple-100 text-purple-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Status: Menunggu TTD Kepala Kampung</h3>
                    <p class="text-xs text-purple-800 mt-0.5">
                        Permohonan telah diajukan ke meja Kepala Kampung untuk ditinjau dan ditandatangani secara digital.
                    </p>
                </div>
            </div>
        @elseif($pengajuan->status === 'ditolak')
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-sm">
                <div class="p-2 rounded-xl bg-rose-100 text-rose-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Status: Permohonan Ditolak</h3>
                    <p class="text-xs text-rose-800 mt-0.5">
                        Permohonan ini telah ditolak secara permanen. Status tidak dapat diubah kembali.
                    </p>
                    @if($pengajuan->catatan_petugas)
                        <div class="mt-2 text-xs font-semibold bg-rose-100/70 p-2 rounded-lg border border-rose-200">
                            Alasan Penolakan: {{ $pengajuan->catatan_petugas }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Segmented Tabs Navigasi Mobile (< lg) -->
        <div class="lg:hidden sticky top-16 z-20 bg-canvas/95 backdrop-blur-md pt-1 pb-2">
            <div class="grid grid-cols-3 p-1 bg-surface border border-border-soft rounded-2xl shadow-xs gap-1 text-xs font-bold">
                <button
                    type="button"
                    @click="activeTab = 'berkas'"
                    :class="activeTab === 'berkas' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="py-2.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate">Berkas &amp; Isian</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'pemohon'"
                    :class="activeTab === 'pemohon' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="py-2.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="truncate">Pemohon</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'riwayat'"
                    :class="activeTab === 'riwayat' ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas'"
                    class="py-2.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="truncate">Riwayat &amp; Info</span>
                </button>
            </div>
        </div>

        <!-- Form Terpadu Verifikasi -->
        <form id="form-verifikasi" method="POST" action="{{ route('kasi.pengajuan.verifikasi', $pengajuan) }}">
            @csrf
            <input type="hidden" name="aksi" id="input-aksi" value="proses">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Data Pemohon & Isian Formulir & Berkas (2 Cols) -->
                <div class="lg:col-span-2 space-y-6" :class="(activeTab === 'berkas' || activeTab === 'pemohon') ? 'block' : 'hidden lg:block'">

                    <!-- 1. Kartu Data Pemohon -->
                    <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeTab === 'pemohon' ? 'block' : 'hidden lg:block'">
                        <div class="flex items-center justify-between border-b border-border-soft pb-3">
                            <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Identitas Pemohon</span>
                            </h2>
                            <span class="text-xs font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-full border border-primary/20">
                                Warga Terverifikasi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                            <div>
                                <span class="text-ink-muted block text-xs">Nama Lengkap</span>
                                <span class="font-bold text-ink text-sm">{{ $pengajuan->warga->pengguna->nama }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Nomor Induk Kependudukan (NIK)</span>
                                <span class="font-mono font-bold text-ink text-sm">{{ $pengajuan->warga->nik }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Nomor Kartu Keluarga (KK)</span>
                                <span class="font-mono font-bold text-ink">{{ $pengajuan->warga->no_kk ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Nomor WhatsApp / HP</span>
                                @php
                                    $rawHp = preg_replace('/[^0-9]/', '', $pengajuan->warga->pengguna->no_hp ?? '');
                                    if (str_starts_with($rawHp, '0')) {
                                        $rawHp = '62' . substr($rawHp, 1);
                                    } elseif (!str_starts_with($rawHp, '62') && !empty($rawHp)) {
                                        $rawHp = '62' . $rawHp;
                                    }
                                    $pesanWa = rawurlencode("Halo Bapak/Ibu {$pengajuan->warga->pengguna->nama}, kami dari Kantor Kampung Rukti Harjo terkait permohonan surat {$pengajuan->layanan->nama_layanan} (No. {$pengajuan->no_pengajuan}).");
                                @endphp
                                <div class="flex items-center justify-between gap-2 mt-0.5">
                                    <span class="font-bold text-ink font-mono">{{ $pengajuan->warga->pengguna->no_hp ?: '-' }}</span>
                                    @if(!empty($rawHp))
                                        <a
                                            href="https://wa.me/{{ $rawHp }}?text={{ $pesanWa }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition shadow-sm active:scale-95 shrink-0"
                                            title="Kirim Pesan WhatsApp Otomatis ke Pemohon"
                                        >
                                            <svg class="w-3.5 h-3.5 fill-[#25D366]" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.301-.15-1.782-.88-2.058-.98-.276-.1-.476-.15-.676.15-.2.301-.776.98-.952 1.18-.175.201-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.675-2.085-.175-.3-.019-.463.131-.613.136-.135.301-.351.451-.526.15-.176.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.15-.676-1.631-.926-2.235-.244-.589-.493-.509-.676-.519-.175-.01-.376-.01-.577-.01-.2 0-.526.075-.802.376-.276.301-1.053 1.029-1.053 2.509 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.14 4.542.718.31 1.279.495 1.716.634.721.23 1.378.197 1.9.12.58-.088 1.782-.728 2.033-1.432.25-.703.25-1.305.175-1.431-.075-.126-.275-.201-.576-.351zM12.04 2C6.544 2 2.08 6.464 2.08 11.96c0 1.84.502 3.564 1.374 5.045L2 22l5.142-1.35c1.42.776 3.04 1.218 4.76 1.218 5.496 0 9.96-4.464 9.96-9.96C21.862 6.464 17.436 2 12.04 2zm0 18.234c-1.58 0-3.07-.42-4.364-1.155l-.313-.18-3.044.798.813-2.966-.2-.317a8.214 8.214 0 01-1.258-4.454c0-4.562 3.712-8.274 8.274-8.274 4.562 0 8.274 3.712 8.274 8.274 0 4.562-3.712 8.274-8.274 8.274z"/>
                                            </svg>
                                            <span>Hubungi WA</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Tempat & Tanggal Lahir</span>
                                <span class="text-ink">
                                    {{ $pengajuan->warga->tempat_lahir ?: '-' }},
                                    {{ $pengajuan->warga->tgl_lahir ? tgl_indo($pengajuan->warga->tgl_lahir) : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Jenis Kelamin & Agama</span>
                                <span class="text-ink">
                                    {{ $pengajuan->warga->jenis_kelamin === 'L' ? 'Laki-laki' : ($pengajuan->warga->jenis_kelamin === 'P' ? 'Perempuan' : '-') }},
                                    {{ $pengajuan->warga->agama ?: '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Pekerjaan</span>
                                <span class="text-ink">{{ $pengajuan->warga->pekerjaan ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-ink-muted block text-xs">Dusun / RT / RW</span>
                                <span class="text-ink">
                                    {{ $pengajuan->warga->dusun?->nama_dusun ?? 'Dusun -' }} /
                                    RT {{ str_pad((string) $pengajuan->warga->rt, 3, '0', STR_PAD_LEFT) }} /
                                    RW {{ str_pad((string) $pengajuan->warga->rw, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-ink-muted block text-xs">Alamat KTP Lengkap</span>
                                <span class="text-ink font-medium">{{ $pengajuan->warga->alamat ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Rincian Data Isian Permohonan -->
                    <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeTab === 'berkas' ? 'block' : 'hidden lg:block'">
                        <div class="flex items-center justify-between border-b border-border-soft pb-3">
                            <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Rincian Isian Formulir Permohonan</span>
                            </h2>
                            <span class="text-xs font-mono font-bold text-ink-muted">
                                Kode: {{ $pengajuan->layanan->kode }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                            @php
                                $dataIsian = $pengajuan->data_isian ?? [];
                            @endphp

                            @if($pengajuan->layanan->kode === 'SKCK')
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Keperluan SKCK</span>
                                    <span class="font-bold text-ink text-sm">{{ $dataIsian['keperluan'] ?? '-' }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Keterangan Riwayat Pidana</span>
                                    <span class="text-ink">{{ $dataIsian['riwayat_pidana'] ?? 'Tidak ada catatan pidana / berkelakuan baik' }}</span>
                                </div>
                            @elseif($pengajuan->layanan->kode === 'DOM')
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Alamat Domisili Sekarang</span>
                                    <span class="font-bold text-ink text-sm">{{ $dataIsian['alamat_domisili'] ?? ($pengajuan->warga->alamat ?? '-') }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Lama Menetap / Tinggal</span>
                                    <span class="text-ink font-semibold">
                                        {{ isset($dataIsian['lama_tinggal_tahun']) ? $dataIsian['lama_tinggal_tahun'] . ' Tahun' : (!empty($dataIsian['sejak_tanggal']) ? 'Sejak ' . tgl_indo($dataIsian['sejak_tanggal']) : '-') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Keperluan / Alasan</span>
                                    <span class="text-ink">{{ $dataIsian['keperluan'] ?? ($dataIsian['alasan'] ?? '-') }}</span>
                                </div>
                                @if(!empty($dataIsian['alamat_asal']))
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Alamat KTP Asal</span>
                                    <span class="text-ink">{{ $dataIsian['alamat_asal'] }}</span>
                                </div>
                                @endif
                            @elseif($pengajuan->layanan->kode === 'SKTM')
                                <div>
                                    <span class="text-ink-muted block text-xs">Pekerjaan Kepala Keluarga/Warga</span>
                                    <span class="font-bold text-ink">{{ $pengajuan->warga->pekerjaan ?: ($dataIsian['pekerjaan'] ?? '-') }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Penghasilan Rata-rata Bulanan</span>
                                    <span class="font-bold text-ink font-mono text-sm">
                                        {{ isset($dataIsian['penghasilan_per_bulan']) ? rupiah((int)$dataIsian['penghasilan_per_bulan']) : (isset($dataIsian['penghasilan']) ? rupiah((int)$dataIsian['penghasilan']) : '-') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Jumlah Tanggungan Keluarga</span>
                                    <span class="text-ink font-bold">{{ $dataIsian['jumlah_tanggungan'] ?? '-' }} Jiwa</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Tujuan / Keperluan SKTM</span>
                                    <span class="text-ink font-medium">{{ $dataIsian['tujuan'] ?? ($dataIsian['keperluan'] ?? '-') }}</span>
                                </div>
                            @elseif($pengajuan->layanan->kode === 'SKU')
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Nama Usaha</span>
                                    <span class="font-bold text-ink text-sm">{{ $dataIsian['nama_usaha'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Jenis / Bidang Usaha</span>
                                    <span class="text-ink font-bold">{{ $dataIsian['jenis_usaha'] ?? ($dataIsian['bidang_usaha'] ?? '-') }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-xs">Lama Usaha Berjalan</span>
                                    <span class="text-ink font-bold">
                                        {{ isset($dataIsian['lama_usaha_tahun']) ? $dataIsian['lama_usaha_tahun'] . ' Tahun' : (!empty($dataIsian['tahun_mulai']) ? 'Mulai ' . $dataIsian['tahun_mulai'] : '-') }}
                                    </span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-ink-muted block text-xs">Alamat Lokasi Usaha</span>
                                    <span class="text-ink font-medium">{{ $dataIsian['alamat_usaha'] ?? '-' }}</span>
                                </div>
                                @if(isset($dataIsian['modal_usaha']))
                                <div>
                                    <span class="text-ink-muted block text-xs">Modal Usaha Awal</span>
                                    <span class="font-mono text-ink">
                                        {{ rupiah((int)$dataIsian['modal_usaha']) }}
                                    </span>
                                </div>
                                @endif
                            @else
                                @foreach($dataIsian as $k => $v)
                                    <div>
                                        <span class="text-ink-muted block text-xs">{{ ucwords(str_replace('_', ' ', $k)) }}</span>
                                        <span class="font-medium text-ink">{{ is_array($v) ? json_encode($v) : $v }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- 3. Berkas Persyaratan & Lembar Verifikasi -->
                    <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-5" :class="activeTab === 'berkas' ? 'block' : 'hidden lg:block'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-border-soft pb-3">
                            <div>
                                <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span>Verifikasi Berkas Persyaratan</span>
                                </h2>
                                <p class="text-xs text-ink-muted mt-0.5">
                                    Periksa keabsahan dan kejelasan setiap berkas yang diunggah pemohon
                                </p>
                            </div>
                            <span class="text-xs font-semibold text-ink-muted">
                                Total: {{ $pengajuan->berkas->count() }} Berkas
                            </span>
                        </div>

                        <!-- Daftar Berkas Pengajuan -->
                        <div class="space-y-4">
                            @foreach($pengajuan->berkas as $b)
                                @php
                                    $oldStatus = old("berkas.{$b->id_berkas}.status_verifikasi", $b->status_verifikasi);
                                    $oldCatatan = old("berkas.{$b->id_berkas}.catatan", $b->catatan ?? '');
                                    $hasCatatanError = $errors->has("berkas.{$b->id_berkas}.catatan");
                                @endphp
                                <div x-data="{
                                    statusVerifikasi: {{ Js::from($oldStatus) }},
                                    catatan: {{ Js::from($oldCatatan) }}
                                }" class="rounded-xl border p-4 space-y-3 transition"
                                     :class="{
                                         'border-emerald-300 bg-emerald-50/20': statusVerifikasi === 'sesuai',
                                         'border-orange-300 bg-orange-50/20': statusVerifikasi === 'perlu_perbaikan',
                                         'border-border-soft bg-canvas/30': statusVerifikasi === 'menunggu'
                                     }">

                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                        <!-- Info Berkas -->
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-ink">
                                                    {{ $b->syarat?->nama_syarat ?? 'Persyaratan' }}
                                                </h4>
                                                @if($b->syarat?->wajib)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Wajib</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-ink-muted">
                                                {{ $b->syarat?->petunjuk }}
                                            </p>
                                            <div class="flex items-center gap-3 text-xs text-ink-muted pt-1">
                                                <span class="font-mono">{{ $b->nama_file_asli }}</span>
                                                <span>•</span>
                                                <span>{{ number_format($b->ukuran_kb, 0, ',', '.') }} KB</span>
                                                <span>•</span>
                                                <span class="uppercase text-[10px] font-bold">{{ $b->mime }}</span>
                                            </div>
                                        </div>

                                        <!-- Tombol Lihat Berkas -->
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('pengajuan.berkas', [$pengajuan, $b->id_berkas]) }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border-soft bg-surface hover:bg-canvas text-ink text-xs font-bold transition shadow-sm">
                                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Buka Berkas</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Kontrol Keputusan Verifikasi Berkas (Hanya aktif jika status bukan final) -->
                                    @if(!in_array($pengajuan->status, ['selesai', 'ditolak']))
                                        <!-- Hidden input untuk selalu mengirim status_verifikasi yang sinkron dengan Alpine -->
                                        <input type="hidden"
                                               name="berkas[{{ $b->id_berkas }}][status_verifikasi]"
                                               :value="statusVerifikasi">

                                        <div class="pt-2 border-t border-border-soft/60 space-y-3">
                                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                                                <span class="font-bold text-ink">Hasil Verifikasi Berkas:</span>

                                                <!-- Tombol Sesuai -->
                                                <button type="button"
                                                        @click="statusVerifikasi = (statusVerifikasi === 'sesuai' ? 'menunggu' : 'sesuai')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold transition select-none cursor-pointer"
                                                        :class="statusVerifikasi === 'sesuai' ? 'border-emerald-500 bg-emerald-600 text-white shadow-sm' : 'border-border-soft bg-surface text-ink-muted hover:text-ink'">
                                                    <span>✓ Sesuai &amp; Jelas</span>
                                                </button>

                                                <!-- Tombol Perlu Perbaikan -->
                                                <button type="button"
                                                        @click="statusVerifikasi = (statusVerifikasi === 'perlu_perbaikan' ? 'menunggu' : 'perlu_perbaikan')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold transition select-none cursor-pointer"
                                                        :class="statusVerifikasi === 'perlu_perbaikan' ? 'border-orange-500 bg-orange-600 text-white shadow-sm' : 'border-border-soft bg-surface text-ink-muted hover:text-ink'">
                                                    <span>⚠️ Perlu Perbaikan</span>
                                                </button>

                                                <!-- Tombol Reset ke Menunggu jika sudah dipilih -->
                                                <button type="button"
                                                        x-show="statusVerifikasi !== 'menunggu'"
                                                        @click="statusVerifikasi = 'menunggu'; catatan = ''"
                                                        class="text-[11px] text-ink-muted hover:text-rose-600 underline transition cursor-pointer">
                                                    Reset ke Menunggu
                                                </button>
                                            </div>

                                            <!-- Field Alasan Perbaikan Berkas (Muncul bila Perlu Perbaikan) -->
                                            <div x-show="statusVerifikasi === 'perlu_perbaikan'"
                                                 x-cloak
                                                 class="space-y-1.5 pt-1">
                                                <label class="block text-xs font-bold text-orange-900">
                                                    Catatan / Alasan Perbaikan untuk Pemohon <span class="text-rose-600">*</span>
                                                </label>
                                                <input type="text"
                                                       name="berkas[{{ $b->id_berkas }}][catatan]"
                                                       x-model="catatan"
                                                       :disabled="statusVerifikasi !== 'perlu_perbaikan'"
                                                       placeholder="Contoh: Foto buram dan tulisan NIK tidak terbaca dengan jelas"
                                                       class="w-full px-3 py-2 rounded-lg border {{ $hasCatatanError ? 'border-rose-500 ring-1 ring-rose-500' : 'border-orange-300' }} bg-surface focus:ring-2 focus:ring-orange-500 text-xs text-ink placeholder-ink-muted">

                                                @error("berkas.{$b->id_berkas}.catatan")
                                                    <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                                                @enderror

                                                <!-- Preset Saran Cepat Alasan Perbaikan Berkas -->
                                                <div class="flex flex-wrap gap-1.5 pt-1">
                                                    <button type="button"
                                                            @click="catatan = 'Foto buram / tidak terbaca dengan jelas, harap foto ulang dokumen asli.'"
                                                            class="px-2 py-0.5 rounded-md bg-orange-100 hover:bg-orange-200 text-orange-900 text-[10px] font-semibold transition border border-orange-200">
                                                        Foto Buram
                                                    </button>
                                                    <button type="button"
                                                            @click="catatan = 'Sebagian sudut dokumen terpotong, pastikan seluruh lembar terlihat utuh.'"
                                                            class="px-2 py-0.5 rounded-md bg-orange-100 hover:bg-orange-200 text-orange-900 text-[10px] font-semibold transition border border-orange-200">
                                                        Dokumen Terpotong
                                                    </button>
                                                    <button type="button"
                                                            @click="catatan = 'Berkas yang dilampirkan bukan dokumen yang diminta atau masa berlaku telah habis.'"
                                                            class="px-2 py-0.5 rounded-md bg-orange-100 hover:bg-orange-200 text-orange-900 text-[10px] font-semibold transition border border-orange-200">
                                                        Berkas Tidak Sesuai
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- State Read-Only Saat Status Final -->
                                        <div class="pt-2 border-t border-border-soft/60 flex items-center justify-between text-xs">
                                            <span class="text-ink-muted">Status Verifikasi:</span>
                                            <span class="font-bold {{ $b->status_verifikasi === 'sesuai' ? 'text-emerald-700' : ($b->status_verifikasi === 'perlu_perbaikan' ? 'text-orange-700' : 'text-ink-muted') }}">
                                                {{ ucfirst($b->status_verifikasi) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Kolom Kanan: Timeline Riwayat & Ringkasan Petugas (1 Col) -->
                <div class="space-y-6" :class="activeTab === 'riwayat' ? 'block' : 'hidden lg:block'">

                    <!-- Kartu Informasi Pengajuan -->
                    <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4">
                        <h3 class="text-sm font-bold text-ink font-heading border-b border-border-soft pb-2.5">
                            Informasi Permohonan
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-ink-muted">No. Registrasi</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-bold text-ink">{{ $pengajuan->no_pengajuan }}</span>
                                    <button type="button" @click="salinTeks('{{ $pengajuan->no_pengajuan }}', 'No. Registrasi')" class="p-1 rounded text-ink-muted hover:text-primary hover:bg-canvas transition" title="Salin No. Registrasi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @if($pengajuan->surat?->no_surat)
                                <div class="flex items-center justify-between gap-2 bg-primary-soft/40 p-2 rounded-lg border border-primary/20">
                                    <span class="text-primary font-bold text-[11px]">No. Surat Resmi</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-primary text-[11px]">{{ $pengajuan->surat->no_surat }}</span>
                                        <button type="button" @click="salinTeks('{{ $pengajuan->surat->no_surat }}', 'Nomor Surat Resmi')" class="p-1 rounded text-primary hover:bg-surface transition" title="Salin No. Surat Resmi">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-ink-muted">Tanggal Masuk</span>
                                <span class="text-ink font-medium">{{ tgl_indo($pengajuan->tgl_pengajuan) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-ink-muted">Petugas Verifikator</span>
                                <span class="text-ink font-bold">{{ $pengajuan->verifikator?->nama ?? 'Belum Ditugaskan' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-ink-muted">Waktu Diproses</span>
                                <span class="text-ink font-medium">{{ $pengajuan->tgl_diproses ? tgl_indo($pengajuan->tgl_diproses) : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-ink-muted">Waktu Selesai</span>
                                <span class="text-ink font-medium">{{ $pengajuan->tgl_selesai ? tgl_indo($pengajuan->tgl_selesai) : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pelacak Riwayat Status (Timeline Tracker) -->
                    <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4">
                        <h3 class="text-sm font-bold text-ink font-heading border-b border-border-soft pb-2.5 flex items-center justify-between">
                            <span>Riwayat Aktivitas</span>
                            <span class="text-xs font-semibold text-ink-muted">{{ $pengajuan->statusLogs->count() }} log</span>
                        </h3>

                        <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-border-soft">
                            @forelse($pengajuan->statusLogs as $log)
                                <div class="relative text-xs space-y-1">
                                    <div class="absolute -left-6 top-0.5 w-3 h-3 rounded-full border-2 border-surface bg-primary"></div>
                                    <div class="flex items-center justify-between text-ink-muted text-[11px]">
                                        <span>{{ tgl_indo($log->created_at) }}</span>
                                        <span>{{ $log->pengguna?->nama ?? 'Sistem' }}</span>
                                    </div>
                                    <div class="font-bold text-ink">
                                        Perubahan Status:
                                        <span class="text-primary">{{ ucwords(str_replace('_', ' ', $log->status_baru)) }}</span>
                                    </div>
                                    @if($log->catatan)
                                        <div class="text-[11px] text-ink-muted bg-canvas p-2 rounded-lg border border-border-soft/60">
                                            {{ $log->catatan }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-ink-muted">Belum ada riwayat aktivitas.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <!-- Sticky Bottom Action Bar -->
        @if(!in_array($pengajuan->status, ['selesai', 'ditolak']))
            <div class="fixed bottom-0 inset-x-0 lg:left-64 bg-surface/95 backdrop-blur-md border-t border-border-soft p-3 sm:p-4 z-40 shadow-2xl safe-bottom">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-3">
                    <div class="text-xs text-ink-muted flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span>Status saat ini: <strong class="text-ink font-bold">{{ ucwords(str_replace('_', ' ', $pengajuan->status)) }}</strong></span>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-end">
                        <!-- Tombol Tolak (Semua status aktif) -->
                        <button type="button"
                                @click="showModalTolak = true"
                                class="flex-1 sm:flex-none px-3.5 py-2.5 rounded-xl border border-rose-300 hover:bg-rose-50 text-rose-700 text-xs font-bold transition shadow-sm text-center">
                            Tolak
                        </button>

                        @if($pengajuan->status === 'menunggu')
                            <!-- Tombol Minta Perbaikan -->
                            <button type="button"
                                    @click="submitWithAction('minta_perbaikan')"
                                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2.5 rounded-xl border border-orange-300 bg-orange-50 hover:bg-orange-100 text-orange-800 text-xs font-bold transition shadow-sm text-center">
                                Minta Perbaikan
                            </button>

                            <!-- Tombol Proses Pengajuan -->
                            <button type="button"
                                    @click="submitWithAction('proses')"
                                    class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-featured flex items-center justify-center gap-1.5 text-center">
                                <span>Verifikasi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        @elseif($pengajuan->status === 'diproses')
                            <!-- Tombol Ajukan ke Kepala Kampung -->
                            <button type="button"
                                    @click="submitWithAction('ajukan_ttd')"
                                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold transition shadow-featured flex items-center justify-center gap-1.5">
                                <span>Ajukan ke Kepala</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        @elseif($pengajuan->status === 'kurang_berkas')
                            <span class="text-xs font-semibold text-orange-800 px-3 py-2 rounded-xl bg-orange-50 border border-orange-200">
                                Menunggu unggah berkas pengganti dari warga pemohon
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Dialog Penolakan Pengajuan (Bottom Sheet di mobile, Dialog di Desktop) -->
        <template x-teleport="body">
            <div x-show="showModalTolak"
                 x-cloak
                 x-data="{ isSubmitting: false }"
                 @keydown.escape.window="if (!isSubmitting) showModalTolak = false"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-tolak-title">
                <!-- Backdrop Overlay (Hanya mem-blur latar belakang halaman) -->
                <div x-show="showModalTolak"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="if (!isSubmitting) showModalTolak = false"
                     class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>

                <!-- Panel Modal Container (Elevated dengan relative z-10) -->
                <div class="relative z-10 min-h-full flex items-end sm:items-center justify-center p-0 sm:p-4 text-center"
                     @click.self="if (!isSubmitting) showModalTolak = false">
                    <div x-show="showModalTolak"
                         @click.stop
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
                         class="w-full sm:max-w-lg bg-surface rounded-t-3xl sm:rounded-card border border-border-soft p-6 text-left shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">

                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-2xl bg-rose-100 text-rose-700 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 id="modal-tolak-title" class="text-base font-bold text-ink font-heading">
                                    Tolak Permohonan Surat
                                </h3>
                                <p class="text-xs text-ink-muted mt-1">
                                    Berikan alasan penolakan yang jelas agar dapat dipahami oleh pemohon. Permohonan yang ditolak tidak dapat diproses kembali.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('kasi.pengajuan.verifikasi', $pengajuan) }}" class="space-y-4"
                              @submit="isSubmitting = true">
                            @csrf
                            <input type="hidden" name="aksi" value="tolak">

                            <div>
                                <label for="alasan-penolakan-field" class="block text-xs font-bold text-ink mb-1.5">
                                    Alasan Penolakan <span class="text-rose-600">*</span>
                                </label>
                                <textarea id="alasan-penolakan-field"
                                          name="catatan_petugas"
                                          x-ref="catatanTolakInput"
                                          rows="3"
                                          required
                                          placeholder="Contoh: Pemohon tidak memenuhi persyaratan domisili minimum di Kampung Rukti Harjo."
                                          class="w-full p-3 rounded-xl border {{ $errors->has('catatan_petugas') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-border-soft' }} bg-canvas focus:bg-surface focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-xs text-ink placeholder-ink-muted">{{ old('catatan_petugas') }}</textarea>
                                @error('catatan_petugas')
                                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button"
                                        :disabled="isSubmitting"
                                        @click="if (!isSubmitting) showModalTolak = false"
                                        class="px-4 py-2.5 rounded-xl border border-border-soft hover:bg-canvas text-ink-muted hover:text-ink text-xs font-bold transition disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px]">
                                    Batalkan
                                </button>
                                <button type="submit"
                                        :disabled="isSubmitting"
                                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed min-h-[44px]">
                                    <template x-if="isSubmitting">
                                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="isSubmitting ? 'Memproses Penolakan...' : 'Konfirmasi Tolak Pengajuan'">Konfirmasi Tolak Pengajuan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
