<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title') — SIRUKTI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col justify-between p-4 sm:p-6 lg:p-8 selection:bg-primary-light selection:text-primary">

    <!-- Top Minimal Brand -->
    <header class="w-full max-w-7xl mx-auto flex items-center justify-between py-2">
        <a href="/" class="focus:outline-none focus:ring-2 focus:ring-primary rounded-xl transition">
            <x-application-logo />
        </a>
        <a href="/" class="text-xs sm:text-sm font-semibold text-primary hover:underline">
            Beranda SIRUKTI
        </a>
    </header>

    <!-- Main Error Content (Layar E1) -->
    <main class="w-full max-w-2xl mx-auto my-auto flex flex-col items-center justify-center text-center relative py-8">

        <!-- Watermark Number in background -->
        <div class="text-7xl sm:text-9xl font-extrabold text-border-soft/60 tracking-widest select-none pointer-events-none -mb-10 sm:-mb-14">
            @yield('code')
        </div>

        <div class="relative z-10 bg-surface rounded-card p-6 sm:p-10 shadow-card border border-border-soft flex flex-col items-center w-full">
            <!-- Icon Box -->
            <div class="w-16 h-16 rounded-2xl bg-canvas flex items-center justify-center text-primary mb-5 shadow-sm border border-border-soft">
                @yield('icon')
            </div>

            <!-- Status Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-canvas text-ink-muted text-xs font-semibold mb-3 border border-border-soft">
                <span class="w-2 h-2 rounded-full @yield('dot-color', 'bg-primary')"></span>
                <span>@yield('status-badge')</span>
            </div>

            <!-- Title & Description -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight mb-2.5">
                @yield('heading')
            </h1>
            <p class="text-sm sm:text-base text-ink-muted leading-relaxed max-w-lg mb-8">
                @yield('message')
            </p>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                <a href="/" class="min-h-[48px] px-6 rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm inline-flex items-center justify-center w-full sm:w-auto transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Kembali ke Beranda
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="min-h-[48px] px-6 rounded-btn border border-border-soft bg-canvas hover:bg-surface text-ink font-semibold text-sm inline-flex items-center justify-center w-full sm:w-auto transition">
                    Hubungi Bantuan
                </a>
            </div>
        </div>
    </main>

    <!-- Footer: Institutional Registry -->
    <footer class="w-full text-center max-w-lg mx-auto py-4 space-y-1 text-xs text-ink-muted">
        <p>Pemerintah Kampung Rukti Harjo • Seputih Raman, Lampung Tengah</p>
        <p>Sistem Informasi Pelayanan Kampung Terpadu (SIRUKTI)</p>
    </footer>

</body>
</html>
