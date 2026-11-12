<x-app-layout>
    <x-slot name="pageTitle">Antrean Pengajuan</x-slot>
    <x-slot name="title">Antrean Verifikasi — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                    Antrean Verifikasi Surat
                </h1>
                <p class="text-sm text-ink-muted mt-1">
                    Kelola dan verifikasi permohonan surat administrasi warga Kampung Rukti Harjo
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span>Modul Aparatur Desa</span>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        cari: '{{ addslashes($cari) }}',
        status: '{{ $statusFilter }}',
        layanan: '{{ $layananFilter }}',
        isLoading: false,
        hasError: false,
        errorMessage: '',
        abortController: null,
        baseUrl: '{{ route('kasi.pengajuan.index') }}',

        buildUrl(page = null) {
            const params = new URLSearchParams();
            if (this.cari.trim() !== '') params.set('cari', this.cari.trim());
            if (this.status !== 'semua' && this.status !== '') params.set('status', this.status);
            if (this.layanan !== 'semua' && this.layanan !== '') params.set('layanan', this.layanan);
            if (page) params.set('page', page);
            const query = params.toString();
            return query ? `${this.baseUrl}?${query}` : this.baseUrl;
        },

        muatData(url, pushState = true) {
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            this.isLoading = true;
            this.hasError = false;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: this.abortController.signal
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data antrean');
                return response.text();
            })
            .then(html => {
                const container = document.getElementById('area-hasil-antrean');
                if (container) {
                    container.innerHTML = html;
                }
                if (pushState) {
                    window.history.replaceState({ url }, '', url);
                }
                this.isLoading = false;
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                this.isLoading = false;
                this.hasError = true;
                this.errorMessage = err.message || 'Koneksi bermasalah saat memuat antrean.';
            });
        },

        cariData() {
            this.muatData(this.buildUrl());
        },

        setStatus(newStatus) {
            this.status = newStatus;
            this.muatData(this.buildUrl());
        },

        setLayanan(newLayanan) {
            this.layanan = newLayanan;
            this.muatData(this.buildUrl());
        },

        resetSemua() {
            this.cari = '';
            this.status = 'semua';
            this.layanan = 'semua';
            this.muatData(this.baseUrl);
        },

        init() {
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#area-hasil-antrean .pagination-ajax-area a, #area-hasil-antrean nav a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    this.muatData(paginationLink.href);
                    return;
                }

                const resetBtn = e.target.closest('[data-reset-filter="true"]');
                if (resetBtn) {
                    e.preventDefault();
                    this.resetSemua();
                }
            });

            window.addEventListener('popstate', (e) => {
                const targetUrl = (e.state && e.state.url) ? e.state.url : window.location.href;
                this.muatData(targetUrl, false);
            });
        }
    }" class="py-6 space-y-6">
        <!-- 1. KPI Cards Ringkasan Antrean -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Menunggu Verifikasi -->
            <button type="button"
                    @click="setStatus('menunggu')"
                    class="bg-surface rounded-card p-4 border border-border-soft shadow-card hover:border-amber-400 transition group text-left cursor-pointer"
                    :class="status === 'menunggu' ? 'ring-2 ring-amber-500' : ''">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-muted">Menunggu</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading group-hover:text-amber-600 transition">
                    {{ $kpi['menunggu'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Perlu diverifikasi</p>
            </button>

            <!-- Kurang Berkas -->
            <button type="button"
                    @click="setStatus('kurang_berkas')"
                    class="bg-surface rounded-card p-4 border border-border-soft shadow-card hover:border-orange-400 transition group text-left cursor-pointer"
                    :class="status === 'kurang_berkas' ? 'ring-2 ring-orange-500' : ''">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-muted">Kurang Berkas</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading group-hover:text-orange-600 transition">
                    {{ $kpi['kurang_berkas'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Menunggu warga</p>
            </button>

            <!-- Sedang Diproses -->
            <button type="button"
                    @click="setStatus('diproses')"
                    class="bg-surface rounded-card p-4 border border-border-soft shadow-card hover:border-blue-400 transition group text-left cursor-pointer"
                    :class="status === 'diproses' ? 'ring-2 ring-blue-500' : ''">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-muted">Diproses</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading group-hover:text-blue-600 transition">
                    {{ $kpi['diproses'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Penyiapan surat</p>
            </button>

            <!-- Menunggu TTD -->
            <button type="button"
                    @click="setStatus('menunggu_ttd')"
                    class="bg-surface rounded-card p-4 border border-border-soft shadow-card hover:border-purple-400 transition group text-left cursor-pointer"
                    :class="status === 'menunggu_ttd' ? 'ring-2 ring-purple-500' : ''">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-ink-muted">Menunggu TTD</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-ink font-heading group-hover:text-purple-600 transition">
                    {{ $kpi['menunggu_ttd'] }}
                </div>
                <p class="text-xs text-ink-muted mt-0.5">Di Kepala Kampung</p>
            </button>
        </div>

        <!-- 2. Filter & Pencarian Bar AJAX -->
        <div class="bg-surface rounded-card p-4 border border-border-soft shadow-card space-y-4">
            <form @submit.prevent="cariData()" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Input Cari dengan Debounce 300ms -->
                <div class="md:col-span-7 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           x-model="cari"
                           @input.debounce.300ms="cariData()"
                           placeholder="Cari nomor permohonan, nama pemohon, atau NIK..."
                           class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink placeholder-ink-muted">

                    <!-- Spinner Loading Indicator / Tombol Hapus -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <template x-if="isLoading">
                            <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <template x-if="!isLoading && cari.length > 0">
                            <button type="button"
                                    @click="cari = ''; cariData()"
                                    class="text-ink-muted hover:text-ink text-xs p-1 rounded-full hover:bg-border-soft transition"
                                    title="Hapus pencarian">
                                ✕
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Dropdown Layanan -->
                <div class="md:col-span-3">
                    <select x-model="layanan"
                            @change="cariData()"
                            class="w-full py-2.5 px-3 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-medium">
                        <option value="semua">Semua Jenis Surat</option>
                        @foreach($daftarLayanan as $layanan)
                            <option value="{{ $layanan->kode }}">
                                {{ $layanan->kode }} — {{ $layanan->nama_layanan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white font-semibold text-sm transition shadow-sm text-center cursor-pointer">
                        Cari
                    </button>
                    <button type="button"
                            x-show="cari !== '' || layanan !== 'semua' || status !== 'semua'"
                            x-cloak
                            @click="resetSemua()"
                            class="py-2.5 px-3 rounded-xl border border-border-soft hover:bg-canvas text-ink-muted hover:text-ink font-semibold text-sm transition cursor-pointer"
                            title="Reset filter">
                        ↺
                    </button>
                </div>
            </form>

            <!-- Chip Filter Status (Horizontal Scrollable) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-1 scrollbar-thin">
                @php
                    $chips = [
                        'semua' => 'Semua Antrean',
                        'menunggu' => 'Menunggu Verifikasi',
                        'kurang_berkas' => 'Kurang Berkas',
                        'diproses' => 'Sedang Diproses',
                        'menunggu_ttd' => 'Menunggu TTD',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ];
                @endphp

                @foreach($chips as $key => $label)
                    <button type="button"
                            @click="setStatus('{{ $key }}')"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition cursor-pointer"
                            :class="status === '{{ $key }}' ? 'bg-primary text-white shadow-sm' : 'bg-canvas text-ink-muted hover:text-ink border border-border-soft hover:bg-surface'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Banner Error Retry Bila AJAX Gagal -->
        <div x-show="hasError"
             x-cloak
             class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span x-text="errorMessage">Terjadi kendala saat memuat data antrean.</span>
            </div>
            <button type="button"
                    @click="cariData()"
                    class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold hover:bg-rose-700 transition">
                Coba Lagi
            </button>
        </div>

        <!-- 3. Daftar Antrean (Kontainer Target AJAX) -->
        <div class="bg-surface rounded-card border border-border-soft shadow-card overflow-hidden relative">
            <!-- Loading Indicator Overlay -->
            <div x-show="isLoading"
                 x-cloak
                 class="absolute inset-0 bg-surface/60 backdrop-blur-[1px] z-10 flex items-center justify-center transition">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ink/80 text-white text-xs font-semibold shadow-lg">
                    <svg class="w-4 h-4 animate-spin text-accent" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memperbarui antrean...</span>
                </div>
            </div>

            <div id="area-hasil-antrean" aria-live="polite">
                @include('kasi.pengajuan.partials.daftar-antrean')
            </div>
        </div>
    </div>
</x-app-layout>
