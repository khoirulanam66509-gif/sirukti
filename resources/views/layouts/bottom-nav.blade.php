@php
    $role = auth()->user()?->role ?? 'warga';

    // Logika penanda rute aktif terpusat
    $nav = [
        'beranda' => request()->is('dashboard'),
        'ajukan' => request()->is('pengajuan/buat*'),
        'riwayat' => request()->is('riwayat*') || (request()->is('pengajuan/*') && !request()->is('pengajuan/buat*')),
        'profil' => request()->is('profil*') || request()->is('ubah-password*'),
        'antrean' => request()->is('kasi/antrean*') || (request()->is('antrean*')),
        'warga' => request()->is('kasi/warga*'),
        'arsip' => request()->is('arsip-laporan*'),
        'persetujuan' => request()->is('kepala/persetujuan*') || request()->is('persetujuan*'),
        // Kategori sheet lainnya untuk Kasi / Kepala
        'lainnya_kasi' => request()->is('kasi/persyaratan*') || request()->is('kasi/pengaturan*') || request()->is('ubah-password*'),
        'lainnya_kepala' => request()->is('kepala/profil-ttd*') || request()->is('ubah-password*'),
    ];
@endphp

<div x-data="{ openMenu: false }" class="lg:hidden">
    <!-- Bottom Navigation Bar -->
    <nav class="fixed bottom-0 inset-x-0 bg-surface/95 backdrop-blur-md border-t border-border-soft z-40 px-2 py-1.5 shadow-lg safe-bottom">
        <div class="flex items-center justify-around max-w-lg mx-auto">
            @if($role === 'warga')
                <!-- 1. Beranda -->
                <a href="/dashboard"
                   @if($nav['beranda']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['beranda'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['beranda'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Beranda</span>
                </a>

                <!-- 2. Ajukan Surat (Tombol Menonjol di Tengah) -->
                <a href="/pengajuan/buat"
                   @if($nav['ajukan']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center -mt-5 min-w-[56px] focus:outline-none">
                    <div class="w-14 h-14 rounded-full p-3 border-4 border-surface flex items-center justify-center hover:scale-105 active:scale-95 transition {{ $nav['ajukan'] ? 'bg-primary text-white shadow-featured ring-4 ring-primary/20' : 'bg-primary-light text-primary border-surface shadow-md' }}">
                        <svg class="w-6 h-6 {{ $nav['ajukan'] ? 'text-accent' : 'text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold mt-1 {{ $nav['ajukan'] ? 'text-primary' : 'text-ink-muted' }}">Ajukan</span>
                </a>

                <!-- 3. Riwayat -->
                <a href="/riwayat"
                   @if($nav['riwayat']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['riwayat'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['riwayat'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Riwayat</span>
                </a>

                <!-- 4. Profil Saya (Warga membuka Bottom Sheet Menu Pengaturan Profil & Keluar) -->
                <button type="button" @click="openMenu = true"
                        @if($nav['profil']) aria-current="page" @endif
                        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['profil'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['profil'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Profil</span>
                </button>

            @elseif($role === 'kasi')
                <!-- 1. Beranda -->
                <a href="/dashboard"
                   @if($nav['beranda']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[48px] min-h-[48px] py-1 text-[11px] transition {{ $nav['beranda'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-2.5 py-1 rounded-full transition {{ $nav['beranda'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <span class="mt-0.5">Beranda</span>
                </a>

                <!-- 2. Antrean -->
                <a href="/kasi/antrean"
                   @if($nav['antrean']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[48px] min-h-[48px] py-1 text-[11px] transition {{ $nav['antrean'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-2.5 py-1 rounded-full transition {{ $nav['antrean'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span class="mt-0.5">Antrean</span>
                </a>

                <!-- 3. Warga -->
                <a href="/kasi/warga"
                   @if($nav['warga']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[48px] min-h-[48px] py-1 text-[11px] transition {{ $nav['warga'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-2.5 py-1 rounded-full transition {{ $nav['warga'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="mt-0.5">Warga</span>
                </a>

                <!-- 4. Arsip -->
                <a href="/arsip-laporan"
                   @if($nav['arsip']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[48px] min-h-[48px] py-1 text-[11px] transition {{ $nav['arsip'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-2.5 py-1 rounded-full transition {{ $nav['arsip'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                    </div>
                    <span class="mt-0.5">Arsip</span>
                </a>

                <!-- 5. Lainnya -->
                <button type="button" @click="openMenu = true"
                        @if($nav['lainnya_kasi']) aria-current="page" @endif
                        class="flex flex-col items-center justify-center min-w-[48px] min-h-[48px] py-1 text-[11px] transition {{ $nav['lainnya_kasi'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-2.5 py-1 rounded-full transition {{ $nav['lainnya_kasi'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <span class="mt-0.5">Lainnya</span>
                </button>

            @elseif($role === 'kepala')
                <!-- 1. Beranda -->
                <a href="/dashboard"
                   @if($nav['beranda']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['beranda'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['beranda'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Beranda</span>
                </a>

                <!-- 2. Persetujuan -->
                <a href="/kepala/persetujuan"
                   @if($nav['persetujuan']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['persetujuan'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['persetujuan'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Persetujuan</span>
                </a>

                <!-- 3. Arsip -->
                <a href="/arsip-laporan"
                   @if($nav['arsip']) aria-current="page" @endif
                   class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['arsip'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['arsip'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Arsip</span>
                </a>

                <!-- 4. Akun -->
                <button type="button" @click="openMenu = true"
                        @if($nav['lainnya_kepala']) aria-current="page" @endif
                        class="flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-1 text-xs transition {{ $nav['lainnya_kepala'] ? 'text-primary font-bold' : 'text-ink-muted hover:text-ink font-semibold' }}">
                    <div class="px-3.5 py-1 rounded-full transition {{ $nav['lainnya_kepala'] ? 'bg-primary text-white shadow-xs' : 'text-ink-muted' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="mt-0.5 text-[11px]">Akun</span>
                </button>
            @endif
        </div>
    </nav>

    <!-- Bottom Sheet Menu (Profil / Akun / Lainnya untuk Warga, Kasi & Kepala) -->
    <div
        x-show="openMenu"
        class="fixed inset-0 z-50 flex flex-col justify-end"
        style="display: none;"
    >
        <!-- Overlay -->
        <div
            x-show="openMenu"
            @click="openMenu = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-[#1E2A22]/40 backdrop-blur-[2px]"
        ></div>

        <!-- Sheet Panel -->
        <div
            x-show="openMenu"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="relative bg-surface rounded-t-[28px] border-t border-border-soft p-6 shadow-modal max-h-[80vh] overflow-y-auto space-y-4 safe-bottom"
        >
            <div class="w-12 h-1.5 rounded-full bg-border-soft mx-auto mb-2"></div>

            <div class="flex items-center justify-between gap-3 pb-3 border-b border-border-soft">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()?->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-ink text-sm truncate">{{ auth()->user()?->nama ?? 'Pengguna' }}</h4>
                        <p class="text-xs text-ink-muted font-mono truncate">{{ auth()->user()?->username ?? '' }}</p>
                    </div>
                </div>
                <button type="button" @click="openMenu = false" class="w-8 h-8 rounded-full bg-canvas text-ink-muted hover:text-ink flex items-center justify-center transition shrink-0" aria-label="Tutup Menu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-1 text-sm font-semibold text-ink">
                @if($role === 'warga')
                    <a href="/profil" @click="openMenu = false" class="flex items-center gap-3 p-3 min-h-[44px] rounded-xl transition {{ request()->is('profil*') ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-canvas' }}">
                        <svg class="w-5 h-5 {{ request()->is('profil*') ? 'text-primary' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengaturan Profil</span>
                    </a>
                @elseif($role === 'kasi')
                    <a href="/kasi/persyaratan" @click="openMenu = false" class="flex items-center gap-3 p-3 min-h-[44px] rounded-xl hover:bg-canvas">
                        <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Persyaratan Layanan</span>
                    </a>
                    <a href="/kasi/pengaturan" @click="openMenu = false" class="flex items-center gap-3 p-3 min-h-[44px] rounded-xl hover:bg-canvas">
                        <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan Kampung</span>
                    </a>
                @elseif($role === 'kepala')
                    <a href="/kepala/profil-ttd" @click="openMenu = false" class="flex items-center gap-3 p-3 min-h-[44px] rounded-xl hover:bg-canvas">
                        <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Profil & Tanda Tangan</span>
                    </a>
                @endif

                <a href="/ubah-password" @click="openMenu = false" class="flex items-center gap-3 p-3 min-h-[44px] rounded-xl transition {{ request()->is('ubah-password*') ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-canvas' }}">
                    <svg class="w-5 h-5 {{ request()->is('ubah-password*') ? 'text-primary' : 'text-ink-muted' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Ubah Password</span>
                </a>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 min-h-[44px] rounded-xl text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
