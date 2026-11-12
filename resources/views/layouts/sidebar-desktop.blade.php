@php
    $role = auth()->user()?->role ?? 'warga';
@endphp

<aside class="hidden lg:flex lg:flex-col lg:w-64 fixed inset-y-0 left-0 p-4 z-40">
    <div class="flex-1 bg-surface rounded-card border border-border-soft shadow-card p-5 flex flex-col justify-between overflow-y-auto">
        <!-- Brand / Header -->
        <div class="space-y-6">
            <a href="/" class="block focus:outline-none focus:ring-2 focus:ring-primary rounded-xl">
                <x-application-logo />
            </a>

            <!-- Role Badge -->
            <div class="px-3 py-2 rounded-xl bg-canvas border border-border-soft flex items-center justify-between gap-2">
                <span class="text-xs font-medium text-ink-muted shrink-0">Akses:</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-primary/10 text-primary text-xs font-bold whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                    {{ $role === 'kasi' ? 'Kasi Pelayanan' : ($role === 'kepala' ? 'Kepala Kampung' : 'Warga Desa') }}
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                @if($role === 'warga')
                    <a href="/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Beranda</span>
                    </a>
                    <a href="/pengajuan/buat" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('pengajuan/buat*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Ajukan Surat</span>
                    </a>
                    <a href="/riwayat" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('riwayat*') || (request()->is('pengajuan/*') && !request()->is('pengajuan/buat*')) ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Riwayat Pengajuan</span>
                    </a>
                    <a href="/profil" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('profil*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profil Saya</span>
                    </a>
                @elseif($role === 'kasi')
                    <a href="/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Beranda Kasi</span>
                    </a>
                    <a href="/kasi/antrean" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kasi/antrean*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Antrean Verifikasi</span>
                    </a>
                    <a href="/kasi/warga" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kasi/warga*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola Warga</span>
                    </a>
                    <a href="/kasi/persyaratan" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kasi/persyaratan*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Syarat Layanan</span>
                    </a>
                    <a href="/arsip-laporan" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('arsip-laporan*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        <span>Arsip & Laporan</span>
                    </a>
                    <a href="/kasi/pengaturan" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kasi/pengaturan*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan Kampung</span>
                    </a>
                @elseif($role === 'kepala')
                    <a href="/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Beranda Kepala</span>
                    </a>
                    <a href="/kepala/persetujuan" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kepala/persetujuan*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Daftar Persetujuan</span>
                    </a>
                    <a href="/arsip-laporan" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('arsip-laporan*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        <span>Arsip & Laporan</span>
                    </a>
                    <a href="/kepala/profil-ttd" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition {{ request()->is('kepala/profil-ttd*') ? 'bg-primary text-white shadow-sm' : 'text-ink-muted hover:text-ink hover:bg-canvas' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Profil & Tanda Tangan</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- User Info / Logout -->
        <div class="pt-4 border-t border-border-soft space-y-3">
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()?->nama ?? 'U', 0, 1)) }}
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-ink truncate">{{ auth()->user()?->nama ?? 'Pengguna' }}</span>
                    <span class="text-xs text-ink-muted truncate">{{ auth()->user()?->username ?? '' }}</span>
                </div>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </div>
</aside>
