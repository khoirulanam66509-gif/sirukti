<x-app-layout>
    <x-slot name="pageTitle">Persyaratan Layanan</x-slot>
    <x-slot name="title">Persyaratan Layanan — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-wide uppercase">
                        Pengaturan Sistem
                    </span>
                    <span class="text-ink-muted text-xs">•</span>
                    <span class="text-xs text-ink-muted flex items-center gap-1 font-medium">
                        <svg class="w-3.5 h-3.5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Regulasi Kampung Aktif
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                    Persyaratan Layanan Surat
                </h1>
                <p class="text-sm text-ink-muted max-w-3xl mt-1 leading-relaxed">
                    Kelola konfigurasi berkas persyaratan yang wajib diunggah warga untuk setiap jenis permohonan surat administrasi.
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2.5 px-4 py-2 rounded-full bg-surface shadow-sm border border-border-soft self-start md:self-auto">
                <div class="w-7 h-7 rounded-full bg-secondary/15 flex items-center justify-center text-secondary">
                    <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-ink leading-none">Konfigurasi Baku Pelayanan</span>
                    <span class="text-[11px] text-ink-muted mt-0.5">4 Layanan Standar Desa</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        addModalOpen: false,
        editModalOpen: false,
        editSyaratId: null,
        editNama: '',
        editPetunjuk: '',
        editUrutan: 1,
        editWajib: true,
        editStatusAktif: true,
        openEditModal(id, nama, petunjuk, urutan, wajib, aktif) {
            this.editSyaratId = id;
            this.editNama = nama;
            this.editPetunjuk = petunjuk;
            this.editUrutan = urutan;
            this.editWajib = wajib;
            this.editStatusAktif = aktif;
            this.editModalOpen = true;
        }
    }">

        <!-- Flash Message Status -->
        @if (session('success'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('success') }}
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

        @if ($errors->any())
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kesalahan pada isian persyaratan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mobile Horizontal Pill Tabs (< lg) -->
        <div class="lg:hidden flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            @foreach ($daftarLayanan as $layanan)
                @php
                    $isSelected = $layananAktif && $layananAktif->id_layanan === $layanan->id_layanan;
                @endphp
                <a
                    href="{{ route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan]) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition border {{ $isSelected ? 'bg-primary text-white border-primary shadow-sm' : 'bg-surface text-ink-muted hover:text-ink border-border-soft' }}"
                >
                    <x-badge-layanan :kode="$layanan->kode" variant="icon" size="sm" class="{{ $isSelected ? '!bg-white/20 !text-white' : '' }}" />
                    <span>{{ $layanan->nama_layanan }}</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isSelected ? 'bg-white/20 text-white' : 'bg-canvas text-ink-muted' }}">
                        {{ $layanan->syarat->count() }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Two-Column Master-Detail Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: DAFTAR LAYANAN (4 Cols Desktop) -->
            <div class="hidden lg:flex lg:col-span-4 flex-col gap-4">
                <div class="bg-surface rounded-card p-5 shadow-card border border-border-soft flex flex-col">
                    <div class="flex items-center justify-between pb-3.5 mb-3 border-b border-border-soft">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="font-bold text-ink text-base">Daftar Layanan</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-canvas text-ink-muted text-xs font-bold">
                            4 Jenis
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-canvas mb-4 flex items-start gap-2.5 text-xs text-ink-muted">
                        <svg class="w-4 h-4 text-accent shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="leading-relaxed">
                            Pilih jenis surat untuk melihat dan mengubah konfigurasi berkas persyaratan. Jenis surat bersifat baku.
                        </p>
                    </div>

                    <!-- Service List -->
                    <div class="flex flex-col gap-2">
                        @foreach ($daftarLayanan as $layanan)
                            @php
                                $isSelected = $layananAktif && $layananAktif->id_layanan === $layanan->id_layanan;
                            @endphp
                            <a
                                href="{{ route('kasi.layanan.index', ['layanan_id' => $layanan->id_layanan]) }}"
                                class="flex items-center justify-between p-3.5 rounded-2xl transition border {{ $isSelected ? 'bg-primary-light border-primary/30 shadow-sm' : 'bg-canvas/50 border-border-soft hover:bg-canvas' }}"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shrink-0 {{ $isSelected ? 'bg-primary text-white shadow-xs' : 'bg-surface text-primary border border-border-soft' }}">
                                        {{ $layanan->kode }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-sm truncate {{ $isSelected ? 'text-primary' : 'text-ink' }}">
                                            {{ $layanan->nama_layanan }}
                                        </span>
                                        <span class="text-xs text-ink-muted">
                                            {{ $layanan->syarat->count() }} Berkas Persyaratan
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if ($isSelected)
                                        <span class="px-2 py-0.5 rounded-full bg-primary text-white text-[11px] font-bold">
                                            Aktif
                                        </span>
                                    @else
                                        <svg class="w-4 h-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Guidance Note -->
                <div class="p-5 rounded-card bg-surface shadow-card border border-border-soft flex items-start gap-3">
                    <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-ink">Integritas Riwayat Berkas</span>
                        <p class="text-xs text-ink-muted mt-1 leading-relaxed">
                            Persyaratan yang telah dipakai oleh warga dalam berkas pengajuan hanya dapat <strong>dinonaktifkan</strong> agar riwayat arsip lampiran tetap utuh.
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: KONFIGURASI SYARAT LAYANAN AKTIF (8 Cols Desktop) -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                @if ($layananAktif)
                    <div class="bg-surface rounded-card p-5 sm:p-7 shadow-card border border-border-soft flex flex-col">

                        <!-- Section Header -->
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-6 mb-6 border-b border-border-soft gap-4">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-full bg-accent-light text-amber-900 text-xs font-bold font-mono">
                                        Kode: {{ $layananAktif->kode }}
                                    </span>
                                    <span class="text-xs text-ink-muted">Urutan Ke-{{ $layananAktif->urutan }}</span>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">
                                    {{ $layananAktif->nama_layanan }}
                                </h2>
                                <p class="text-xs sm:text-sm text-ink-muted mt-1">
                                    {{ $layananAktif->deskripsi ?: 'Berkas ini akan muncul pada langkah ke-3 alur pengajuan mandiri warga di aplikasi SIRUKTI.' }}
                                </p>
                            </div>

                            <!-- Button Tambah Persyaratan -->
                            <button
                                type="button"
                                @click="addModalOpen = true"
                                class="min-h-[44px] px-5 rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition inline-flex items-center gap-2 shrink-0 self-start"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Persyaratan</span>
                            </button>
                        </div>

                        <!-- List of Requirements -->
                        <div class="flex flex-col gap-3.5">
                            @forelse ($layananAktif->syarat as $syarat)
                                <div class="flex flex-col md:flex-row md:items-center justify-between p-4 rounded-2xl bg-canvas/60 border border-border-soft hover:bg-canvas transition gap-4">
                                    <!-- Info Syarat -->
                                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            {{ $syarat->urutan }}
                                        </div>
                                        <div class="flex flex-col min-w-0 pr-2">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-bold text-ink text-sm">{{ $syarat->nama_syarat }}</span>
                                                <span class="px-2 py-0.5 rounded-full bg-surface border border-border-soft text-[10px] font-semibold text-ink-muted">
                                                    JPG / PNG / PDF
                                                </span>
                                                @if ($syarat->wajib)
                                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                                                        Wajib
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-medium">
                                                        Opsional
                                                    </span>
                                                @endif
                                                @if ($syarat->status_aktif)
                                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-medium">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-ink-muted mt-1 leading-relaxed">
                                                {{ $syarat->petunjuk ?: 'Unggah dokumen pendukung asli yang jelas dan dapat dibaca.' }}
                                            </p>
                                            @if ($syarat->berkas_count > 0)
                                                <span class="text-[11px] text-ink-muted mt-1">
                                                    Digunakan pada {{ $syarat->berkas_count }} berkas pengajuan warga.
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('kasi.layanan.syarat.toggle', [$layananAktif, $syarat]) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ $syarat->status_aktif ? 'bg-surface hover:bg-canvas text-ink-muted border-border-soft' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }}"
                                                title="{{ $syarat->status_aktif ? 'Nonaktifkan Syarat' : 'Aktifkan Syarat' }}"
                                            >
                                                {{ $syarat->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <!-- Edit Button -->
                                        <button
                                            type="button"
                                            @click="openEditModal(
                                                {{ $syarat->id_syarat }},
                                                '{{ addslashes($syarat->nama_syarat) }}',
                                                '{{ addslashes($syarat->petunjuk ?? '') }}',
                                                {{ $syarat->urutan }},
                                                {{ $syarat->wajib ? 'true' : 'false' }},
                                                {{ $syarat->status_aktif ? 'true' : 'false' }}
                                            )"
                                            class="px-3 py-1.5 rounded-full bg-surface hover:bg-canvas text-ink font-semibold text-xs border border-border-soft transition inline-flex items-center gap-1"
                                        >
                                            <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Ubah</span>
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('kasi.layanan.syarat.destroy', [$layananAktif, $syarat]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus persyaratan ini?')"
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-ink-muted hover:text-rose-600 hover:bg-rose-50 transition {{ $syarat->berkas_count > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                title="{{ $syarat->berkas_count > 0 ? 'Tidak dapat dihapus karena sudah dipakai berkas' : 'Hapus Persyaratan' }}"
                                                @if ($syarat->berkas_count > 0) disabled @endif
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-ink-muted text-sm">
                                    Belum ada berkas persyaratan untuk layanan ini.
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endif
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- MODAL TAMBAH PERSYARATAN -->
        <!-- ======================================================== -->
        <div
            x-show="addModalOpen"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="addModalOpen"
                @click="addModalOpen = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-[#1E2A22]/50 backdrop-blur-xs"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="addModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                class="relative bg-surface rounded-t-[28px] sm:rounded-card shadow-modal border border-border-soft w-full max-w-lg p-6 overflow-hidden z-10"
            >
                @if ($layananAktif)
                    <form action="{{ route('kasi.layanan.syarat.store', $layananAktif) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="flex items-start justify-between pb-3 border-b border-border-soft">
                            <div>
                                <h3 class="text-lg font-bold text-ink">Tambah Persyaratan Baru</h3>
                                <p class="text-xs text-ink-muted">Layanan: {{ $layananAktif->nama_layanan }}</p>
                            </div>
                            <button type="button" @click="addModalOpen = false" class="w-8 h-8 rounded-full bg-canvas text-ink-muted hover:text-ink flex items-center justify-center transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Nama Syarat -->
                        <div class="space-y-1.5">
                            <label for="nama_syarat_add" class="block text-xs font-semibold text-ink">
                                Nama Berkas Persyaratan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nama_syarat"
                                id="nama_syarat_add"
                                required
                                placeholder="Contoh: Foto Pengantar RT/RW Asli"
                                class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                            >
                        </div>

                        <!-- Petunjuk -->
                        <div class="space-y-1.5">
                            <label for="petunjuk_add" class="block text-xs font-semibold text-ink">
                                Petunjuk Unggah Untuk Warga
                            </label>
                            <textarea
                                name="petunjuk"
                                id="petunjuk_add"
                                rows="2"
                                placeholder="Jelaskan ketentuan berkas, misal: Foto jelas 4 sudut terlihat"
                                class="w-full p-3 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                            ></textarea>
                        </div>

                        <!-- Urutan -->
                        <div class="space-y-1.5">
                            <label for="urutan_add" class="block text-xs font-semibold text-ink">
                                Urutan Tampilan
                            </label>
                            <input
                                type="number"
                                name="urutan"
                                id="urutan_add"
                                min="1"
                                value="{{ ($layananAktif->syarat->max('urutan') ?? 0) + 1 }}"
                                class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                            >
                        </div>

                        <!-- Checkboxes: Wajib & Aktif -->
                        <div class="pt-2 space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="wajib" value="0">
                                <input
                                    type="checkbox"
                                    name="wajib"
                                    value="1"
                                    checked
                                    class="rounded text-primary focus:ring-primary border-border-soft"
                                >
                                <span class="text-xs font-semibold text-ink">Wajib diunggah oleh warga</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="status_aktif" value="0">
                                <input
                                    type="checkbox"
                                    name="status_aktif"
                                    value="1"
                                    checked
                                    class="rounded text-primary focus:ring-primary border-border-soft"
                                >
                                <span class="text-xs font-semibold text-ink">Langsung aktifkan persyaratan ini</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-border-soft">
                            <button
                                type="button"
                                @click="addModalOpen = false"
                                class="min-h-[44px] px-5 rounded-btn bg-canvas text-ink font-semibold text-sm hover:bg-border-soft transition border border-border-soft"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="min-h-[44px] px-6 rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition"
                            >
                                Simpan Persyaratan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL UBAH PERSYARATAN -->
        <!-- ======================================================== -->
        <div
            x-show="editModalOpen"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="editModalOpen"
                @click="editModalOpen = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-[#1E2A22]/50 backdrop-blur-xs"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="editModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                class="relative bg-surface rounded-t-[28px] sm:rounded-card shadow-modal border border-border-soft w-full max-w-lg p-6 overflow-hidden z-10"
            >
                @if ($layananAktif)
                    <form :action="'/kasi/layanan/{{ $layananAktif->id_layanan }}/syarat/' + editSyaratId" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="flex items-start justify-between pb-3 border-b border-border-soft">
                            <div>
                                <h3 class="text-lg font-bold text-ink">Ubah Konfigurasi Persyaratan</h3>
                                <p class="text-xs text-ink-muted">Layanan: {{ $layananAktif->nama_layanan }}</p>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="w-8 h-8 rounded-full bg-canvas text-ink-muted hover:text-ink flex items-center justify-center transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Nama Syarat -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-ink">
                                Nama Berkas Persyaratan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nama_syarat"
                                x-model="editNama"
                                required
                                class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                            >
                        </div>

                        <!-- Petunjuk -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-ink">
                                Petunjuk Unggah Untuk Warga
                            </label>
                            <textarea
                                name="petunjuk"
                                x-model="editPetunjuk"
                                rows="2"
                                class="w-full p-3 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                            ></textarea>
                        </div>

                        <!-- Urutan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-ink">
                                Urutan Tampilan
                            </label>
                            <input
                                type="number"
                                name="urutan"
                                x-model="editUrutan"
                                min="1"
                                class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                            >
                        </div>

                        <!-- Checkboxes: Wajib & Aktif -->
                        <div class="pt-2 space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="wajib" value="0">
                                <input
                                    type="checkbox"
                                    name="wajib"
                                    value="1"
                                    x-model="editWajib"
                                    class="rounded text-primary focus:ring-primary border-border-soft"
                                >
                                <span class="text-xs font-semibold text-ink">Wajib diunggah oleh warga</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="status_aktif" value="0">
                                <input
                                    type="checkbox"
                                    name="status_aktif"
                                    value="1"
                                    x-model="editStatusAktif"
                                    class="rounded text-primary focus:ring-primary border-border-soft"
                                >
                                <span class="text-xs font-semibold text-ink">Status Aktif</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-border-soft">
                            <button
                                type="button"
                                @click="editModalOpen = false"
                                class="min-h-[44px] px-5 rounded-btn bg-canvas text-ink font-semibold text-sm hover:bg-border-soft transition border border-border-soft"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="min-h-[44px] px-6 rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
