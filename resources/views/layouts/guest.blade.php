<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col justify-between selection:bg-primary-light selection:text-primary">
        <!-- Minimal Top Nav -->
        <header class="w-full bg-surface/80 backdrop-blur-md border-b border-border-soft sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="focus:outline-none focus:ring-2 focus:ring-primary rounded-xl">
                    <x-application-logo />
                </a>
                <div class="flex items-center gap-3">
                    <a href="/login" class="text-sm font-semibold text-ink hover:text-primary transition py-2 px-3">
                        Masuk
                    </a>
                    <a href="/register" class="text-sm font-bold bg-primary text-white hover:bg-primary-hover py-2 px-4 rounded-btn shadow-sm transition">
                        Daftar
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Auth / Guest Content -->
        <main class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:py-12">
            <div class="w-full sm:max-w-md">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer (Dark Forest Emerald) -->
        <footer class="w-full bg-[#0D2616] text-white border-t border-emerald-900/50 py-6 text-center text-xs">
            <div class="max-w-7xl mx-auto px-4 space-y-1">
                <p class="font-bold text-white tracking-wide">SIRUKTI <span class="text-accent">—</span> Sistem Layanan Surat Digital</p>
                <p class="text-emerald-200/70">Pemerintah Kampung Rukti Harjo, Kecamatan Seputih Raman, Kabupaten Lampung Tengah</p>
            </div>
        </footer>

        <!-- Toast Notifications -->
        <x-toast />

        <!-- Modal Panduan & Pemicu Shortcut PWA (O8) -->
        <x-pwa-install-banner />

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
