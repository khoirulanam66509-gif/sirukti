<x-app-layout>
    <x-slot name="pageTitle">Riwayat Pengajuan</x-slot>
    <x-slot name="title">Riwayat Pengajuan Surat — SIRUKTI</x-slot>

    <div class="space-y-6">
        <!-- Top Header & Action Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-primary text-xs uppercase tracking-wider font-bold">
                    <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                    </svg>
                    <span>Layanan Administrasi Kependudukan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Riwayat Pengajuan Surat</h1>
                <p class="text-sm sm:text-base text-ink-muted max-w-2xl leading-relaxed">
                    Pantau seluruh rekam jejak permohonan surat administrasi kependudukan Anda di Balai Kampung Rukti Harjo.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('pengajuan.buat') }}"
                    class="inline-flex items-center gap-2 px-5 h-12 rounded-btn bg-primary text-white font-bold text-sm shadow-featured hover:bg-primary-hover active:scale-95 transition"
                >
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Ajukan Surat Baru</span>
                </a>
            </div>
        </div>

        <!-- Mini KPI Summary Strip -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Stat 1: Total -->
            <div class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs text-ink-muted uppercase tracking-wider font-semibold">Total Pengajuan</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-ink mt-1">
                        {{ $counts['total'] }} <span class="text-xs font-normal text-ink-muted">Surat</span>
                    </span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-canvas border border-border-soft flex items-center justify-center text-primary shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
            </div>

            <!-- Stat 2: Selesai -->
            <div class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs text-ink-muted uppercase tracking-wider font-semibold">Selesai & Terbit</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-primary mt-1">
                        {{ $counts['selesai'] }} <span class="text-xs font-normal text-ink-muted">Surat</span>
                    </span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>

            <!-- Stat 3: Sedang Berjalan -->
            <div class="bg-surface rounded-card p-4 sm:p-5 border border-border-soft shadow-card flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs text-ink-muted uppercase tracking-wider font-semibold">Sedang Diproses</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-blue-700 mt-1">
                        {{ $counts['berjalan'] }} <span class="text-xs font-normal text-ink-muted">Surat</span>
                    </span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-700 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>

            <!-- Stat 4: Perlu Tindakan -->
            <div class="bg-gradient-to-br from-[#2F5D3A] to-[#1E3B25] rounded-card p-4 sm:p-5 shadow-featured flex items-center justify-between text-white">
                <div class="flex flex-col">
                    <span class="text-xs text-white/80 uppercase tracking-wider font-semibold">Perlu Tindakan Warga</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-accent mt-1">
                        {{ $counts['perlu_tindakan'] }} <span class="text-xs font-normal text-white/80">Surat</span>
                    </span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/15 flex items-center justify-center text-accent shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter Chips & Search Bar -->
        <!-- Search & Filter Toolbar with Alpine AJAX (R1) -->
        <div
            x-data="{
                search: @js($searchQuery ?? ''),
                status: @js($statusFilter ?? ''),
                baseUrl: @js(route('riwayat')),
                loading: false,
                errorMessage: null,
                abortController: null,

                init() {
                    window.addEventListener('popstate', () => {
                        const url = new URL(window.location.href);
                        this.search = url.searchParams.get('q') || '';
                        this.status = url.searchParams.get('status') || '';
                        const page = url.searchParams.get('page') || 1;
                        this.fetchData(page, false);
                    });
                },

                onSearchInput() {
                    this.fetchData(1, true);
                },

                setStatus(newStatus) {
                    this.status = newStatus;
                    this.fetchData(1, true);
                },

                resetAll() {
                    this.search = '';
                    this.status = '';
                    this.fetchData(1, true);
                },

                handleContainerClick(e) {
                    const resetBtn = e.target.closest('[data-reset-filter]');
                    if (resetBtn) {
                        e.preventDefault();
                        this.resetAll();
                        return;
                    }

                    const paginationLink = e.target.closest('nav[role=\'navigation\'] a');
                    if (paginationLink && paginationLink.href) {
                        e.preventDefault();
                        try {
                            const url = new URL(paginationLink.href);
                            const page = url.searchParams.get('page') || 1;
                            this.fetchData(page, true);
                        } catch (err) {
                            window.location.href = paginationLink.href;
                        }
                    }
                },

                fetchData(page = null, updateHistory = true) {
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                    this.abortController = new AbortController();
                    this.loading = true;
                    this.errorMessage = null;

                    const url = new URL(this.baseUrl, window.location.origin);
                    if (this.search && this.search.trim() !== '') {
                        url.searchParams.set('q', this.search.trim());
                    }
                    if (this.status && this.status.trim() !== '') {
                        url.searchParams.set('status', this.status.trim());
                    }
                    if (page && page > 1) {
                        url.searchParams.set('page', page);
                    }

                    if (updateHistory) {
                        window.history.replaceState({}, '', url.toString());
                    }

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html, application/xhtml+xml',
                        },
                        signal: this.abortController.signal,
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.text();
                    })
                    .then(html => {
                        const target = document.getElementById('area-hasil-riwayat');
                        if (target) {
                            target.innerHTML = html;
                        }
                        this.loading = false;
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') {
                            return;
                        }
                        this.loading = false;
                        this.errorMessage = 'Terjadi kendala saat memuat data. Silakan periksa koneksi internet Anda atau coba lagi.';
                    });
                }
            }"
            class="space-y-4"
        >
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Filter Chips Scrollable (Mobile Friendly) -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1.5 md:pb-0 scrollbar-none text-xs">
                    @php
                        $chips = [
                            '' => ['label' => 'Semua', 'count' => $counts['total']],
                            'menunggu' => ['label' => 'Menunggu', 'count' => $counts['menunggu']],
                            'kurang_berkas' => ['label' => 'Kurang Berkas', 'count' => $counts['perlu_tindakan']],
                            'diproses' => ['label' => 'Diproses', 'count' => $counts['diproses']],
                            'menunggu_ttd' => ['label' => 'Menunggu TTD', 'count' => $counts['menunggu_ttd']],
                            'selesai' => ['label' => 'Selesai', 'count' => $counts['selesai']],
                            'ditolak' => ['label' => 'Ditolak', 'count' => $counts['ditolak']],
                        ];
                    @endphp

                    @foreach($chips as $key => $chip)
                        @php
                            $url = route('riwayat', array_merge(request()->except(['page']), $key !== '' ? ['status' => $key] : []));
                        @endphp
                        <a
                            href="{{ $url }}"
                            @click.prevent="setStatus('{{ (string) $key }}')"
                            :class="(status === '{{ (string) $key }}' || (!status && '{{ (string) $key }}' === '')) ? 'bg-primary text-white shadow-sm' : 'bg-surface border border-border-soft text-ink-muted hover:text-ink hover:bg-canvas'"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full whitespace-nowrap transition font-semibold"
                        >
                            <span>{{ $chip['label'] }}</span>
                            <span
                                :class="(status === '{{ (string) $key }}' || (!status && '{{ (string) $key }}' === '')) ? 'bg-primary-hover text-white' : 'bg-canvas text-ink-muted'"
                                class="px-1.5 py-0.2 rounded-full text-[11px] font-bold"
                            >
                                {{ $chip['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <!-- Search Form (Progressive Enhancement) -->
                <form method="GET" action="{{ route('riwayat') }}" @submit.prevent="fetchData(1)" class="relative w-full md:w-80 shrink-0">
                    <input type="hidden" name="status" :value="status" />
                    <input
                        type="text"
                        name="q"
                        x-model="search"
                        @input.debounce.300ms="onSearchInput"
                        placeholder="Cari nomor pengajuan / surat..."
                        class="w-full h-11 pl-10 pr-10 bg-surface text-ink text-sm rounded-btn border border-border-soft focus:border-primary focus:ring-4 focus:ring-primary/15 transition placeholder:text-ink-muted/60"
                    />
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Spinner Indikator Loading -->
                    <div x-show="loading" x-cloak class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-primary">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>

                    <!-- Tombol Reset Input Pencarian -->
                    <button
                        type="button"
                        x-show="search && !loading"
                        x-cloak
                        @click="search = ''; onSearchInput();"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-muted hover:text-ink transition"
                        title="Hapus kata kunci pencarian"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Pesan Error Jaringan / Server -->
            <div x-show="errorMessage" x-cloak class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between gap-3 text-xs shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span x-text="errorMessage"></span>
                </div>
                <button type="button" @click="fetchData()" class="px-3 py-1 rounded-btn bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shrink-0">
                    Coba Lagi
                </button>
            </div>

            <!-- Area Hasil Riwayat (Daftar Kartu & Tabel Responsif) -->
            <div class="relative">
                <!-- Subtle Loading Overlay saat Fetching -->
                <div
                    x-show="loading"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-surface/50 backdrop-blur-[0.5px] rounded-card flex items-center justify-center z-10"
                >
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-surface border border-border-soft shadow-modal text-xs font-bold text-ink">
                        <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memuat hasil pencarian...</span>
                    </div>
                </div>

                <div
                    id="area-hasil-riwayat"
                    aria-live="polite"
                    @click="handleContainerClick($event)"
                >
                    @include('pengajuan.partials.daftar-riwayat')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
