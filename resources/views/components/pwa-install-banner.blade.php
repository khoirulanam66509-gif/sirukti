<!-- Global Early Listener untuk menangkap event beforeinstallprompt sebelum Alpine diinisialisasi -->
<script>
    if (typeof window.__pwaDeferredPrompt === 'undefined') {
        window.__pwaDeferredPrompt = null;
        window.__pwaIsStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.__pwaDeferredPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-prompt-ready'));
        });

        window.addEventListener('appinstalled', () => {
            window.__pwaDeferredPrompt = null;
            window.__pwaIsStandalone = true;
            window.dispatchEvent(new CustomEvent('pwa-installed'));
        });
    }
</script>

<!-- Modal / Bottom Sheet Panduan & Pemicu Instalasi Shortcut PWA (O8) -->
<div
    x-data="{
        guideOpen: false,
        canPrompt: Boolean(window.__pwaDeferredPrompt),
        isStandalone: Boolean(window.__pwaIsStandalone),
        activePlatform: /iPad|iPhone|iPod/.test(navigator.userAgent) ? 'ios' : (/Android/.test(navigator.userAgent) ? 'android' : 'desktop'),
        async handleInstallRequest() {
            if (window.__pwaDeferredPrompt) {
                try {
                    window.__pwaDeferredPrompt.prompt();
                    const { outcome } = await window.__pwaDeferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        window.__pwaDeferredPrompt = null;
                        this.canPrompt = false;
                        this.guideOpen = false;
                    }
                } catch (err) {
                    this.guideOpen = true;
                }
            } else {
                this.guideOpen = true;
            }
        }
    }"
    @pwa-prompt-ready.window="canPrompt = true"
    @pwa-installed.window="isStandalone = true; canPrompt = false; guideOpen = false"
    @pwa-install-request.window="handleInstallRequest()"
    @keydown.escape.window="guideOpen = false"
>
    <!-- Modal Dialog / Mobile Bottom Sheet -->
    <div
        x-show="guideOpen"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pwa-modal-title"
    >
        <!-- Backdrop -->
        <div
            x-show="guideOpen"
            @click="guideOpen = false"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-[#1E2A22]/60 backdrop-blur-xs"
        ></div>

        <!-- Panel Konten -->
        <div
            x-show="guideOpen"
            x-transition:enter="ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
            class="relative w-full sm:max-w-lg bg-surface rounded-t-[28px] sm:rounded-card border border-border-soft shadow-modal p-5 sm:p-6 space-y-4 z-10 max-h-[88vh] overflow-y-auto safe-bottom"
        >
            <!-- Handle bar khusus mobile -->
            <div class="w-12 h-1.5 rounded-full bg-border-soft mx-auto sm:hidden"></div>

            <!-- Header Modal -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-soft text-primary flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="pwa-modal-title" class="text-base font-bold text-ink leading-snug">
                            Instal Aplikasi SIRUKTI
                        </h3>
                        <p class="text-xs text-ink-muted">
                            Tambahkan ke layar utama untuk akses lebih cepat.
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="guideOpen = false"
                    class="min-w-[40px] min-h-[40px] rounded-xl bg-canvas text-ink-muted hover:text-ink flex items-center justify-center transition shrink-0"
                    aria-label="Tutup panduan instalasi"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tombol Install Otomatis 1-Klik bila didukung browser -->
            <template x-if="canPrompt">
                <div class="p-4 rounded-2xl bg-primary-soft/50 border border-primary/20 space-y-3">
                    <p class="text-xs text-ink font-medium">
                        Perangkat Anda mendukung pemasangan otomatis 1-klik:
                    </p>
                    <button
                        type="button"
                        @click="handleInstallRequest()"
                        class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Pasang Shortcut Sekarang</span>
                    </button>
                </div>
            </template>

            <!-- Tab Pemilih Perangkat (Android / iPhone / Laptop) -->
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-1.5 p-1 bg-canvas rounded-xl border border-border-soft text-xs font-bold">
                    <button
                        type="button"
                        @click="activePlatform = 'android'"
                        :class="activePlatform === 'android' ? 'bg-surface text-primary shadow-xs border border-border-soft' : 'text-ink-muted hover:text-ink'"
                        class="min-h-[40px] py-2 px-2 rounded-lg transition text-center"
                    >
                        HP Android
                    </button>
                    <button
                        type="button"
                        @click="activePlatform = 'ios'"
                        :class="activePlatform === 'ios' ? 'bg-surface text-primary shadow-xs border border-border-soft' : 'text-ink-muted hover:text-ink'"
                        class="min-h-[40px] py-2 px-2 rounded-lg transition text-center"
                    >
                        iPhone / iOS
                    </button>
                    <button
                        type="button"
                        @click="activePlatform = 'desktop'"
                        :class="activePlatform === 'desktop' ? 'bg-surface text-primary shadow-xs border border-border-soft' : 'text-ink-muted hover:text-ink'"
                        class="min-h-[40px] py-2 px-2 rounded-lg transition text-center"
                    >
                        Laptop / PC
                    </button>
                </div>

                <!-- Langkah HP Android -->
                <div x-show="activePlatform === 'android'" class="p-4 rounded-2xl bg-canvas/80 border border-border-soft space-y-2.5 text-xs sm:text-sm text-ink">
                    <p class="font-bold text-primary">Cara Pasang di Google Chrome (Android):</p>
                    <ol class="list-decimal list-inside space-y-2 text-ink-muted">
                        <li>Ketuk ikon <strong class="text-ink">Titik Tiga (⋮)</strong> di pojok kanan atas browser Chrome.</li>
                        <li>Pilih menu <strong class="text-ink">"Tambahkan ke layar utama"</strong> atau <strong class="text-ink">"Instal aplikasi"</strong>.</li>
                        <li>Ketuk <strong class="text-ink">Instal / Tambah</strong> — ikon <strong>SIRUKTI</strong> akan muncul di layar utama HP Anda.</li>
                    </ol>
                </div>

                <!-- Langkah iPhone / iOS -->
                <div x-show="activePlatform === 'ios'" style="display: none;" class="p-4 rounded-2xl bg-canvas/80 border border-border-soft space-y-2.5 text-xs sm:text-sm text-ink">
                    <p class="font-bold text-primary">Cara Pasang di Safari (iPhone / iPad):</p>
                    <ol class="list-decimal list-inside space-y-2 text-ink-muted">
                        <li>Buka SIRUKTI menggunakan browser <strong class="text-ink">Safari</strong>.</li>
                        <li>Ketuk tombol <strong class="text-ink">Bagikan (Share)</strong> (ikon kotak dengan panah ke atas) di bar bawah layar.</li>
                        <li>Geser ke bawah lalu pilih <strong class="text-ink">"Tambah ke Layar Utama" (Add to Home Screen)</strong>, lalu ketuk <strong class="text-ink">Tambah</strong>.</li>
                    </ol>
                </div>

                <!-- Langkah Laptop / PC -->
                <div x-show="activePlatform === 'desktop'" style="display: none;" class="p-4 rounded-2xl bg-canvas/80 border border-border-soft space-y-2.5 text-xs sm:text-sm text-ink">
                    <p class="font-bold text-primary">Cara Pasang di Chrome / Microsoft Edge (Laptop/PC):</p>
                    <ol class="list-decimal list-inside space-y-2 text-ink-muted">
                        <li>Lihat sisi kanan <strong class="text-ink">kolom alamat URL (Address Bar)</strong> di bagian atas browser.</li>
                        <li>Klik ikon <strong class="text-ink">Instal SIRUKTI</strong> (ikon layar monitor dengan tanda panah unduh).</li>
                        <li>Klik <strong class="text-ink">Instal</strong> — shortcut aplikasi SIRUKTI akan muncul di Desktop dan Start Menu.</li>
                    </ol>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button
                    type="button"
                    @click="guideOpen = false"
                    class="w-full sm:w-auto min-h-[44px] px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs sm:text-sm hover:bg-primary-hover transition"
                >
                    Mengerti, Tutup
                </button>
            </div>
        </div>
    </div>
</div>
