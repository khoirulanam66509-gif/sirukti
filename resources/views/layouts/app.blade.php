@props(['hideBottomNav' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2F5D3A">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
        <link rel="manifest" href="/manifest.webmanifest">

        <title>{{ $title ?? config('app.name', 'SIRUKTI') }}</title>

        <!-- Scripts & Styles (Plus Jakarta Sans self-hosted via Vite) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-canvas text-ink min-h-screen selection:bg-primary-light selection:text-primary">
        <!-- Floating Left Sidebar (Desktop >= lg) -->
        @include('layouts.sidebar-desktop')

        <!-- Main Wrapper -->
        <div class="lg:pl-64 flex flex-col min-h-screen">
            <!-- App Topbar -->
            <header class="bg-surface/80 backdrop-blur-md border-b border-border-soft sticky top-0 z-30 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Left Topbar: Mobile Logo & Single-line Page Title -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="lg:hidden flex items-center shrink-0">
                        <x-application-logo size="sm" :showSub="false" />
                    </div>
                    <div class="min-w-0">
                        <span class="text-base sm:text-lg font-bold text-ink truncate block">
                            {{ $pageTitle ?? ($title ? str_replace(' — SIRUKTI', '', str_replace(' - SIRUKTI', '', $title)) : 'SIRUKTI') }}
                        </span>
                    </div>
                </div>

                <!-- Right Topbar: Date & User Profile -->
                <div class="flex items-center gap-4 shrink-0">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-ink-muted">{{ tgl_indo(now()) }}</span>
                        <span class="text-[11px] text-primary font-bold">Rukti Harjo, Seputih Raman</span>
                    </div>

                    <div x-data="{ openProfileDropdown: false }" class="relative pl-3 border-l border-border-soft">
                        <button
                            type="button"
                            @click="openProfileDropdown = !openProfileDropdown"
                            @keydown.escape.window="openProfileDropdown = false"
                            class="flex items-center gap-2.5 min-h-[44px] px-1.5 rounded-xl hover:bg-canvas transition focus:outline-none"
                            aria-label="Menu Profil Pengguna"
                            :aria-expanded="openProfileDropdown.toString()"
                        >
                            <div class="w-9 h-9 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold text-sm border border-primary/20 shrink-0">
                                {{ strtoupper(substr(auth()->user()?->nama ?? 'U', 0, 1)) }}
                            </div>
                            <div class="hidden md:flex flex-col text-left">
                                <span class="text-sm font-bold text-ink leading-none">{{ auth()->user()?->nama ?? 'Pengguna' }}</span>
                                <span class="text-[11px] text-ink-muted capitalize leading-tight mt-0.5">{{ auth()->user()?->role ?? 'Warga' }}</span>
                            </div>
                            <svg class="w-4 h-4 text-ink-muted transition-transform duration-200" :class="openProfileDropdown ? 'rotate-180 text-primary' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu Profil Topbar -->
                        <div
                            x-show="openProfileDropdown"
                            @click.outside="openProfileDropdown = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                            style="display: none;"
                            class="absolute right-0 mt-2 w-60 rounded-2xl bg-surface border border-border-soft shadow-modal py-2 z-50"
                        >
                            <div class="px-4 py-2.5 border-b border-border-soft">
                                <p class="text-xs font-bold text-ink truncate">{{ auth()->user()?->nama ?? 'Pengguna' }}</p>
                                <p class="text-[11px] text-ink-muted font-mono truncate mt-0.5">{{ auth()->user()?->username ?? '' }}</p>
                            </div>

                            <div class="p-1.5 space-y-0.5 text-xs font-semibold text-ink">
                                @if((auth()->user()?->role ?? 'warga') === 'warga')
                                    <a href="/profil" @click="openProfileDropdown = false" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[40px] rounded-xl hover:bg-canvas transition">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Pengaturan Profil</span>
                                    </a>
                                @elseif(auth()->user()?->role === 'kepala')
                                    <a href="/kepala/profil-ttd" @click="openProfileDropdown = false" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[40px] rounded-xl hover:bg-canvas transition">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span>Profil & Tanda Tangan</span>
                                    </a>
                                @elseif(auth()->user()?->role === 'kasi')
                                    <a href="/kasi/pengaturan" @click="openProfileDropdown = false" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[40px] rounded-xl hover:bg-canvas transition">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Pengaturan Kampung</span>
                                    </a>
                                @endif

                                <a href="/ubah-password" @click="openProfileDropdown = false" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[40px] rounded-xl hover:bg-canvas transition">
                                    <svg class="w-4 h-4 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Ubah Password</span>
                                </a>

                                <div class="my-1 border-t border-border-soft"></div>

                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 min-h-[40px] rounded-xl text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Keluar dari Akun</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Header Container (Rendered below topbar inside flow, grows as needed) -->
            @isset($header)
                <div class="border-b border-border-soft/80 bg-canvas/60">
                    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Main Content Area (Unified padding bottom for mobile & desktop) -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 {{ $hideBottomNav ? 'pb-28 lg:pb-24' : 'pb-24 lg:pb-8' }} max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- Footer Ringkas (Dark Forest Emerald - Desktop Only) -->
            <footer class="mt-auto bg-[#0D2616] text-white border-t border-emerald-900/40 py-4 px-4 sm:px-6 lg:px-8 hidden lg:block">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-emerald-200/70">
                    <p class="font-medium">© {{ date('Y') }} Pemerintah Kampung Rukti Harjo — SIRUKTI</p>
                    <p class="text-[11px] text-emerald-300/60">Kec. Seputih Raman, Kab. Lampung Tengah</p>
                </div>
            </footer>
        </div>

        @if(!$hideBottomNav)
            <!-- Bottom Navigation Bar (Mobile < lg) -->
            @include('layouts.bottom-nav')
        @endif

        <!-- Toast Notifications -->
        <x-toast />

        <!-- Registrasi Service Worker PWA (O8) -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
