<x-app-layout>
    <x-slot name="pageTitle">Kelola Warga</x-slot>
    <x-slot name="title">Kelola Warga — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-wide uppercase">
                        Modul Administrasi
                    </span>
                    <span class="text-ink-muted text-xs">•</span>
                    <span class="text-xs text-ink-muted flex items-center gap-1 font-medium">
                        <svg class="w-3.5 h-3.5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Kewenangan Kasi Pelayanan
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                    Kelola Data Warga
                </h1>
                <p class="text-sm text-ink-muted max-w-3xl mt-1 leading-relaxed">
                    Daftar penduduk Kampung Rukti Harjo terdaftar pada portal mandiri pelayanan surat digital.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        q: '{{ addslashes($q) }}',
        dusun: '{{ $dusunSelected }}',
        status: '{{ $statusSelected }}',
        isLoading: false,
        hasError: false,
        errorMessage: '',
        abortController: null,
        baseUrl: '{{ route('kasi.warga.index') }}',
        resetModalOpen: false,
        selectedWargaId: null,
        selectedWargaName: '',
        selectedWargaNik: '',
        selectedWargaDusun: '',

        openResetModal(id, name, nik, dusun) {
            this.selectedWargaId = id;
            this.selectedWargaName = name;
            this.selectedWargaNik = nik;
            this.selectedWargaDusun = dusun;
            this.resetModalOpen = true;
        },

        buildUrl(page = null) {
            const params = new URLSearchParams();
            if (this.q.trim() !== '') params.set('q', this.q.trim());
            if (this.dusun !== '') params.set('dusun', this.dusun);
            if (this.status !== 'semua' && this.status !== '') params.set('status', this.status);
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
                if (!response.ok) throw new Error('Gagal memuat data warga');
                return response.text();
            })
            .then(html => {
                const container = document.getElementById('area-hasil-warga');
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
                this.errorMessage = err.message || 'Koneksi bermasalah saat memuat data warga.';
            });
        },

        cariData() {
            this.muatData(this.buildUrl());
        },

        setDusun(newDusun) {
            this.dusun = newDusun;
            this.muatData(this.buildUrl());
        },

        setStatus(newStatus) {
            this.status = newStatus;
            this.muatData(this.buildUrl());
        },

        resetSemua() {
            this.q = '';
            this.dusun = '';
            this.status = 'semua';
            this.muatData(this.baseUrl);
        },

        init() {
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#area-hasil-warga .pagination-ajax-area a, #area-hasil-warga nav a');
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

        <!-- Mini KPI Stat Cards (Bento Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card 1: Total Warga (Hero Featured) -->
            <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-primary to-[#3C7A4C] text-white p-6 shadow-featured flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-white backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-md">
                        Basis Data Desa
                    </span>
                </div>
                <div class="mt-6">
                    <div class="text-3xl sm:text-4xl font-extrabold tracking-tight">{{ number_format($totalWarga, 0, ',', '.') }}</div>
                    <div class="text-xs text-white/80 font-medium mt-1">Total Warga Terdaftar (Akun Mandiri)</div>
                </div>
            </div>

            <!-- Card 2: Akun Aktif -->
            <div class="rounded-card bg-surface p-6 shadow-card border border-border-soft flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-[#DCFCE7] flex items-center justify-center text-[#15803D]">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-canvas text-primary text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span>Aktif</span>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-3xl sm:text-4xl font-extrabold text-ink tracking-tight">{{ number_format($totalAktif, 0, ',', '.') }}</div>
                    <div class="text-xs text-ink-muted font-medium mt-1">Akun Warga Aktif &amp; Terverifikasi</div>
                </div>
            </div>

            <!-- Card 3: Butuh Reset Password -->
            <div class="rounded-card bg-surface p-6 shadow-card border border-border-soft flex flex-col justify-between">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                        Wajib Ganti Sandi
                    </span>
                </div>
                <div class="mt-6">
                    <div class="text-3xl sm:text-4xl font-extrabold text-ink tracking-tight">{{ number_format($totalReset, 0, ',', '.') }}</div>
                    <div class="text-xs text-ink-muted font-medium mt-1">Warga Dalam Status Reset Sandi</div>
                </div>
            </div>
        </div>

        <!-- Main Card: Table & Filter -->
        <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-6 space-y-6">

            <!-- Toolbar Filter & Search AJAX -->
            <form @submit.prevent="cariData()" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                <!-- Search Input dengan Debounce 300ms -->
                <div class="relative flex-1 max-w-lg">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        x-model="q"
                        @input.debounce.300ms="cariData()"
                        placeholder="Cari NIK 16 digit, nama warga, atau username..."
                        class="w-full h-12 pl-11 pr-10 bg-canvas text-ink rounded-full text-sm placeholder:text-ink-muted border-border-soft focus:border-primary focus:ring-primary transition"
                    >
                    <!-- Spinner / Clear Button -->
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                        <template x-if="isLoading">
                            <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <template x-if="!isLoading && q.length > 0">
                            <button type="button"
                                    @click="q = ''; cariData()"
                                    class="text-ink-muted hover:text-ink text-xs p-1 rounded-full hover:bg-border-soft transition cursor-pointer"
                                    title="Hapus pencarian">
                                ✕
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Dusun Dropdown Filter -->
                    <select
                        x-model="dusun"
                        @change="cariData()"
                        class="h-12 pl-4 pr-10 rounded-full bg-canvas text-ink text-sm font-medium border-border-soft focus:border-primary focus:ring-primary cursor-pointer transition"
                    >
                        <option value="">Semua Wilayah Dusun</option>
                        @foreach ($daftarDusun as $dusunItem)
                            <option value="{{ $dusunItem->id_dusun }}">
                                {{ $dusunItem->nama_dusun }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status Chips Filter -->
                    <div class="inline-flex p-1 bg-canvas rounded-full border border-border-soft text-xs font-semibold">
                        <button type="button"
                                @click="setStatus('semua')"
                                class="px-3 py-1.5 rounded-full transition cursor-pointer"
                                :class="status === 'semua' || status === '' ? 'bg-surface text-primary shadow-sm font-bold' : 'text-ink-muted hover:text-ink'">
                            Semua
                        </button>
                        <button type="button"
                                @click="setStatus('aktif')"
                                class="px-3 py-1.5 rounded-full transition cursor-pointer"
                                :class="status === 'aktif' ? 'bg-surface text-primary shadow-sm font-bold' : 'text-ink-muted hover:text-ink'">
                            Aktif
                        </button>
                        <button type="button"
                                @click="setStatus('nonaktif')"
                                class="px-3 py-1.5 rounded-full transition cursor-pointer"
                                :class="status === 'nonaktif' ? 'bg-surface text-primary shadow-sm font-bold' : 'text-ink-muted hover:text-ink'">
                            Nonaktif
                        </button>
                    </div>

                    <button type="submit" class="h-12 px-5 rounded-full bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition cursor-pointer">
                        Cari
                    </button>
                    <button type="button"
                            x-show="q !== '' || dusun !== '' || (status !== 'semua' && status !== '')"
                            x-cloak
                            @click="resetSemua()"
                            class="h-12 px-4 inline-flex items-center justify-center rounded-full bg-canvas text-ink-muted hover:text-ink text-sm font-semibold transition border border-border-soft cursor-pointer">
                        Reset
                    </button>
                </div>
            </form>

            <!-- Banner Error Retry Bila AJAX Gagal -->
            <div x-show="hasError"
                 x-cloak
                 class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span x-text="errorMessage">Terjadi kendala saat memuat data warga.</span>
                </div>
                <button type="button"
                        @click="cariData()"
                        class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold hover:bg-rose-700 transition">
                    Coba Lagi
                </button>
            </div>

            <!-- Kontainer Hasil Data Warga AJAX -->
            <div class="relative">
                <!-- Loading Indicator Overlay -->
                <div x-show="isLoading"
                     x-cloak
                     class="absolute inset-0 bg-surface/60 backdrop-blur-[1px] z-10 flex items-center justify-center transition min-h-[160px]">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ink/80 text-white text-xs font-semibold shadow-lg">
                        <svg class="w-4 h-4 animate-spin text-accent" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memperbarui data warga...</span>
                    </div>
                </div>

                <div id="area-hasil-warga" aria-live="polite">
                    @include('kasi.warga.partials.daftar-warga')
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- DIALOG MODAL / BOTTOM SHEET: RESET PASSWORD (K5 DIALOG) -->
        <!-- ======================================================== -->
        <div
            x-show="resetModalOpen"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <!-- Backdrop -->
            <div
                x-show="resetModalOpen"
                @click="resetModalOpen = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-[#1E2A22]/50 backdrop-blur-xs"
            ></div>

            <!-- Modal Content (Bottom Sheet on 360px, Centered Card on md+) -->
            <div
                x-show="resetModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                class="relative bg-surface rounded-t-[28px] sm:rounded-card shadow-modal border border-border-soft w-full max-w-lg p-6 overflow-hidden z-10"
            >
                <form :action="'/kasi/warga/' + selectedWargaId + '/reset-password'" method="POST" class="space-y-5">
                    @csrf

                    <!-- Modal Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-800 shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-ink leading-snug">Reset Kata Sandi Akun</h3>
                                <p class="text-xs text-ink-muted">Layanan Mandiri Warga SIRUKTI</p>
                            </div>
                        </div>
                        <button type="button" @click="resetModalOpen = false" class="w-8 h-8 rounded-full bg-canvas text-ink-muted hover:text-ink flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Metadata Pill -->
                    <div class="p-3.5 rounded-xl bg-canvas border border-border-soft flex items-center justify-between text-xs">
                        <div class="truncate">
                            <span class="font-bold text-ink" x-text="selectedWargaName"></span>
                            <span class="mx-1 text-ink-muted">•</span>
                            <span class="font-mono text-ink-muted" x-text="'NIK: ' + selectedWargaNik"></span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-bold shrink-0 text-[11px]" x-text="selectedWargaDusun"></span>
                    </div>

                    <!-- Advisory Alert -->
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="leading-relaxed">
                            Password sementara akan diatur. Warga <strong>diwajibkan mengganti kata sandi</strong> secara mandiri sesaat setelah berhasil masuk ke sistem.
                        </p>
                    </div>

                    <!-- Input Custom Temp Password -->
                    <div class="space-y-1.5">
                        <label for="password_baru" class="block text-xs font-semibold text-ink">
                            Kata Sandi Baru Sementara (Opsional)
                        </label>
                        <input
                            type="text"
                            name="password_baru"
                            id="password_baru"
                            placeholder="Biarkan kosong untuk otomatis 'sirukti123'"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                        >
                        <p class="text-[11px] text-ink-muted">
                            Minimal 8 karakter jika diisi manual.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-border-soft">
                        <button
                            type="button"
                            @click="resetModalOpen = false"
                            class="min-h-[44px] px-5 rounded-btn bg-canvas text-ink font-semibold text-sm hover:bg-border-soft transition border border-border-soft"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="min-h-[44px] px-6 rounded-btn bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-sm transition inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan &amp; Reset Sandi</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</x-app-layout>
